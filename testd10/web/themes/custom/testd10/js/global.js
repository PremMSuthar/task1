/**
 * @file
 * Global utilities.
 *
 */
(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.nih = {
    attach: function (context, settings) {
      jQuery('body.path-search div.search-page #block-nih-content h2').text('Results');

      jQuery('.nih_pagination').change(function() {
        var uri = window.location.href;
        var key = 'page';
        var value = jQuery(this).val();
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
          var url = uri.replace(re, '$1' + key + "=" + value + '$2');
        }else {
          var url = uri + separator + key + "=" + value;
        }  
        window.location = url;
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
