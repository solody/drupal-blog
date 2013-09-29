<?php

/**
 * @file
 * Page callback file for the blog module.
 */

/**
 * Menu callback; displays a Drupal page containing recent blog entries of a given user.
 * @todo Provide a Views alternative for when Views are enabled.
 * @todo Refactor this function into a Controller when routing is upgraded for D8.
 */
function blog_page_user($account) {
  global $user;

  //@todo drupal_set_title is deprecated for Drupal 8.
  drupal_set_title($title = t("@name's blog", array('@name' => user_format_name($account))), PASS_THROUGH);

  $build = array();

  $query = db_select('node_field_data', 'n');
  $query->addTag('node_access');
  $query->condition('type','blog');
  $query->condition('status',1);
  $query ->condition('uid', $account->id());
  
  $count_query = clone $query;
  $count_query->addExpression('Count(n.nid)');

  $paged_query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender');
  $paged_query->limit(variable_get('default_nodes_main', 10));
  $paged_query->setCountQuery($count_query);

  $nids = $paged_query
    ->fields('n', array('nid', 'sticky', 'created'))
    ->condition('type', 'blog')
    ->orderBy('sticky', 'DESC')
    ->orderBy('created', 'DESC')
    ->execute()
    ->fetchCol();

  if (!empty($nids)) {
    $nodes = node_load_multiple($nids);
    $build['nodes']= node_view_multiple($nodes);
    $build['pager'] = array(
      '#theme' => 'pager',
      '#weight' => 5,
    );
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
 * @todo Provide a Views alternative when Views are enabled.
 * @todo Refactory this function into a controller when routing is updated for D8.
 */
function blog_page_last() {
  global $user;
  $build = array();

  $query = db_select('node_field_data', 'n');
  $query->addTag('node_access');
  $query->condition('type','blog');
  $query->condition('status',1);

  $count_query = clone $query;
  $count_query->addExpression('Count(n.nid)');
  
  $paged_query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender');
  $paged_query->limit(variable_get('default_nodes_main', 10));
  $paged_query->setCountQuery($count_query);

  $nids = $paged_query
    ->fields('n', array('nid', 'sticky', 'created'))
    ->orderBy('sticky', 'DESC')
    ->orderBy('created', 'DESC')
    ->range(0, variable_get('default_nodes_main', 10))
    ->execute()
    ->fetchCol();

  if (!empty($nids)) {
    $nodes = node_load_multiple($nids);
    $build['nodes'] = node_view_multiple($nodes);
    $build['pager'] = array(
      '#theme' => 'pager',
      '#weight' => 5,
    );
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
