<?php

namespace Drupal\blog\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'SearchBlog' block.
 *
 * @Block(
 *  id = "search_blog",
 *  admin_label = @Translation("Search blog"),
 * )
 */
class SearchBlog extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#attached']['library'][] = 'blog/hide_views_exposed_form';

    $build['#theme'] = 'search_blog';

    // attach form for ajax functionality.
    $build['form'] = \Drupal::formBuilder()->getForm(\Drupal\blog\Form\SearchBlog::class);

    return $build;
  }

}
