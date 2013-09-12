(function() {
  var $masonry = $('.masonry');
  var $adstacyContainer = $('.adstacy-container');
  var $window = $(window);
  var masonryTriggered = false;
  var masonryDestroyed = false;

  var resizeCallback = function() {
    if ($masonry.length == 0) {
      $adstacyContainer.css('width', Math.floor($window.width()/250)*250);
    }
  };
  var masonryCheck = function() {
    var windowWidth = $window.width();

    if (windowWidth > 480) {
      if (!masonryTriggered || masonryDestroyed) {
        $masonry.masonry({
          columnWidth: 250,
          isFitWidth: true,
          isInitLayout: false,
          transitionDuration: 0,
          itemSelector: '.masonry-item'
        });

        $masonry.masonry('on', 'layoutComplete', function(msnryInstance) {
          $adstacyContainer.css('width', msnryInstance.cols * msnryInstance.columnWidth + 'px');
        });
        $window.on('resize orientationChanged', resizeCallback);
        masonryTriggered = true;
      }
      $masonry.masonry();
      $().UItoTop({ easingType: 'easeOutQuart' });
      $('.navbar-collapse').removeClass('no-transition');

      $(function() {
        resizeCallback();
      });
    } else {
      if (masonryTriggered) {
        $masonry.masonry('destroy');
        masonryDestroyed = true;
      }
      $('.navbar-collapse').addClass('no-transition');
    }
  }
  $(function() {
    $window.on('resize orientationChanged', masonryCheck);
    masonryCheck();
  });
})();
