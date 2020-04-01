<?php

namespace Drupal\Tests\blog\Functional;

use Drupal\Tests\block\Traits\BlockCreationTrait;
use Drupal\Tests\BrowserTestBase;

/**
 * Breadcrumb test for blog module.
 *
 * @group blog
 */
class BreadcrumbTest extends BrowserTestBase {
  use BlockCreationTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'block',
    'comment',
    'blog',
    'menu_ui',
    'node',
    'path',
    'taxonomy',
  ];

  /**
   * @var \Drupal\node\NodeInterface[]
   */
  protected $blog_nodes;

  /**
   * @var \Drupal\node\NodeInterface[]
   */
  protected $article_nodes;

  /**
   * @var \Drupal\user\UserInterface
   */
  protected $blogger;


  protected function setUp() {
    parent::setUp();
    // add article node type
    $this->createContentType([
      'type' => 'article',
    ]);
    // add breadcrumb block
    $this->blogger = $this->drupalCreateUser([
      'create article content',
      'create blog_post content',
      'administer blocks',
    ]);
    $this->drupalLogin($this->blogger);
    // generate blog posts and articles
    $this->blog_nodes = [];
    for ($i = 0; $i < 5; $i++) {
      $node = $this->createNode([
        'type' => 'blog_post',
        'title' => $this->randomMachineName(32),
        'uid' => $this->blogger->id(),
      ]);
      $this->blog_nodes[$node->id()] = $node;
    }
    $this->article_nodes = [];
    for ($i = 0; $i < 5; $i++) {
      $node = $this->createNode([
        'type' => 'article',
        'title' => $this->randomMachineName(32),
        'uid' => $this->blogger->id(),
      ]);
      $this->article_nodes[$node->id()] = $node;
    }
    // add breadcrumb block
    $this->placeBlock('system_breadcrumb_block', ['region' => 'content']);
  }

  /**
   * Blog node type breadcrumb test.
   */
  public function testBlogNodeBreadcrumb() {
    $blog_nid = array_rand($this->blog_nodes);
    $blog_owner = $this->blog_nodes[$blog_nid]->getOwner();
    $this->drupalGet('node/' . $blog_nid);
    $links = $this->getSession()->getPage()->findAll('css', '.block-system-breadcrumb-block li a');
    $this->assertEquals(count($links), 3, 'Breadcrumb element number is correctly.');
    list($home, $blogs, $personal_blog) = $links;
    $this->assertTrue(($home->getAttribute('href') == '/' && $home->getHtml() == 'Home'), 'Home link correctly.');
    $this->assertTrue(($blogs->getAttribute('href') == '/blog' && $blogs->getHtml() == 'Blogs'), 'Blogs link correctly.');
    $blog_name = \Drupal::service('blog.lister')->userBlogTitle($blog_owner);
    $blog_url = '/blog/' . $blog_owner->id();
    $this->assertTrue(($personal_blog->getAttribute('href') == $blog_url && $personal_blog->getHtml() == $blog_name), 'Personal blog link correctly.');
  }

  /**
   * Other node type breadcrumb test.
   */
  public function testOtherNodeBreadcrumb() {
    $article_nid = array_rand($this->article_nodes);
    $article_owner = $this->article_nodes[$article_nid]->getOwner();
    $blog_name = \Drupal::service('blog.lister')->userBlogTitle($article_owner);
    $this->drupalGet('node/' . $article_nid);
    $links = $this->getSession()->getPage()->findAll('css', '.block-system-breadcrumb-block li a');
    $link = array_pop($links);
    $this->assertFalse($link->getHtml() == $blog_name, 'Other node type breadcrumb is correct.');
  }

}
