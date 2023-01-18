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
   * Create config form from Green Exchange Block.
   *
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
    $currencyList = $this->exchangeService->getCurrencyList();

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
    $form['settings']['range'] = [
      '#type' => 'number',
      '#title' => $this->t('Exchange rate for the period in days'),
      '#default_value' => $config->get('range') ?? 1,
    ];
    $form['settings']['uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Money Exchange service URI'),
      '#default_value' => $config->get('uri') ?? ' ',
      '#maxlength' => NULL,
    ];

    if (count($currencyList)) {
      $form['currency-list'] = [
        '#type' => 'details',
        '#title' => $this->t('Select currencies to display'),
        '#open' => TRUE,
      ];
      $form['currency-list']['currency-item'] = [
        '#type' => 'checkboxes',
        '#options' => $currencyList,
        '#title' => $this->t('What standardized tests did you take?'),
        '#default_value' => $config->get('currency-item') ?? [],
      ];

    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $isRemovedCurrency = $this->exchangeService->checkCurrencyList();

    $this->config('green_money_exchange.customconfig')
      ->set('request', $form_state->getValue('request'))
      ->set('range', $form_state->getValue('range'))
      ->set('uri', trim($form_state->getValue('uri')))
      ->set('currency-item', $form_state->getValue('currency-item'))
      ->save();

    if (count($isRemovedCurrency) > 0) {
      foreach ($isRemovedCurrency as $deletedCurrency) {
        $this->messenger->addWarning('Currency is deleted ' . $deletedCurrency);
      }
    }

  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $isUriError = $this->exchangeService->isValidUri($form_state->getValue('uri'));

    if (!$isUriError['isValid']) {
      $form_state
        ->setErrorByName('uri', $this
          ->t((string) $isUriError['error']));
    }

    if ($form_state->getValue('range') < 0) {
      $form_state->setErrorByName('range', $this
        ->t('Exchange rate for the period in
        days must be greater than or equal to 1'));
    }
  }

}
