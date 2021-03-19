<?php

namespace App\Filter\AdminFlock\LexikExtension\Event\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\FormFilterBundle\Event\GetFilterConditionEvent;
use Lexik\Bundle\FormFilterBundle\Filter\FilterOperands;

class AutocompleteFilterSubscriber implements EventSubscriberInterface
{
  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents()
  {
    return array(
      // if a Doctrine\ORM\QueryBuilder is passed to the lexik_form_filter.query_builder_updater service
      'lexik_form_filter.apply.orm.filter_autocomplete' => array('filterAutocomplete'),

      // if a Doctrine\DBAL\Query\QueryBuilder is passed to the lexik_form_filter.query_builder_updater service
      'lexik_form_filter.apply.dbal.filter_autocomplete' => array('filterAutocomplete'),
    );
  }

  /**
   * Apply a filter for a filter_autocomplete type.
   *
   * This method should work whih both ORM and DBAL query builder.
   */
  public function filterAutocomplete(GetFilterConditionEvent $event)
  {
    $qb     = $event->getQueryBuilder();
    $expr   = $event->getFilterQuery()->getExpressionBuilder();
    $values = $event->getValues();

    if (!empty($values['value'])) {
      list($joinAlias, $condition_pattern) = $this->getQueryData($qb, $values);

      $event->setCondition(
        $expr->stringLike($joinAlias, trim($values['value']), $condition_pattern)
      );

    }
  }


  private function getQueryData($qb, $values) {
    $parent     = $values['rootTable'] !== null ? $values['rootTable'] : $qb->getRootAliases()[0];
    $joinAlias = '';

    if (empty($values['joinTables'])) {
      //throw new \Exception('Please pass join table array for "'. ucfirst($values['class']) . '" class');
      $joinAlias = $parent;
    }
    else {
      foreach ($values['joinTables'] as $key => $tableName) {
        $joinAlias = $parent.'_'.$tableName . ($key == 0 ? '_'.uniqid() : '');
        $qb->leftJoin($parent.'.'.$tableName, $joinAlias);
        $parent = $joinAlias;
      }
    }

    return array(
      $joinAlias.'.'.$values['conditionFieldName'],
      $values['condition_pattern'] ? $values['condition_pattern'] : FilterOperands::STRING_EQUALS,
    );
  }
}