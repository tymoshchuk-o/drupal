<?php

namespace Drupal\green_money_exchange;

use Drupal\Core\Config\ConfigFactoryInterface;
use GuzzleHttp\ClientInterface;

/**
 * Receives exchange rate data from Rest API.
 *
 * @package Drupal\green_money_exchange
 */
class GreenExchangeService {

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
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The Drupal http client.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
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

    return ['uri' => $config->get('uri'), 'request' => $config->get('request')];
  }

  /**
   * Send GET request to currency server.
   *
   * @return array|null
   *   An array with of currency exchange
   */
  public function getExchange() {

    $settings = $this->getExchangeSetting();
    $request = $settings['request'];
    $uri = $settings['uri'];

    if (!$request || !$uri) {
      return;
    }

    try {
      $response = $this->httpClient->get($uri)->getBody();
      $data = json_decode($response);
    }
    catch (RequestException $e) {
      watchdog_exception('green_money_exchange', $e->getMessage());
    }

    return $data;

  }

}
