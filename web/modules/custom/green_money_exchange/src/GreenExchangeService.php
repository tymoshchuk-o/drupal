<?php

namespace Drupal\green_money_exchange;

use Drupal\Core\Config\ConfigFactory;
use GuzzleHttp\ClientInterface;


/**
 * Class GreenExchange.
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
   * Configuration Factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Constructor.
   */
  public function __construct(ClientInterface $http_client, ConfigFactoryctory $configFactory) {
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


  public function getExchange() {

    $settings = $this->getExchangeSetting();

    if (!$settings['request'] || !$settings['uri']) {
      return;
    }

    try {
      $response = $this->http_client->get($settings['uri']);
      $data = json_decode($response->getBody());
    } catch (RequestException $e) {
      watchdog_exception('green_money_exchange', $e->getMessage());
    }

    return $data;

  }

}
