;(function($) {
  $(document).ready(function() {
    const $modal = $('#cities-modal');
    const $modalTitle = $modal.find('.modal-title');
    const $modalBody = $modal.find('.modal-body');
    const $close = $('.close');
    
    const toggleModal = () => {
      $modal.toggleClass('open');
    };

    $close.on('click', toggleModal);
    
    window.onclick = e => e.target === $modal.get(0) && toggleModal();

    $('body').on('click', '.post-list a', function(e) {
      const target = $(this).attr('href')
      if (target) {
        window.location.href = target; 
      }
    });
    
    $('#vmap').vectorMap({ 
      map: 'usa_en', 
      onRegionClick: function(event, code, region) {
        const stateCode = code.toUpperCase();
        const $cityList = $(`div#${stateCode}`).clone();
        $modalTitle.html($cityList.find('h3').text());
        $modalBody.html($cityList.find('ul'));
        toggleModal();
      },
    });
  });
})(jQuery);