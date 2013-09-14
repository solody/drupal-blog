<?php

/**
 * @file
 * Page callback file for the blog module.
 */

/**
 * Menu callback; displays a Drupal page containing recent blog entries of a given user.
 * @todo Refactor the listing pages to use Views in core.
 */
function blog_page_user($account) {
  global $user;

  drupal_set_title($title = t("@name's blog", array('@name' => user_format_name($account))), PASS_THROUGH);

  $build = array();

  $query = db_select('node_field_data', 'n');
  // This query will limit the number of nodes returned based on the range, note
  // that the original D7 verstion used a limit and added a pager. In the current
  // D8 state the listing pages have no pager.
  $nids = $query
    ->fields('n', array('nid', 'sticky', 'created'))
    ->condition('type', 'blog')
    ->condition('uid', $account->id())
    ->condition('status', 1)
    ->orderBy('sticky', 'DESC')
    ->orderBy('created', 'DESC')
    ->range(0, variable_get('default_nodes_main', 10))
    ->addTag('node_access')
    ->execute()
    ->fetchCol();

  if (!empty($nids)) {
    $nodes = node_load_multiple($nids);
    $build += node_view_multiple($nodes);
  }
  else {
    if ($account->id() == $user->id()) {
      drupal_set_message(t('You have not created any blog entries.'));
    }
    else {
      drupal_set_message(t('!author has not created any blog entries.', array('!author' => theme('username', array('account' => $account)))));
    }
  }
  drupal_add_feed('blog/' . $account->id() . '/feed', t('RSS - !title', array('!title' => $title)));

  return $build;
}

/**
 * Menu callback; displays a Drupal page containing recent blog entries of all users.
 * @todo Refactor the listing pages to use Views in core.
 */
function blog_page_last() {
  global $user;
  $build = array();

  $query = db_select('node_field_data', 'n');
  // This query will limit the number of nodes returned based on the range, note 
  // that the original D7 verstion used a limit and added a pager. In the current
  // D8 state the listing pages have no pager.
  $nids = $query
    ->fields('n', array('nid', 'sticky', 'created'))
    ->condition('type', 'blog')
    ->condition('status', 1)
    ->orderBy('sticky', 'DESC')
    ->orderBy('created', 'DESC')
    ->range(0, variable_get('default_nodes_main', 10))
    ->addTag('node_access')
    ->execute()
    ->fetchCol();

  if (!empty($nids)) {
    $nodes = node_load_multiple($nids);
    $build += node_view_multiple($nodes);
  }
  else {
    drupal_set_message(t('No blog entries have been created.'));
  }
  drupal_add_feed('blog/feed', t('RSS - blogs'));

  return $build;
}

/**
 * Menu callback; displays an RSS feed containing recent blog entries of a given user.
 */
function blog_feed_user($account) {

  $nids = db_select('node_field_data', 'n')
    ->fields('n', array('nid', 'created'))
    ->condition('type', 'blog')
    ->condition('uid', $account->id())
    ->condition('status', 1)
    ->orderBy('created', 'DESC')
    ->range(0, variable_get('feed_default_items', 10))
    ->addTag('node_access')
    ->execute()
    ->fetchCol();

  $channel['title'] = t("!name's blog", array('!name' => user_format_name($account)));
  $channel['link'] = url('blog/' . $account->id(), array('absolute' => TRUE));

  node_feed($nids, $channel);
}

/**
 * Menu callback; displays an RSS feed containing recent blog entries of all users.
 */
function blog_feed_last() {
  $nids = db_select('node_field_data', 'n')
    ->fields('n', array('nid', 'created'))
    ->condition('type', 'blog')
    ->condition('status', 1)
    ->orderBy('created', 'DESC')
    ->range(0, variable_get('feed_default_items', 10))
    ->addTag('node_access')
    ->execute()
    ->fetchCol();

  $channel['title'] = t('!site_name blogs', array('!site_name' => variable_get('site_name', 'Drupal')));
  $channel['link'] = url('blog', array('absolute' => TRUE));

  node_feed($nids, $channel);
}
