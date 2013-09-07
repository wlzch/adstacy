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
  var $searchContainer = $('#search-container');
  var windowWidth = $window.width();
  console.log(windowWidth);

  if (windowWidth >= 480) {
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
    $masonry.masonry();

    var resizeCallback = function() {
      if ($masonry.length == 0) {
        $adstacyContainer.css('width', Math.floor($window.width()/250)*250);
      }
    };
    $window.on('resize orientationChanged', resizeCallback);
    $(function() {
      resizeCallback();
    });
  }
  $(function() {
    $().UItoTop({ easingType: 'easeOutQuart' });
  });

  $('img.lazy').lazyload();
  $('.unfollow-user').hover(
    function() { $(this).text('Unfollow'); },
    function() { $(this).text('Following'); }
  ).click(
    function() { $(this).text('Following'); }
  );
  $('.unpromote').hover(
    function() { $(this).find('.text').text('Unpromote'); },
    function() { $(this).find('.text').text('Promoted'); }
  ).click(
    function() { $(this).find('.text').text('Promoted'); }
  );
  $('.ad').ajaxlink('ads');
  $('.user').ajaxlink('users');
  $('[data-toggle=tooltip]').tooltip({
    container: "body"
  });
  $searchContainer.find('.dropdown-menu li').click(function() {
    var $type = $searchContainer.find('#search-type');
    $type.val($(this).find('a').attr('data-val'));
    $searchContainer.find('#search-type-text').text($(this).find('a').text());
  });
  $('#search-form').submit(function() {
    var $box = $(this).find('#search-box');
    if ($box.val().length == 0) return false;
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
