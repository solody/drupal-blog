<?php

namespace Drupal\Tests\blog\Functional;

use Drupal\Tests\block\Functional\AssertBlockAppearsTrait;

/**
 * Test blog functionality.
 *
 * @group blog
 */
class BasicBlogTest extends BlogTestBase {

  use AssertBlockAppearsTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'block',
    'blog',
  ];

  /**
   * @var \Drupal\user\UserInterface
   */
  protected $regular_user;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    // Create regular user.
    $this->regular_user = $this->drupalCreateUser(['create article content']);
  }

  /**
   * Test personal blog title.
   */
  public function testPersonalBlogTitle() {
    $this->drupalLogin($this->regular_user);
    $this->drupalGet('blog/' . $this->blogger1->id());
    $this->assertResponse(200);
    $this->assertTitle($this->blogger1->getDisplayName() . "'s blog | Drupal");
  }

  /**
   * View the blog of a user with no blog entries as another user.
   */
  public function testBlogPageNoEntries() {
    $this->drupalLogin($this->regular_user);
    $this->drupalGet('blog/' . $this->blogger_no_entries->id());
    $this->assertResponse(200);
    $this->assertTitle($this->blogger_no_entries->getDisplayName() . "'s blog | Drupal");
    $this->assertText($this->blogger_no_entries->getDisplayName() . ' has not created any blog entries.');
  }

  /**
   * View blog block.
   */
  public function testBlogBlock() {
    // Place the recent blog posts block.
    $blog_block = $this->drupalPlaceBlock('blog_blockblock-views-block-blog-blog-block');
    // Verify the blog block was displayed.
    $this->drupalGet('<front>');
    $this->assertBlockAppears($blog_block);
  }

}
