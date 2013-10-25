$(function(){
  $('.site-menu').perfectScrollbar({
                                        
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

  $('#m-btn-search').click(function() {
    $siteHeader.addClass('focus');
    $searchInput.focus();
  });

  $searchInput.blur(function() {
    $searchInput.val("");
    $siteHeader.removeClass('focus');
  });

  $('.timeago').timeago();
  $('.btn-share').click(function() {
    var $share = $(this).parent().parent().next().find('.advert-share');
    $share.toggle();
    $share.find('.url').select();
  });
  $('img.lazy').lazyload();
  $().UItoTop({ easingType: 'easeOutQuart' });
  $('.advert').ajaxlink('ads');
  $('.user').ajaxlink('users');
})();
