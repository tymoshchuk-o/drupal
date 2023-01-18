<?php

namespace Drupal\green_money_exchange;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use GuzzleHttp\ClientInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class GreenExchangeService.
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
   * The LoggerChannelFactoryInterface.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $errorLog;

  /**
   * Constructor.
   */
  public function __construct(ClientInterface $http_client, ConfigFactoryInterface $configFactory, LoggerChannelFactoryInterface $errorLog) {
    $this->httpClient = $http_client;
    $this->configFactory = $configFactory;
    $this->errorLog = $errorLog;
  }

  /**
   * Log error.
   *
   * @param string $message
   *   Error Message.
   */
  public function logError(string $message): void {
    $this->errorLog->get('green_exchange')->error($this->t($message));
  }

  /**
   * Log notice.
   *
   * @param string $message
   *   Error Message.
   */
  public function logNotice(string $message): void {
    $this->errorLog->get('green_exchange')->notice($this->t($message));
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
   * Returns only active currencies..
   *
   * @return array
   *   An active currency.
   */
  public function activeCurrency(): array {
    $allCurrency = $this->getExchangeSetting()['currency'];
    if ($allCurrency && count($allCurrency) > 0) {
      $filteredActiveCurrency = array_filter($allCurrency, function ($item) {
        return $item !== 0;
      });
    }
    else {
      return [];
    }

    return $filteredActiveCurrency;
  }

  /**
   * If currency list changed Returns an array of deleted currencies.
   *
   * @return array
   *   An associative array with deleted or added currency.
   */
  public function checkCurrencyList(): array {
    $logMessage = 'There are no currency data on the server: ';
    $filteredActiveCurrency = $this->activeCurrency();
    $serverCurrency = $this->getCurrencyList();

    $returnArr = array_filter($filteredActiveCurrency, function ($item) use ($serverCurrency) {
      foreach ($serverCurrency as $currency => $value) {
        if ($currency == $item) {
          return FALSE;
        }
      }

      return TRUE;
    }
    );

    if (count($returnArr) > 0) {
      foreach ($returnArr as $currency) {
        $logMessage .= $currency . '; ';
      }

      $this->logError($this->t($logMessage));

    }

    return $returnArr;

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
  public function filterCurrency(array $currencyData): array {

    $filteredActiveCurrency = $this->activeCurrency();

    if ($currencyData && count($currencyData) > 0) {
      $returnCurrencyData = array_filter($currencyData, function ($item) use ($filteredActiveCurrency) {
        foreach ($filteredActiveCurrency as $active) {
          if ($item->cc === $active) {
            return TRUE;
          }

        }

        return FALSE;

      }
      );
    }
    else {
      return [];
    }

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
   * @return array
   *   An array with of currency exchange.
   */
  public function getExchange(): array {

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
      $this->logError($e->getMessage());
      return [];
    }

    return $data ?? [];

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
    if ($currencyData && count($currencyData) > 0) {
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
      $this->logNotice($returnArr["error"]);
    }
    if (trim($uri) !== '') {
      try {
        $exchangeData = $this->fetchData($uri);
      }
      catch (\Exception $e) {
        $returnArr['error'] = 'Server request error.';
        $this->logError($returnArr["error"]);
        return $returnArr;
      }

      if (!$exchangeData) {
        $returnArr["error"] = 'The exchange server not found.';
        $this->logError($returnArr["error"]);
        return $returnArr;
      }

      foreach ($exchangeData as $item) {
        foreach ($this->validResponseData as $key) {
          if (!$item->$key) {
            $returnArr['error'] = 'Server response data is invalid';
            $this->logError($returnArr["error"]);
            return $returnArr;
          }
        }
      }
    }

    $returnArr['isValid'] = TRUE;

    return $returnArr;
  }

}
