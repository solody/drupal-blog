<?php

/**
 * @file
 * Contains \Drupal\book\BlogLister.
 */

namespace Drupal\blog;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\UserInterface;
use Drupal\Component\Utility\Xss;

/**
 * Defines a blog lister.
 */
class BlogLister implements BlogListerInterface {

  /**
   * Config Factory Service Object.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * Constructs a BookManager object.
   */
  public function __construct(AccountInterface $account, ConfigFactoryInterface $config_factory) {
    $this->account = $account;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function allBlogPosts() {
   
    $build = array();

    $query = db_select('node_field_data', 'n');
    $query->addTag('node_access');
    $query->condition('type','blog');
    $query->condition('status',1);
    $count_query = clone $query;
    $count_query->addExpression('Count(n.nid)');

    $paged_query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender');
    $paged_query->limit($this->configFactory->get('node.settings')->get('items_per_page'));
    $paged_query->setCountQuery($count_query);
    $nids = $paged_query
      ->fields('n', array('nid', 'sticky', 'created'))
      ->orderBy('sticky', 'DESC')
      ->orderBy('created', 'DESC')
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

    drupal_add_feed('/blog/feed', t('RSS - blogs'));

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function allBlogPostsRss() {
    $nids = db_select('node_field_data', 'n')
      ->fields('n', array('nid', 'created'))
      ->condition('type', 'blog')
      ->condition('status', 1)
      ->orderBy('created', 'DESC')
      ->range(0,$this->configFactory->get('node.settings')->get('items_per_page'))
      ->addTag('node_access')
      ->execute()
      ->fetchCol();

    $channel['title'] = t('!site_name blogs', array('!site_name' => $this->configFactory->get('system.site')->get('name')));
    //TODO _url is a deprecated replacement for url(), need to update with urls derived from routes.
    $channel['link'] = _url('blog', array('absolute' => TRUE));

    return node_feed($nids, $channel);
  }

  /**
   * {@inheritdoc}
   */
  public function userBlogPosts(UserInterface $user) {
    $build = array();

    $query = db_select('node_field_data', 'n');
    $query->addTag('node_access');
    $query->condition('type','blog');
    $query->condition('status',1);
    $query->condition('uid', $user->id());

    $count_query = clone $query;
    $count_query->addExpression('Count(n.nid)');

    $paged_query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender');
    $paged_query->limit($this->configFactory->get('node.settings')->get('items_per_page'));
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
      if ($this->account->id() == $user->id()) {
        drupal_set_message(t('You have not created any blog entries.'));
      }
      else {
        drupal_set_message(t('!author has not created any blog entries.', array('!author' => $user->getUsername())));
      }
    }
    drupal_add_feed('/blog/' . $user->id() . '/feed', t('RSS - !title', array('!title' => $user->getUsername() . t("'s blog"))));

    return $build;
  }

  /**
   * {@inheritdoc}
   * @TODO fix this, it breaks since the removal of node_feed.
   */
  public function userBlogPostsRss(UserInterface $user) {
    $nids = db_select('node_field_data', 'n')
      ->fields('n', array('nid', 'created'))
      ->condition('type', 'blog')
      ->condition('uid', $user->id())
      ->condition('status', 1)
      ->orderBy('created', 'DESC')
      ->range(0,$this->configFactory->get('node.settings')->get('items_per_page'))
      ->addTag('node_access')
      ->execute()
      ->fetchCol();
    $channel['title'] = t("!name's blog", array('!name' => $user->getUsername()));
    //TODO _url is a deprecated replacement for url(), need to update with urls derived from routes.
    $channel['link'] = _url('blog/' . $user->id(), array('absolute' => TRUE));
    return node_feed($nids, $channel); 
  }

  /**
   * {@inheritdoc}
   *
   * @param UserInterface $user
   *   User object
   *
   * @return String
   *   Title string
   */
  public function userBlogTitle(UserInterface $user) {
    return Xss::filter($user->getUsername()) . "'s blog";
  }

}
