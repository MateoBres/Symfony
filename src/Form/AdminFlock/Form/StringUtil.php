<?php

namespace App\Form\AdminFlock\Form;

class StringUtil
{
    /**
     * Converts a fully-qualified class name to a block prefix.
     *
     * @return string|null The block prefix or null if not a valid FQCN
     */
    public static function fqcnToBlockPrefix($fqcn)
    {
        if (preg_match('~([^\\\\]+?)(type)?$~i', $fqcn, $matches)) {
            $entityName = strtolower(preg_replace(['/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'], ['\\1_\\2', '\\1_\\2'],
                $matches[1]));
            $cleanedNamespaceArray = explode('\\', explode('\\', $fqcn, 3)[2], -1);
            $cleanedNamespaceArray[] = $entityName;
            return strtolower(implode('_', $cleanedNamespaceArray));
        }
    }
}