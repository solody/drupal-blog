<?php

/**
 * @file
 * Contains \Drupal\book\BlogListerInterface.
 */

namespace Drupal\blog;

use Drupal\user\UserInterface;

/**
 * Provides an interface defining a blog lister.
 */
interface BlogListerInterface {

  /**
   * Constructs listing page for all blog posts.
   *
   */
  public function allBlogPosts();

  /**
   * Constructs listing page for user blog posts
   *
   * @param UserInterface $user 
   */
  public function userBlogPosts(UserInterface $user);

  /**
   * Constructs RSS feed for all blog posts.
   *
   */
  public function allBlogPostsRss();

  /**
   * Constructs RSS feed for user blog posts
   *
   * @param UserInterface $user 
   */
  public function userBlogPostsRss(UserInterface $user);

  /**
   * Returns a title for a user blog
   *
   */
  public function userBlogTitle(UserInterface $user);

}
