<?php
/**
 * @file
 * Contains \Drupal\article\Plugin\Block\ArticleBlock.
 */

namespace Drupal\article2\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;

/**
 * Provides a 'article2' block.
 *
 * @Block(
 *   id = "article2_block",
 *   admin_label = @Translation("Article2 block"),
 *   category = @Translation("Custom article2 block example")
 * )
 */
class Article2Block extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $form = \Drupal::formBuilder()->getForm('\Drupal\hero_search\Form\HeroForm');

    return $form;
   }
}