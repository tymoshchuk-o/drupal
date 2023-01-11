<?php
/**
 * @file
 * Contains Drupal\green_money_exchange\Plugin\Block\GreenExchange.
 *
 */
namespace Drupal\green_money_exchange\Plugin\Block;

use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'GreenExchange' Block.
 *
 * @Block(
 *   id = "green_exchange_block",
 *   admin_label = @Translation("Green Excange block"),
 *   category = @Translation("Excange block"),
 * )
 */
class GreenExchange extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build()
  {

    $currencsData = $this->getExchange();

    $renderArr =  [
      '#theme' => 'green_exchange_template',
      '#exchange_var' => $currencsData,
    ];

    return $renderArr;
  }


  public function getExchange() {
    $uri = "https://bank.gov.ua/NBUStatService/v1/statdirectory/exchangenew?json";
    $client = \Drupal::httpClient();

    try {
      $response = $client->get($uri);
      $data = json_decode($response->getBody());
    }
    catch (RequestException $e) {
      watchdog_exception('green_money_exchange', $e->getMessage());
    }

    return $data;

  }

}


