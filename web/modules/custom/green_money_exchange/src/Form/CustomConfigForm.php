<?php

namespace Drupal\green_money_exchange\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\green_money_exchange\GreenExchangeService;

/**
 * Class CustomConfigForm.
 */
class CustomConfigForm extends ConfigFormBase {

  /**
   * Custom exchange service.
   *
   * @var Drupal\green_money_exchange\GreenExchangeService
   */
  protected $exchangeService;

  /**
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config Factory.
   * @param \Drupal\green_money_exchange\GreenExchangeService $exchangeService
   *   Exchange service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, GreenExchangeService $exchangeService) {
    parent::__construct($config_factory);
    $this->exchangeService = $exchangeService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('green_money_exchange.exchange')
    );
  }


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


    $form['settings'] = [
      '#type' => 'details',
      '#title' => $this->t('Money Exchange'),
      '#open' => TRUE,
    ];
    $form['settings']['request'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Activate request to server'),
      '#default_value' => $config->get('request') ?? FALSE,
    ];
    $form['settings']['uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Money Exchange service URI'),
      '#default_value' => $config->get('uri') ?? ' ',
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
      ->set('uri', trim($form_state->getValue('uri')))
      ->set('request', $form_state->getValue('request'))
      ->save();
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $isUriError = $this->exchangeService->isValidUri($form_state->getValue('uri'));

    if (!$isUriError['isValid']) {
      $form_state
        ->setErrorByName('uri', $this
          ->t($isUriError['error']));
    }
  }

}
