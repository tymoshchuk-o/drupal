<?php

/**
 * @file
 * Contains \Drupal\green_money_exchange\Entity\Currency.
 */

namespace Drupal\green_money_exchange\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Entity\EntityChangedTrait;

/**
 * Defines the Currency entity.
 *
 * @ingroup currency
 *
 *
 * @ContentEntityType(
 * id = "green_exchange_currency",
 * label = @Translation("Currency entity"),
 * handlers = {
 * "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 * "list_builder" = "Drupal\dictionary\Entity\Controller\TermListBuilder",
 * },
 * list_cache_contexts = { "user" },
 * base_table = "currency_state",
 * admin_permission = "administer currency_state entity",
 * entity_keys = {
 * "id" = "id",
 * "uuid" = "uuid",
 * "exchangedate" = "exchangedate",
 * "cc" = "cc",
 * "txt" = "txt",
 * "rate" = "rate"
 * },
 * )
 */
class Currency extends ContentEntityBase {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Standard field, used as unique if primary index.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Currency entity.'))
      ->setReadOnly(TRUE);

    // Standard field, unique outside of the scope of the current project.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Currency entity.'))
      ->setReadOnly(TRUE);

    // Field for the Currency exchange date.
    $fields['exchangedate'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Exchange Date'))
      ->setDescription(t('Exchange rate date'))
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 255,
        'text_processing' => 0,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -2,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -2,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    //Field CC
    $fields['cc'] = BaseFieldDefinition::create('string')
      ->setLabel(t('cc'))
      ->setDescription(t('Currency short name'))
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 255,
        'text_processing' => 0,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    //Field txt
    $fields['txt'] = BaseFieldDefinition::create('string')
      ->setLabel(t('txt'))
      ->setDescription(t('Currency long name'))
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 255,
        'text_processing' => 0,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -5,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    //Field rate
    $fields['rate'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('rate'))
      ->setDescription(t('Currency current rate'))
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -6,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -6,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
