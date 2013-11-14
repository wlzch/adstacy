(function() {
  var $window, $siteContainer, $siteHeader, $searchDismiss, $searchInput, $favourites, $siteMenu;
  $window = $(window);
  $siteContainer = $('#site-container');
  $siteHeader = $('#site-header');
  $searchDismiss = $('#search-dismiss');
  $searchInput = $('#search-form input[type=text]');
  $favourites = $('#favourites');
  $siteMenu = $('#site-menu');

  $('#site-menu-toggle').click(function() {
    $siteContainer.toggleClass('open');
  });

  $('#m-btn-search').click(function() {
    $siteHeader.addClass('focus');
    $searchInput.focus();
    return false;
  });

  $siteContainer.click(function() {
    $siteHeader.removeClass('focus');
  });

  $('.timeago').timeago();
  $('img.lazy').lazyload({ load: Adstacy.normalizeImgHeight });
  Adstacy.alert();
  // assign site menu height, replace calc
  $siteMenu.height($window.height() - 43).perfectScrollbar({ wheelSpeed: 20 });
  Adstacy.collapseAd($('.limit'), false);
  $window.on('resize orientationchange', function() {
    Adstacy.collapseAd($('.limit'), true);
    $siteMenu.height($window.height() - 43).perfectScrollbar('update');
  });
  if (Adstacy.user) {
    Adstacy.follow($('.user'), function(data) {
      $(this).closest('.user').find('.user-followers-count').text(data.followers_count);
    });
  }
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
