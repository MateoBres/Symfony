<?php

namespace App\Service\AdminFlock;

use App\DBAL\Types\ExcelDataType;
use App\Service\SettingsFlock\SettingsManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Yaml\Yaml;

abstract class BaseDataImporter
{
    public const SUPPORTING_TABLE_NAME = 'excel_data_import';

    protected $abortOnRowNotProcessed = false; // if true, a false returned by processRow will force the import to abort

    protected $flushInterval = 0;

    protected $parameters = [];

    /** @var EntityManagerInterface */
    protected $em;

    /** @var LoggerInterface */
    protected $logger;

    /** @var TokenStorageInterface */
    protected $tokenStorage;

    protected $additionalInfo;

    protected $conn;
    private $excelReader;
    private $uploadedFile;
    private $activeSheet;
    private $columnLetters;
    private $numRows;
    private $lastColumnLetter;
    private $pointer = 1;
    private $importId = null;
    private $fieldList = [];


    public function __construct()
    {
        $this->excelReader = new Xlsx();
        $this->excelReader->setReadDataOnly(true);
        $this->columnLetters = Utility::getExcelColumnsList();
        $this->buildFieldList();
    }

    /**
     * @return array
     */
    public function getFieldList(): array
    {
        return $this->fieldList;
    }

    /**
     * @param EntityManagerInterface $em
     * @required
     */
    public function setEntityManager(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->conn = $em->getConnection();
    }

    /**
     * @param LoggerInterface $logger
     * @required
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param TokenStorageInterface $tokenStorage
     * @required
     */
    public function setTokenStorage(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }


    /**
     * Called by Controllers
     *
     * @param Request $request
     * @return JsonResponse|null
     */
    final public function import(Request $request): ?JsonResponse
    {
        $formData = $request->request->get('data_import_type');
        $files = $request->files->get('data_import_type');
        $this->uploadedFile = $files['excelFile'];

        $this->additionalInfo = json_decode($formData['additionalInfo']);
        $this->parameters = isset($formData['params']) ? (object)json_decode($formData['params']) : (object)[];
        $parametersValidationResponse = $this->validateParameters($this->parameters);

        if (!(@$parametersValidationResponse['valid'] == true)) {
            return $this->badImportResponse($parametersValidationResponse['msg']);
        }

        if (!$this->preImport()) {
            return $this->badImportResponse('Impossibile inizializzare l\'importazione');
        }

        if ($this->uploadedFile) {
            if (!$this->readSpreadsheet()) {
                return $this->badImportResponse('Il file caricato non è leggibile. Carica un file excel!');
            }

            if (isset($formData['validated'])) {
                try {
                    $this->importId = $formData['importId'];
                    session_write_close();
                    $processRowsResult = $this->processRows();
                    if ($processRowsResult['valid']) {
                        sleep(3); // Don't delete this sleep. It's useful for the ajax request to read the progress before getting deleted the record from db;
                        $this->removeImportId();
                        $this->postImport();
                        return new JsonResponse(['completed' => true]);
                    } else {
                        $this->removeImportId();
                        return $this->badImportResponse($processRowsResult['msg']);
                    }
                } catch (Exception $e) {
                    return $this->badImportResponse("Errore durante l'import: " . $e->getMessage());
                }
            } else {
                $validationInfo = $this->validateData();
                if ($validationInfo['valid']) {
                    $importId = $this->recordImportInDB();
                    if ($importId) {
                        return new JsonResponse([
                            'valid' => true,
                            'importId' => $importId
                        ]);
                    } else {
                        // controlla se esiste la tabella "excel_data_import". Se no lancia il commando "sinervis:excel-data-import-table-create"
                        return $this->badImportResponse('Inizializzazione import fallita. Avvisare reparto tecnico');
                    }
                } else {
                    return $this->badImportResponse($validationInfo['msg']);
                }
            }
        } else {
            return $this->badImportResponse('Nessun file selezionato.');
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    final private function processRows(): array
    {
        $count = 0;
        $processed = 0;
        while ($rawRow = $this->currentRow()) {

            // exit at first empty line (excel EOF)
            if ($this->isRowEmpty($rawRow)) {
                break;
            }
            $row = $this->convertRow($rawRow);

            $count++;
            try {
                if ($this->processRow($row, $count)) {
                    $processed++;
                    if (!$this->abortOnRowNotProcessed && $this->flushInterval > 0 && $count > 0 && $count % $this->flushInterval === 0) {
                        $this->em->flush();
                    }
                    $this->updateProgress($count);
                } else if ($this->abortOnRowNotProcessed) {
                    return $this->invalidResult('Errore di importazione!');
                }
            } catch (Exception $e) {
                return $this->invalidResult('Errore di importazione: [' .$e->getCode() .'] '.  $e->getMessage() . ' in file ' . $e->getFile() . ', Line ' . $e->getLine());
            }
        }

        if (!$this->preFlush()) {
            return $this->invalidResult('Errore di importazione (preFlush non superato)');
        }

        if ($processed > 0) {
            $this->em->flush();
        }

        $this->pointer = 1;

        return $this->validResult();
    }

    /**
     * @param array $rawRow
     * @return object
     */
    final private function convertRow(array $rawRow): object
    {
        $this->cleanData($rawRow);
        $row = [];

        foreach ($rawRow as $column => $value) {
            $field = $this->fieldList[$column];

            if (isset($field['type']) && $field['type'] != '') {
                $convertedValue = null;
                switch ($field['type']) {
                    case ExcelDataType::NUMBER:
                        $convertedValue = Utility::numval($value);
                        break;
                    case ExcelDataType::DATE:
                        $convertedValue = Utility::convertExcelDate($value);
                        break;
                    case ExcelDataType::TIME:
                        $convertedValue = Utility::floorDateToMinutes(Utility::convertExcelTime($value));
                        break;
                    case ExcelDataType::DATETIME:
                        $convertedValue = Utility::convertExcelDatetime($value);
                        break;
                    case ExcelDataType::LIST:
                        $convertedValue = Utility::listToArray($value);
                        break;
                    case ExcelDataType::BOOLEAN:
                        $value = strtoupper(trim($value));
                        $convertedValue = in_array($value, ['VERO', 'TRUE', 'S', 'S', 'Y', 'SÌ', 'SI', 'X', 'OK']);
                        break;
                    case ExcelDataType::TEXT:
                    default:
                        $convertedValue = trim($value);
                }

                $row[$field['name']] = $convertedValue;
            } else {
                $row[$field['name']] = trim($value);
            }
        }

        return (object)$row;
    }

    /**
     * @param string $msg
     * @return JsonResponse
     */
    final private function badImportResponse(string $msg)
    {
        return new JsonResponse($this->invalidResult($msg));
    }

    /**
     * @return bool
     */
    final private function readSpreadsheet(): bool
    {
        try {
            $spreadsheet = $this->excelReader->load($this->uploadedFile);
            $this->activeSheet = $spreadsheet->getActiveSheet();
            $this->numRows = $this->activeSheet->getHighestRow() - 1;
            $this->lastColumnLetter = $this->columnLetters[count($this->fieldList) - 1];
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    final private function removeImportId()
    {
        $this->conn->delete(self::SUPPORTING_TABLE_NAME, ['id' => $this->importId]);
    }

    /**
     * @return int
     */
    final private function recordImportInDB(): int
    {
        $values = array('imported_records' => 0, 'total_records' => $this->numRows);
        if ($this->conn->insert('excel_data_import', $values)) {
            return $this->conn->lastInsertId();
        }

        return 0;
    }

    final private function currentRow()
    {
        if ($this->pointer <= $this->numRows) {
            $this->pointer++;
            return $this->activeSheet->rangeToArray(
                "A{$this->pointer}:{$this->lastColumnLetter}{$this->pointer}",     // The worksheet range that we want to retrieve
                null, // Value that should be returned for empty cells
                true, // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
                true, // Should values be formatted (the equivalent of getFormattedValue() for each cell)
                true // Should the array be indexed by cell row and cell column
            )[$this->pointer];
        }

        return;
    }

    final private function isRowEmpty($rawRow)
    {
        foreach ($rawRow as $value) {
            if (trim($value)) {
                return false;
            }
        }

        return true;
    }

    final private function validateValue($value, $field): array
    {
        $colLetter = $field['column'];
        if (isset($field['type']) && $field['type'] != '') {
            switch ($field['type']) {
                case ExcelDataType::NUMBER:
                    if (!is_numeric($value)) {
                        return $this->invalidResult('Il valore in riga <b>' . $this->pointer . '</b> colonna <b>' . $colLetter . '</b> (' . $field['label'] . ') deve contenere un valore numerico.');
                    }
                    break;
                case ExcelDataType::DATE:
                case ExcelDataType::TIME:
                    if (!Utility::isTimestamp(Utility::excelTimestampToUnix(intval($value)))) {
                        return $this->invalidResult('Il valore in riga <b>' . $this->pointer . '</b> colonna <b>' . $colLetter . '</b> (' . $field['label'] . ') deve contenere un valore data/time.');
                    }
                    break;
                case ExcelDataType::BOOLEAN:
                    $value = strtoupper(trim($value));
                    if (!in_array($value, ['VERO', 'TRUE', 'S', 'Y', 'SÌ', 'SI', 'YES', 'X', 'OK', 'FALSO', 'FALSE', 'KO', 'N', 'NO', ''])) {
                        return $this->invalidResult('Il valore in riga <b>' . $this->pointer . '</b> colonna <b>' . $colLetter . '</b> (' . $field['label'] . ') deve contenere S, N o nulla.');
                    }
                    break;
                case ExcelDataType::TEXT:
                default:
                    return $this->validResult();
            }
        }
        return $this->validResult();
    }

    /*
     * This method Validate all data before import
     * If an error is found the process is aborted before import
     */
    final private function validateData(): array
    {
        $this->preValidate();

        $numCols = count($this->fieldList);
        $valid = true;
        $errorMsg = '';
        $colLetter = '';

        while ($rawRow = $this->currentRow()) {

            // exit at first empty line (excel EOF)
            if ($this->isRowEmpty($rawRow)) {
                $valid = true;
                $this->numRows = $this->pointer - 2;
                break;
            }

            // validate cells
            for ($col = 1; $col <= $numCols; $col++) {
//                $value = $this->activeSheet->getCellByColumnAndRow($col, $this->pointer)->getValue();
                $colLetter = $this->columnLetters[$col - 1];
                $value = $rawRow[$colLetter];
                $field = $this->fieldList[$colLetter];
                if ((is_null($value) || empty($value)) && ($field['required'] ?? false)) {
                    $valid = false;
                    $errorMsg = 'Valore obbligatorio non presente in riga <b>' . $this->pointer . '</b> colonna <b>' . $colLetter . '</b> (' . $field['label'] . ')';
                    break 2;
                }

                if (is_null($value) && !empty($value)) {
                    $validateValueResult = $this->validateValue($value, $field);
                    if (!$validateValueResult['valid']) {
                        $valid = $validateValueResult['valid'];;
                        $errorMsg = $validateValueResult['msg'];
                        break 2;
                    }
                }
            }

            // validate converted row
            $row = $this->convertRow($rawRow);
            $rowValidationResponse = $this->validateRow($row);
            if (!$rowValidationResponse['valid']) {
                $valid = false;
                $colLetter = '';
                $errorMsg = 'Riga N° ' . $this->pointer . ' non valida. ' . $rowValidationResponse['msg'];
                break;
            }
        }

        $this->postValidate();

        return [
            'valid' => $valid,
            'error_line' => $this->pointer,
            'error_column' => $colLetter,
            'msg' => $errorMsg
        ];
    }

    final private function resetPointer(): void
    {
        $this->pointer = 2;
    }

    final private function cleanData(array &$data)
    {
        foreach ($data as $key => $value) {
            $data[$key] = trim($value);
        }
    }

    /**
     * @return array
     * @throws ReflectionException
     * @throws \Symfony\Component\Form\Exception\InvalidConfigurationException
     */
    final private function getConfiguration(): array
    {
        $reflector = new ReflectionClass($this);
        $config_file = str_replace(DIRECTORY_SEPARATOR, '/', $reflector->getFileName());
        $config_file = str_replace('src/Service', 'src/Resources/config', $config_file);
        $config_file = str_replace('.php', '.yaml', $config_file);
        $config_file = str_replace('/', DIRECTORY_SEPARATOR, $config_file);

        if (is_file($config_file)) {
            $config = Yaml::parse(file_get_contents($config_file));
        } else {
            throw new InvalidConfigurationException('Data Importer Configuration not found: ' . $config_file);
        }

        if (!isset($config['columns'])) {
            throw new InvalidConfigurationException('Invalid Data Importer Configuration: ' . $config_file);
        }

        return $config;
    }

    final private function buildFieldList()
    {
        $this->fieldList = [];

        try {
            $config = $this->getConfiguration();
            $n = 0;
            foreach ($config['columns'] as $key => $field) {
                if (isset($field['required'])) {
                    $field['required'] = $field['required'] == 'true';
                }
                $this->fieldList[$this->columnLetters[$n]] = array_merge($field, [
                    'name' => $key,
                    'column' => $this->columnLetters[$n],
                ]);
                $n++;
            }
        } catch (ReflectionException $e) {
            throw new \Symfony\Component\Form\Exception\InvalidConfigurationException('Could not read import configuration');
        }
    }

    /*
     * Below methods has to be called or overridden
     * in derived classes
     */

    /**
     * @param string $text
     * @param string $taxonomyClass
     * @return object[]
     */
    final protected function matchTaxonomy(string $text, string $taxonomyClass)
    {
        $taxonomyRepository = $this->em->getRepository($taxonomyClass);
        return $taxonomyRepository->findOneBy([
            'term' => $text,
        ]);
    }

    /**
     * @param $count
     * @param array $message
     */
    final private function updateProgress($count, array $message = [])
    {
        $messages = $this->conn->fetchColumn("SELECT message FROM " . self::SUPPORTING_TABLE_NAME . " WHERE id = ?", [$this->importId]);
        $messages = empty($messages) ? [] : json_decode($messages);

        if (!empty($message)) {
            $messages[] = $message;
            $values = [$count, json_encode($messages), $this->importId];
            $this->conn->executeUpdate("UPDATE " . self::SUPPORTING_TABLE_NAME . " SET imported_records = ?, message = ? WHERE id = ?", $values);
        } else {
            $this->conn->executeUpdate("UPDATE " . self::SUPPORTING_TABLE_NAME . " SET imported_records = ? WHERE id = ?", [$count, $this->importId]);
        }
    }

    final protected function addWarning(int $count, string $message)
    {
        $this->updateProgress($count, ['type' => 'warning', 'message' => 'Riga ' . ($count + 1) . ': ' . trim($message)]);
    }

    final protected function addError(int $count, string $message)
    {
        $this->updateProgress($count, ['type' => 'danger', 'message' => 'Riga ' . ($count + 1) . ': ' . trim($message)]);
    }

    final protected function addInfo(int $count, string $message)
    {
        $this->updateProgress($count, ['type' => 'info', 'message' => 'Riga ' . ($count + 1) . ': ' . trim($message)]);
    }

    final protected function validResult(string $message = ''): array
    {
        return ['valid' => true, 'msg' => $message];
    }

    final protected function invalidResult(string $message = ''): array
    {
        return ['valid' => false, 'msg' => $message];
    }

    /*
     * MUST be overridden in derived classes
     * use updateProgress
     * if $abortOnRowNotProcessed is true, will stop the import on return false
     */
    abstract protected function processRow(object $row, int $count): bool;

    /*
     * validate row values
     * error found here will prevent the import process
     * to be overridden in derived classes
     */
    protected function validateRow(object $row): array
    {
        return $this->validResult();
    }

    /*
     * Validate parameters of the ajax request
     * error found here will prevent the import process
     * to be overridden in derived classes
     */
    protected function validateParameters(object $parameters): array
    {
        return $this->validResult();
    }

    /*
     * to be overridden in derived classes
     */
    protected function preImport()
    {
        return true;
    }

    /*
     * to be overridden in derived classes
     */
    protected function postImport()
    {
        return true;
    }

    /*
     * to be overridden in derived classes
     */
    protected function preFlush(): bool
    {
        return true;
    }

    /*
     * to be overridden in derived classes
     */
    protected function preValidate(): bool
    {
        return true;
    }

    /*
     * to be overridden in derived classes
     */
    protected function postValidate(): bool
    {
        return true;
    }
}
