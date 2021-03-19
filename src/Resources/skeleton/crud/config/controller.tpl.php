singular_name:   <?= ucfirst($entity_twig_var_singular) . "\n" ?>
plural_name:     <?= ucfirst($entity_twig_var_plural) . "\n" ?>

list_fields:
<?php foreach ($entity_fields as $field): ?>
  - <?= $field['fieldName'] . "\n" ?>
<?php endforeach; ?>


blocks:
  row1:
    col1:
      size: 7
      blocks:
        entity:
          title:  Dettagli
          icon:   fa-table
          fields:
<?php foreach ($entity_fields as $field): ?>
            - <?= $field['fieldName'] . "\n" ?>
<?php endforeach; ?>


fields_map:
<?php foreach ($entity_fields as $field): ?>
  <?= $field['fieldName'] . ":\n" ?>
    label:        <?= ucfirst($field['fieldName']) . "\n" ?>
    type:         <?= $field['type'] . "\n" ?>
    sort_by:      <?= $sortby_field_prefix.'.'.$field['fieldName'] . "\n" ?>
    search_alias: <?= $sortby_field_prefix.'.'.$field['fieldName'] . "\n" ?>
<?php endforeach; ?>