<?php

namespace Drupal\Tests\blog\Functional;

/**
 * Link "My blog" test for blog module.
 *
 * @group blog
 */
class MyBlogLinkTest extends BlogTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'block',
    'blog',
  ];

  /**
   * @var \Drupal\user\UserInterface
   */
  protected $regularUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    // Create regular user.
    $this->regularUser = $this->drupalCreateUser(['create article content']);
    // Add account_menu block.
    $this->placeBlock('system_menu_block:account', ['region' => 'content']);
  }

  /**
   * Test "My blog" link with regular user.
   */
  public function testMyBlogLinkWithRegularUser() {
    $this->drupalLogin($this->regularUser);
    $this->assertLink('My blog');
    $this->assertLinkByHref('/blog/' . $this->regularUser->id());
  }

  /**
   * Test "My blog" link with anonymous user.
   */
  public function testMyBlogLinkWithAnonUser() {
    $this->assertNoLink('My blog');
  }

}
