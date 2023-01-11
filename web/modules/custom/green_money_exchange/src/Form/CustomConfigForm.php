<?php

namespace Drupal\green_money_exchange\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CustomConfigForm.
 */
class CustomConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'green_money_exchange.customconfig',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'green_money_exchange_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('green_money_exchange.customconfig');

    $form['analytics'] = [
      '#type' => 'details',
      '#title' => $this->t('Money Exchange'),
      '#open' => TRUE,
    ];
    $form['settings']['uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Money Exchange service URI'),
      '#default_value' => $config->get('uri'),
      '#maxlength' => NULL,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('green_money_exchange.customconfig')
      ->set('uri', $form_state->getValue('uri'))
      ->save();
  }

}
