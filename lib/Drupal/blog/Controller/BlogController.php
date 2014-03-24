<?php

/**
 * @file
 * Contains \Drupal\blog\Controller\BlogController.
 */

namespace Drupal\blog\Controller;

use Drupal\blog\BlogListerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\user\UserInterface;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

 /**
 *
 * Controller routines for blog routes.
 */
class BlogController implements ContainerInjectionInterface {

  /**
   * The blog lister.
   *
   * @var BlogListerInterface
   */
  protected $blogLister;

  /**
   * Constructs a BlogController object.
   *
   * @param BlogListerInterface $blogLister
   *   The blog lister.
   */
  public function __construct(BlogListerInterface $blogLister) {
    $this->blogLister = $blogLister;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('blog.lister')
    );
  }

  /**
   * Prints a listing of all blog posts.
   *
   * @return array
   *   A render array representing the listing of all blog content.
   */
  public function latestBlogPosts() {
    return $this->blogLister->allBlogPosts();
  }

  /**
   * Outputs a blog RSS feed.
   *
   * @return array
   *   A render array representing the listing of all blog content.
   */
  public function latestBlogPostsRss() {
    return $this->blogLister->allBlogPostsRss();
  }

  /**
   * Prints a listing of user blog posts.
   *
   * @param UserInterface $user
   *
   * @return array
   *   A render array representing the listing of all user blog posts.
   */
  public function userBlogPosts(UserInterface $user) {
    return $this->blogLister->userBlogPosts($user);
  }

  /**
   * Outputs a user blog RSS feed.
   *
   * @param UserInterface $user
   *
   * @return array
   *   A render array representing the listing of all user blog postsi.
   */
  public function userBlogPostsRss(UserInterface $user) {
    return $this->blogLister->userBlogPostsRss($user);
  }

  /**
   * Returns a title for user blog pages
   *
   * @param UserInterface $user
   *
   * @return string
   *   A title string for a user blog page.
   */
   public function userBlogTitle(UserInterface $user) {
     return $this->blogLister->userBlogTitle($user); 
   }
}
