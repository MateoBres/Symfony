<?php


namespace App\Service\AdminFlock;


use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Yaml\Yaml;

class EntityLayoutConfigManager
{
    private $entity;
    private $entityLayout;
    private $session;

    /**
     * @var Session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }


    public function setEntity($entity)
    {
        if (is_string($entity)) {
            $this->entity = new $entity();
        } else {
            $this->entity = $entity;
        }

        try {
            $this->loadConfigFile();
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            if (strpos($errorMsg, 'No such file or directory') !== false) {
                $this->session->getFlashBag()->add('error', 'Layout config file non è stato trovato per "'.get_class($this->entity).'"');
            }
        }
        return $this;
    }

    public function getEntityName()
    {
        $reflector = new \ReflectionClass($this->entity);
        return $reflector->getShortName();
    }

    public function getEntityLabel()
    {
        return $this->entityLayout['singular_name'] ?? '';
    }

    public function getBlocks($type = '', $prefix = '')
    {
        if (!empty($prefix)) {
            $prefix .= '.';
        }

        $configBlocks = $this->entityLayout['blocks'] ?? [];
        if (!empty($type) && isset($this->entityLayout[$type.'_blocks'])) {
            $configBlocks = $this->entityLayout[$type.'_blocks'];
        }

        $configBlocks = $configBlocks ?? [];

        foreach ($configBlocks as &$row) {
            foreach($row as &$column) {
                foreach($column['blocks'] as &$block) {
                    foreach($block['fields'] as &$field) {
                        $field = $prefix.$field;
                    }
                }
            }
        }

        return $configBlocks;
    }

    public function getConfig()
    {
        return $this->entityLayout;
    }

    public function getFieldsMap($prefix = '')
    {
        $fieldsMap = $this->entityLayout['fields_map'] ?? [];

        if (empty($fieldsMap)) {
            return [];
        }

        if (!empty($prefix)) {
            $prefix .= '.';
        }

        foreach ($fieldsMap as $fieldName => $fieldInfo) {
            $fieldsMap[$prefix.$fieldName] = $fieldInfo;
        }

        return $fieldsMap;
    }

    private function loadConfigFile()
    {
        $reflector = new \ReflectionClass($this->entity);
        $configFile = str_replace(DIRECTORY_SEPARATOR, '/', $reflector->getFileName());

        $configFile = str_replace($reflector->getShortName().'.php', $reflector->getShortName().'Controller.yaml', $configFile);
        $configFile = str_replace('src/Entity', 'src/Resources/config', $configFile);

        $configFile = str_replace('/', DIRECTORY_SEPARATOR, $configFile);

        if ($configFile) {
            $this->entityLayout = Yaml::parse(file_get_contents($configFile));
            $this->entityLayout += ['original_entity_short_name' => $reflector->getShortName()];
        }

        $pathToConfigFolder = __DIR__.'/../../Resources/config/';
        $this->entityLayout['fields_map'] = $this->attachSubFieldsMaps($this->entityLayout['fields_map'], $pathToConfigFolder);
    }

    private function attachSubFieldsMaps($fields_map, $pathToConfigFolder)
    {
        foreach ($fields_map as $key => $field_map) {
            if (isset($field_map['path_to_config'])) {
                $path_to_config = $pathToConfigFolder . $field_map['path_to_config'];
                $subConfig = Yaml::parse(file_get_contents($path_to_config));
                $fields_map[$key]['fields_map'] = $this->attachSubFieldsMaps($subConfig['fields_map'], $pathToConfigFolder);
            }
        }

        return $fields_map;
    }
}