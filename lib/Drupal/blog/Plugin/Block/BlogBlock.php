<?php

/**
 * @file
 * Contains \Drupal\blog\Plugin\Block\BlogBlock                 .
 */

namespace Drupal\blog\Plugin\Block;
use Drupal\block\BlockBase;

/**
 * Provides a latest blog posts block.
 *
 * @Block(
 *   id = "blog_block",
 *   admin_label = @Translation("Recent blog posts"),
 *   category = @Translation("Blog")
 * )
 */
class BlogBlock extends BlockBase {

  /**
   * Overrides \Drupal\block\BlockBase::defaultConfiguration().
   */
  public function defaultConfiguration() {
    return array(
      'blog_block_count' => 10,
    );
  }

  /**
   * Overrides \Drupal\block\BlockBase::blockSubmit().
   */
  public function blockSubmit($form, &$form_state) {
    $this->configuration['blog_block_count'] = $form_state['values']['blog_block_count'];
  }

  /**
   * Overrides \Drupal\block\BlockBase::blockForm().
   */
  public function blockForm($form, &$form_state) {
    $options = array(2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 25, 30);

    $form['blog_block_count'] = array(
      '#type' => 'select',
      '#title' => t('Block count'),
      '#description' => t('Number of recent blog posts to display'),
      '#default_value' => $this->configuration['blog_block_count'],
      '#options' => array_combine($options, $options),
    );
    return $form;
  }

  /**
   * Build the content for blog block.
   */
  public function build() {

    $block = array();

    //@todo corrently this block will dispaly to everybody, fix the access check.
    if (True) {
    //if (\Drupal\Core\Session\AccountInterface::hasPermission('access content')) {
      $result = db_select('node_field_data', 'n')
        ->fields('n', array('nid', 'title', 'created'))
        ->condition('type', 'blog')
        ->condition('status', 1)
        ->orderBy('created', 'DESC')
        ->range(0, $this->configuration['blog_block_count'])
        ->addTag('node_access')
        ->execute();

      if ($node_title_list = node_title_list($result)) {
        $block['content']['blog_list'] = $node_title_list;
        $block['content']['blog_more'] = array(
          '#theme' => 'more_link',
          '#url' => 'blog',
          '#title' => t('Read the latest blog entries.'),
        );

        return $block;
      }
    }

    return "";
  }
}

