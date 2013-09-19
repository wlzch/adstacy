(function() {
  var $masonry = $('.masonry');
  var $adstacyContainer = $('.adstacy-container');
  var $window = $(window);
  var masonryTriggered = false;
  var masonryDestroyed = false;

  var resizeCallback = function() {
    var windowWidth = $window.width();
    if ($masonry.length == 0 || windowWidth > 480) {
      $adstacyContainer.css('width', Math.floor($window.width()/250)*250);
    }
    if (windowWidth <= 480) {
      $adstacyContainer.css('width', '100%');
    }
  };
  // masonry will be triggered only when it's in device with width > 480
  var masonryCheck = function() {
    var windowWidth = $window.width();

    if (windowWidth > 480) {
      // initialize masonry only when it's not initialized already or it has been destroyed
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
      $('.navbar-collapse').removeClass('no-transition');

      resizeCallback();
    } else {
      if (masonryTriggered) {
        $masonry.masonry('destroy');
        masonryDestroyed = true;
      }
      $('.navbar-collapse').addClass('no-transition');
    }
  }
  $(function() {
    WebFontConfig = {
      google: { families: [ 'Open+Sans:400,300,700:latin' ] },
      timeout: 2000,
      active: function() {
        masonryCheck();
        $('body').removeClass('body-loading');
      },
      inactive: function() {
        masonryCheck();
        $('body').removeClass('body-loading');
      }
    };
    var wf = document.createElement('script');
    wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
      '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
    wf.type = 'text/javascript';
    wf.async = 'false';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(wf, s);
  });

  $window.on('resize orientationChanged', masonryCheck);
})();

