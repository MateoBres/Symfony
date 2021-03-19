<?php


namespace Sinervis\FileUploaderBundle\Service;

use Sinervis\FileUploaderBundle\Annotations\SinervisUploadableField;
use Sinervis\FileUploaderBundle\Util\MetadataReader;
use Sinervis\FileUploaderBundle\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

class SinervisUploaderHelper
{
    private $metadataReader;
    private $pathToProjectDir;
    private $em;
    private $propertyMetaData;
    private $errorMsgs;

    public function __construct(EntityManagerInterface $em,MetadataReader $metadataReader, string $pathToProjectDir)
    {
        $this->metadataReader = $metadataReader;
        $this->pathToProjectDir = $pathToProjectDir;
        $this->em = $em;
        $this->errorMsgs = [];
    }

    public function handleFileUpload(UploadedFile $uploadedFile, string $dataClass, string $propertyName)
    {
        try {
            $this->propertyMetaData = $this->metadataReader->getUploadableFieldPropertyAnnotation($dataClass, $propertyName, SinervisUploadableField::class);
            $this->removeGarbageFiles();

            $valid = $this->isValid($uploadedFile);
            if ($valid) {
                $destination = $this->getDestination($dataClass, $propertyName);
                $originalFileName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $originalFileExt = $uploadedFile->guessExtension();
                $newFileName = Urlizer::urlize($originalFileName) . '-' . uniqid('', true) . '.' . $originalFileExt;

                $file = $uploadedFile->move($destination, $newFileName);

                if ($file) {
                    $this->recordFile($newFileName, $destination);

                    return new JsonResponse([
                        'name' => $newFileName,
                        'originalName' => $uploadedFile->getClientOriginalName(),
                        'mimeType' => $originalFileExt,
                        'size' => $file->getSize(),
                        'uri' => $destination,
                    ]);
                }

                return new JsonResponse(['error' => 'File move failed!'], 404);
            } else {
                return new JsonResponse(['error' => $this->errorMsgs], 404);
            }
        } catch (\Exception $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 404);
        }
    }

    private function getDestination(string $dataClass, string $propertyName): string
    {
        $destination = $this->propertyMetaData->getDestination();
        $fullPath = $this->pathToProjectDir . '/' . $destination;
        $fullPath = str_replace('//', '/', $fullPath);

        return $fullPath;
    }

    private function recordFile($newFileName, $destination): void
    {
        $sql = "INSERT INTO sv_tmp_file (file_name, uri, created_at) VALUES (:fileName, :uri, :createdAt)";
        $params = [
            'fileName' => $newFileName,
            'uri' => $destination,
            'createdAt' => date('Y-m-d H:i:s')
        ];

        $conn = $this->em->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
    }

    private function isValid(UploadedFile $uploadedFile): bool
    {
        $valid = true;

        $allowedMimeTypes = $this->propertyMetaData->getAllowedMimeTypes();
        if (!empty($allowedMimeTypes)) {
            $fileExt = $uploadedFile->guessExtension();
            if (in_array($fileExt, $allowedMimeTypes, true) === false) {
                $valid = false;
                $this->errorMsgs[] = "Si può caricare soloi i file con le seguenti estenzioni: " . implode(', ', $allowedMimeTypes);
            }
        }

        $maxSize = (float) $this->propertyMetaData->getMaxFileSize();
        if (!empty($maxSize)) {
            $sizeInMB = $uploadedFile->getSize() * 0.000001;
            if ($sizeInMB > $maxSize) {
                $valid = false;
                $this->errorMsgs[] = "La dimensione massima {$maxSize} MB superata";
            }
        }

        return $valid;
    }

    private function removeGarbageFiles(): void
    {
        $currentDateTime = new \DateTime();
        $currentDateTime->modify('-1 day');

        $sql = "SELECT * FROM sv_tmp_file WHERE created_at < :createdAt";
        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->execute(['createdAt' => $currentDateTime->format('Y-m-d H:i:s')]);

        while ($rec = $stmt->fetch()) {
            $fullFileName = $rec['uri'] . '/' . $rec['file_name'];
            if (file_exists($fullFileName)) {
                unlink($fullFileName);
            }
        }
    }
}