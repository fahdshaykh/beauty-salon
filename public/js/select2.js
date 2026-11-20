(function($) {
  'use strict';

  if ($(".js-example-basic-single").length) {
    $(".js-example-basic-single").select2({
      width: '100%',
      dropdownAutoWidth: false
    });
  }
  if ($(".js-example-basic-multiple").length) {
    $(".js-example-basic-multiple").select2({
      width: '100%',
      dropdownAutoWidth: false
    });
  }
})(jQuery);
