;(function($) {
  $(document).ready(function() {
    const $map = $('#map');
    
    function resizeMap() {
      const mapWidth = $('#map-wrap').css('width');
      const svgWidth = $('#map-wrap').width();
      const mapHeight = svgWidth * .5; 
      $map.css('width' , mapWidth).css('height' , mapHeight +'px');
      $map.find('svg').attr('width' , svgWidth).attr('height' , mapHeight).attr('viewBox', `-170 0 930 630`);
    };

    $map.usmap({
      stateStyles: { fill: '#2053c0' },
      stateHoverStyles: { fill: '#5fb7f9' },
      showLabels: true,
      click: function(event, data) {
        $(`a[href="#${data.name}"]`).click();
      }
    })

    resizeMap();

    $(window).resize(function() {
      resizeMap();
    });
  })
})(jQuery);