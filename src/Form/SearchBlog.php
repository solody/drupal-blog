<?php

namespace Drupal\blog\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SearchBlog.
 */
class SearchBlog extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'search_blog_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['keywords'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Keywords'),
      '#title_display' => 'invisible',
      '#placeholder' => $this->t('Input keywords to search.'),
      '#default_value' => $form_state->getUserInput()['keywords'] ?? $this->getRequest()->query->get('keywords'),
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '0',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (empty($form_state->getValue('keywords'))) {
      $form_state->setErrorByName('keywords', $this->t('Keywords should be inputted.'));
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $route_names = [
      'view.blog.blog_all',
      'view.blog.blog_tag_all',
      'view.blog.blog_user_all',
    ];
    $route_name = $route_names[0];
    $params = [];
    if (in_array($this->getRouteMatch()->getRouteName(), $route_names)) {
      $route_name = $this->getRouteMatch()->getRouteName();
    }
    if ($this->getRouteMatch()->getRouteName() === 'view.blog.blog_tag_all') {
      $params['tag'] = $this->getRouteMatch()->getParameter('tag');
    }
    if ($this->getRouteMatch()->getRouteName() === 'view.blog.blog_user_all') {
      $params['user'] = $this->getRouteMatch()->getParameter('user');
    }
    $this->redirect($route_name, $params, [
      'query' => [
        'keywords' => $form_state->getValue('keywords')
      ]
    ])->send();
  }

}
