<?php

namespace Drupal\Tests\blog\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Test for empty blogs.
 *
 * @group blog
 */
class EmptyBlogTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'blog',
  ];

  /**
   * @var \Drupal\user\UserInterface
   */
  protected $blogger_no_entries;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    // create blogger user with no blog posts
    $this->blogger_no_entries = $this->drupalCreateUser([
      'create blog_post content',
    ]);
  }

  /**
   * Test empty blog lists
   */
  public function testEmptyLists() {
    $this->drupalLogin($this->blogger_no_entries);
    $this->drupalGet('blog');
    $this->assertText('No blog entries have been created.');
  }

}
