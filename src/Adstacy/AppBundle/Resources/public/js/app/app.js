(function() {
  var $window = $(window);
  var $siteContainer = $('#site-container');
  var $siteHeader = $('#site-header');
  var $searchDismiss = $('#search-dismiss');
  var $searchInput = $('#search-form input[type=text]');

  $('#site-menu-toggle').click(function() {
    $siteContainer.toggleClass('open');
  });

  $('#m-btn-search').click(function() {
    $siteHeader.addClass('focus');
    $searchInput.focus();
  });

  $searchInput.blur(function() {
    $siteHeader.removeClass('focus');
  });

  $('.timeago').timeago();
  $('img.lazy').lazyload({
    load: function() {
      $(this).parent().css('height', 'auto');
    }
  });
  if (Adstacy.user) {
    Adstacy.follow($('.user'), function(data) {
      $(this).closest('.user').find('.user-followers-count').text(data.followers_count);
    });
    Adstacy.alert();
    Adstacy.hoveruser($('.hovercard-user'));
  }
})();

$(function(){
  $('.site-menu').perfectScrollbar({});
  Adstacy.events.collapseAd($('.advert-object'), false);
});

$(window).on('resize orientationchange', function() {
  Adstacy.events.collapseAd($('.advert-object'), true);
});
