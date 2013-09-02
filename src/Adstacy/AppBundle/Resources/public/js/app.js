(function() {
  if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
    var msViewportStyle = document.createElement("style")
    msViewportStyle.appendChild(
      document.createTextNode(
        "@-ms-viewport{width:auto!important}"
      )
    )
    document.getElementsByTagName("head")[0].appendChild(msViewportStyle)
  }
  var $masonry = $('.masonry');
  var $adstacyContainer = $('.adstacy-container');
  var $window = $(window);
  var $btnNavbarExpand = $('#btn-navbar-expand');
  var $btnNavbarExpand_span = $('#btn-navbar-expand span');
  var $navbarExpandContainer = $('#navbar-expand-container');

  $masonry.masonry({
    columnWidth: 250,
    isFitWidth: true,
    isInitLayout: false,
    transitionDuration: 0,
    itemSelector: '.ad'
  });

  $masonry.masonry('on', 'layoutComplete', function(msnryInstance) {
    $adstacyContainer.css('width', msnryInstance.cols * msnryInstance.columnWidth + 'px');
  });
  $masonry.masonry();

  var resizeCallback = function() {
    if ($masonry.length == 0) {
      $adstacyContainer.css('width', Math.floor($window.width()/250)*250);
    }
  };
  $window.on('resize orientationChanged', resizeCallback);

  $(function() {
    resizeCallback();
    $().UItoTop({ easingType: 'easeOutQuart' });
  });

  $('img.lazy').lazyload();
  $btnNavbarExpand.click(function(event) {
    $btnNavbarExpand_span.toggleClass('icon-chevron-down');
    $navbarExpandContainer.toggleClass('hide');

    event.stopPropagation();
    event.preventDefault();

    return false;
  })
  $('html, dropdown-toggle').click(function() {
    $btnNavbarExpand_span.addClass('icon-chevron-down');
    $navbarExpandContainer.addClass('hide');
  });
  $('.ad-social').ajaxlink('ads');
  $('[data-toggle=tooltip]').tooltip({
    container: "body"
  });
  $('.tweet').click(function() {
    window.open(this.href,
      'Twitter tweet',
      'width=626,height=436'
    );

    return false;
  });
  $('.facebook-share').click(function() {
    window.open(this.href,
      'Facebook share',
      'width=626,height=436'
    );

    return false;
  });
})();
