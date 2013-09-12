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
  if(!Modernizr.input.placeholder){
    $('[placeholder]').focus(function() {
      var input = $(this);
      if (input.val() == input.attr('placeholder')) {
        input.val('');
        input.removeClass('placeholder');
      }
    }).blur(function() {
      var input = $(this);
      if (input.val() == '' || input.val() == input.attr('placeholder')) {
        input.addClass('placeholder');
        input.val(input.attr('placeholder'));
      }
    }).blur();
    $('[placeholder]').parents('form').submit(function() {
      $(this).find('[placeholder]').each(function() {
        var input = $(this);
        if (input.val() == input.attr('placeholder')) {
          input.val('');
        }
      })
    });
  }
  var $masonry = $('.masonry');
  var $adstacyContainer = $('.adstacy-container');
  var $window = $(window);
  var $searchForm = $('#search-form');

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
  $searchForm.find('.dropdown-menu li').click(function() {
    var $type = $searchForm.find('#search-type');
    $type.val($(this).find('a').attr('data-val'));
    $searchForm.find('#search-type-text').text($(this).find('a').text());
  });
  $searchForm.submit(function() {
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
  $('form.submit-disable').submit(function() {
    var $this = $(this);
    var $btn = $this.find('.submit-disable-btn');
    var text = $btn.attr('data-disable-text') || 'Submitting...';
    $btn.attr('disabled', 'disabled');
    $btn.html('<img src="/bundles/adstacyapp/img/spinner.gif" width="15" height="15"> '+ text);
  });
  $('.comment-box').keydown(function(event) {
    if (event.which == 13 && event.shiftKey) {
      return true;
    } else if (event.which == 13) {
      this.form.submit();
      return false;
    }
  });
})();
