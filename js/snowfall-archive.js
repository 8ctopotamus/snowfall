;(function($) {
  $(document).ready(function() {
    const $vMap = $('#vmap');
    $vMap.vectorMap({ 
      map: 'usa_en', 
      onRegionClick: function(event, code, region) {
        $(`a[href="#${code.toUpperCase()}"]`).click()
      }
    });
  })
})(jQuery);