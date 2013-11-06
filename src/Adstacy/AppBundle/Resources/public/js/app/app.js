$(function(){
  $('.site-menu').perfectScrollbar({});
  Adstacy.events.collapseAd($('.advert-object'), false);
});

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
  $('img.lazy').lazyload();
  $('.user').ajaxlink('users');
  Adstacy.alert();
})();

$(window).on('resize orientationchange', function() {
  Adstacy.events.collapseAd($('.advert-object'), true);
});
