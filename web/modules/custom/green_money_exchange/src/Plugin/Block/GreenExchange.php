<?php

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
  public function build() {
    return [
      '#markup' => $this->t('Hello, World!'),
    ];
  }

}
