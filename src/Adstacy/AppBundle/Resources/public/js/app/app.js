(function() {
  var $window, $siteContainer, $siteHeader, $searchDismiss, $searchInput, $favourites;
  $window = $(window);
  $siteContainer = $('#site-container');
  $siteHeader = $('#site-header');
  $searchDismiss = $('#search-dismiss');
  $searchInput = $('#search-form input[type=text]');
  $favourites = $('#favourites');

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
  $('img.lazy').lazyload({ load: Adstacy.normalizeImgHeight });
  if (Adstacy.user) {
    Adstacy.follow($('.user'), function(data) {
      $(this).closest('.user').find('.user-followers-count').text(data.followers_count);
    });
  }
  Adstacy.alert();
  $favourites.find('button').click(function() {
      var $this, $parent, tag, $html;
      $this = $(this);
      $parent = $this.closest('li');
      tag = $parent.attr('data-tag');
      $.post(Routing.generate('adstacy_app_favtags_remove', {tag: tag}));
      $parent.fadeOut();
  });
  Adstacy.hoveruser($('.hovercard-user'), {width: 400});
})();

$(function(){
  $('.site-menu').perfectScrollbar({});
  Adstacy.collapseAd($('.limit'), false);
});

$(window).on('resize orientationchange', function() {
  Adstacy.collapseAd($('.limit'), true);
});
