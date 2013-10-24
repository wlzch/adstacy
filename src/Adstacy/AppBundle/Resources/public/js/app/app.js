$(function(){
  $('#site-menu-inner').slimScroll({
    width: '200px',
    height: '100%',
    size: '8px'
  });
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

  $('#btn-search').click(function() {
    $siteHeader.addClass('focus');
    $searchInput.focus();
  });

  $('#search-dismiss').click(function() {
    $searchInput.val("");
    $siteHeader.removeClass('focus');
  });

  $searchInput.blur(function() {
    $searchInput.val("");
    $siteHeader.removeClass('focus');
  });

  $('.timeago').timeago();
  $('.btn-share').click(function() {
    $(this).parent().parent().next().find('.advert-share').toggle();
  });

  $('img.lazy').lazyload();
  $().UItoTop({ easingType: 'easeOutQuart' });
  $('.advert').ajaxlink('ads');
  $('.user').ajaxlink('users');
})();
