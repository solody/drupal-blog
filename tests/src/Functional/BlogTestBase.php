<?php

namespace Drupal\Tests\blog\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Test base class for blog module.
 *
 * @group blog
 */
abstract class BlogTestBase extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'blog',
  ];

  /**
   * @var \Drupal\node\NodeInterface[]
   */
  protected $blog_nodes1, $blog_nodes2, $article_nodes1, $article_nodes2;

  /**
   * @var \Drupal\user\UserInterface
   */
  protected $blogger1, $blogger2, $blogger_no_entries;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    // add article node type
    $this->createContentType([
      'type' => 'article',
    ]);
    // create blogger1 user
    $this->blogger1 = $this->drupalCreateUser([
      'create article content',
      'create blog_post content',
    ]);
    // create blogger2 user
    $this->blogger2 = $this->drupalCreateUser([
      'create article content',
      'create blog_post content',
    ]);
    // create blogger user with no blog posts
    $this->blogger_no_entries = $this->drupalCreateUser([
      'create blog_post content',
    ]);
    // generate blog posts and articles
    $this->blog_nodes1 = [];
    $this->blog_nodes2 = [];
    $this->article_nodes1 = [];
    $this->article_nodes2 = [];
    for ($i = 0; $i < 10; $i++) {
      $node = $this->createNode([
        'type' => 'blog_post',
        'title' => $this->randomMachineName(32),
        'uid' => ($i % 2) ? $this->blogger1->id() : $this->blogger2->id(),
      ]);
      if ($i % 2) {
        $this->blog_nodes1[$node->id()] = $node;
      }
      else {
        $this->blog_nodes2[$node->id()] = $node;
      }
    }
    for ($i = 0; $i < 10; $i++) {
      $node = $this->createNode([
        'type' => 'article',
        'title' => $this->randomMachineName(32),
        'uid' => ($i % 2) ? $this->blogger1->id() : $this->blogger2->id(),
      ]);
      if ($i % 2) {
        $this->article_nodes1[$node->id()] = $node;
      }
      else {
        $this->article_nodes2[$node->id()] = $node;
      }
    }
  }

}
