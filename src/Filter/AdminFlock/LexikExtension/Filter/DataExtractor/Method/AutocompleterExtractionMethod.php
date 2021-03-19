<?php

namespace App\Filter\AdminFlock\LexikExtension\Filter\DataExtractor\Method;

use Symfony\Component\Form\FormInterface;
use Lexik\Bundle\FormFilterBundle\Filter\DataExtractor\Method\DataExtractionMethodInterface;

/**
 * Extract data needed to apply a filter condition.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Gilles Gauthier <g.gauthier@lexik.fr>
 */
class AutocompleterExtractionMethod implements DataExtractionMethodInterface
{
  /**
   * {@inheritdoc}
   */
  public function getName()
  {
      return 'sv_autocompleter';
  }

  /**
   * {@inheritdoc}
   */
  public function extract(FormInterface $form)
  {

    $data   = $form->getData();
    $values = array(
      'value' => NULL,
      'class' => NULL,
    );

    if (array_key_exists('autocomplete_field', $data) && is_object($data['autocomplete_field'])) {
      $reflectionClass   = new \ReflectionClass($data['autocomplete_field']);

      $values = array(
        'value' => trim($data['autocomplete_field']->__toString()),
        'class' => lcfirst($reflectionClass->getShortName()),
      );
    } elseif (isset($data['autocomplete_field'])) {
      if (!isset($data['condition_pattern']) || $data['condition_pattern'] === null) {
        $data['condition_pattern'] = 4;
      }

      $values = array(
        'value' => trim($data['autocomplete_field']),
      );
    }

    $values += $data;

    return $values;
  }
}
