;(function($) {
  $(document).ready(function() {
    $('#map').usmap({
      click: function(event, data) {
        $(`a[href="#${data.name}"]`).click();
      }
    });
  })
})(jQuery);