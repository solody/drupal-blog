(function ($, Drupal) {
  Drupal.behaviors.hide_views_exposed_form = {
    attach: function (context, settings) {
      $('form.views-exposed-form', context).hide();
    }
  };
})(jQuery, Drupal);
