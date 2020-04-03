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
  protected $bloggerNoEntries;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    // Create blogger user with no blog posts.
    $this->bloggerNoEntries = $this->drupalCreateUser([
      'create blog_post content',
    ]);
  }

  /**
   * Test empty blog lists.
   */
  public function testEmptyLists() {
    $this->drupalLogin($this->bloggerNoEntries);
    $this->drupalGet('blog');
    $this->assertText('No blog entries have been created.');
  }

}
