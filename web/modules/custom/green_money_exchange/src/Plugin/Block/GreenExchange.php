<?php

namespace Drupal\green_money_exchange\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\green_money_exchange\GreenExchangeService;

/**
 * Provides a 'GreenExchange' Block.
 *
 * @Block(
 *   id = "green_exchange_block",
 *   admin_label = @Translation("Green Excange block"),
 *   category = @Translation("Excange block"),
 * )
 */
class GreenExchange extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Custom exchange service
   *
   * @var Drupal\green_money_exchange\GreenExchangeService $exchangeService
   */
  protected $exchangeService;

  /**
   * Block constructor function
   *
   * @param array $configuration
   *   Configuration array
   * @param string $plugin_id
   *   Plugin id
   * @param mixed $plugin_definition
   *   Plugin definition
   * @param \Drupal\green_money_exchange\GreenExchangeService $exchangeService
   *   Exchange service
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, GreenExchangeService $exchangeService) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->exchangeService = $exchangeService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('green_money_exchange.exchange')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $currencsData = $this->exchangeService->getExchange();

    $renderArr = [
      '#theme' => 'green_exchange_template',
      '#exchange_var' => $currencsData,
    ];

    return $renderArr;
  }

}
