<?php
/**
 * @file
 * Contains \Drupal\article\Plugin\Block\ArticleBlock.
 */

namespace Drupal\article3\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;

/**
 * Provides a 'article3' block.
 *
 * @Block(
 *   id = "article3_block",
 *   admin_label = @Translation("article3 block"),
 *   category = @Translation("Custom article3 block example")
 * )
 */
class Article3Block extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $form = \Drupal::formBuilder()->getForm('\Drupal\contact_us\Form\ContactForm');

    return $form;
   }
}