(function($) {
  'use strict';

  function initSelect2() {
    if ($(".js-example-basic-single").length) {
      $(".js-example-basic-single").each(function() {
        if (!$(this).hasClass("select2-hidden-accessible")) {
          $(this).select2({
            width: '100%',
            dropdownAutoWidth: false
          });
        }
      });
    }
    if ($(".js-example-basic-multiple").length) {
      $(".js-example-basic-multiple").each(function() {
        if (!$(this).hasClass("select2-hidden-accessible")) {
          $(this).select2({
            width: '100%',
            dropdownAutoWidth: false
          });
        }
      });
    }
    $('.select2-container:not(.select2-container--open)').css('width', '100%');
  }

  $(document).ready(function() {
    initSelect2();
  });

  $(window).on('resize orientationchange', function() {
    $('.select2-container:not(.select2-container--open)').css('width', '100%');
  });
})(jQuery);

