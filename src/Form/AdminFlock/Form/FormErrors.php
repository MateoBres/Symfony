<?php

namespace App\Form\AdminFlock\Form;

use Symfony\Component\Form\Form;

/**
 * Transform form errors object in array items used in form templates
 */
class FormErrors
{
    public static function getArray(Form $form)
    {
        return self::getFormErrors($form);
    }

    public static function getFormErrors(Form $form)
    {
        $errors = array();

        if ($form instanceof Form) {
            // find errors of this element
            foreach ($form->getErrors() as $error) {
                $errors[] = $error->getMessage();
            }

            // iterate over errors of all children
            foreach ($form->all() as $key => $child) {
                if ($child instanceof Form) {
                    /** @var $child \Symfony\Component\Form\Form */
                    $err = self::getFormErrors($child);
                    if (count($err) > 0) {
                        $errors = array_merge($errors, $err);
                    }
                }
            }
        }

        return array_unique($errors);
    }
}