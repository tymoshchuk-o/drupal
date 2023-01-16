<?php

namespace Drupal\green_money_exchange;

use Drupal\Core\Config\ConfigFactoryInterface;
use GuzzleHttp\ClientInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class GreenExchange.
 *
 * @package Drupal\green_money_exchange
 */
class GreenExchangeService {

  use StringTranslationTrait;

  /**
   * The valid keys in HTTP client response.
   *
   * @var string
   */
  protected $validResponseData = ['txt', 'rate', 'cc'];

  /**
   * The HTTP client to fetch the feed data with.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructor.
   */
  public function __construct(ClientInterface $http_client, ConfigFactoryInterface $configFactory) {
    $this->httpClient = $http_client;
    $this->configFactory = $configFactory;
  }

  /**
   * Gets exchange setting.
   */
  public function getExchangeSetting() {
    $config = $this->configFactory->get('green_money_exchange.customconfig');

    return [
      'uri' => $config->get('uri'),
      'request' => $config->get('request'),
      'currency' => $config->get('currency-item'),
    ];
  }

  /**
   * Returns only active currencies in the config form.
   *
   * @param array $currencyData
   *   An array with of currency exchange.
   *
   * @return array
   *   A filtered array with of currency exchange.
   */
  public function filterCurrency(array $currencyData) {
    $allCurrency = $this->getExchangeSetting()['currency'];

    $filtredActiveCurrency = array_filter($allCurrency, function ($item) {
      return $item !== 0;
    });

    $returnCurrencyData = array_filter($currencyData, function ($item) use ($filtredActiveCurrency) {
      foreach ($filtredActiveCurrency as $active) {
        if ($item->cc === $active) {
          return TRUE;
        }

      }

      return FALSE;

    }
    );

    return $returnCurrencyData;

  }

  /**
   * Send GET request to currency server.
   *
   * @return array
   *   An array with of currency exchange.
   */
  public function fetchData($uri) {

    try {
      $response = $this->httpClient->get($uri)->getBody();
      $data = json_decode($response);
    }
    catch (\Exception $e) {
      throw  new \Exception('Server not found');
    }

    return $data;

  }

  /**
   * Send GET request to currency server.
   *
   * @return array|NULL
   *   An array with of currency exchange.
   */
  public function getExchange() :array|NULL {

    $settings = $this->getExchangeSetting();
    $request = $settings['request'];
    $uri = $settings['uri'];

    if (!$request || !$uri) {
      return [];
    }

    try {
      $data = $this->fetchData($uri);
    }
    catch (\Exception $e) {
      return [];
    }

    return $data;

  }

  /**
   * Send GET request to currency server.
   *
   * @return array
   *   An array with of currency name.
   */
  public function getCurrencyList() {
    $currencyData = $this->getExchange();
    $currencyList = [];
    if (count($currencyData) > 0) {
      foreach ($currencyData as $item) {
        $currencyList[$item->cc] = $this->t((string) $item->txt);
      }
    }

    return $currencyList;

  }

  /**
   * Check is valid URI.
   *
   * @param string $uri
   *   Exchange API URI.
   *
   * @return array
   *   An associative array with two keys isValid:boolean, error:string|null.
   */
  public function isValidUri(string $uri) {

    $returnArr = [
      "isValid" => FALSE,
      "error" => NULL,
    ];

    if (trim($uri) == '') {
      $returnArr["error"] = 'The server URI field is empty.';
      return $returnArr;
    }

    try {
      $exchangeData = $this->fetchData($uri);
    } catch (\Exception $e) {
      $returnArr['error'] = 'Server request error.';
      return $returnArr;
    }

    if (!$exchangeData) {
      $returnArr["error"] = 'The exchange server not found.';
      return $returnArr;
    }

    foreach ($exchangeData as $item) {
      foreach ($this->validResponseData as $key) {
        if (!$item->$key) {
          $returnArr['error'] = 'Server response data is invalid';
          return $returnArr;
        }
      }
    }

    $returnArr['isValid'] = TRUE;

    return $returnArr;
  }

}
