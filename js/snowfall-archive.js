;(function($) {
  $(document).ready(function() {
    $('#vmap').vectorMap({ 
      map: 'usa_en', 
      onRegionClick: function(event, code, region) {
        const stateCode = code.toUpperCase();
        const cityList = $(`div#${stateCode}`);
        const highlightClass = 'highlight';
        $(`a[href="#${stateCode}"]`).click();
        cityList.addClass(highlightClass);
        setTimeout(() => cityList.removeClass(highlightClass), 2200);
      }
    });
  });
})(jQuery);