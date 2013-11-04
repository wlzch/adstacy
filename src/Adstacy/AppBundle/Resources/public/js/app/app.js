$(function(){
  $('.site-menu').perfectScrollbar({});
  $('.advert-object').children().each(Adstacy.events.collapseAd);
});

(function() {
  var $window = $(window);
  var $sidebar = $('#sidebar');
  var $siteContainer = $('#site-container');
  var $siteHeader = $('#site-header');
  var $searchDismiss = $('#search-dismiss');
  var $searchInput = $('#search-form input[type=text]');
  var template = Adstacy.templates.sidebar_recommendation;

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
  $().UItoTop({ easingType: 'easeOutQuart' });
  $('.user').ajaxlink('users');
  Adstacy.alert();
  if (Adstacy.user) {
    $.get(Routing.generate('adstacy_api_recommendation'), function(data) {
      var json, users, html;
      json = JSON.parse(data);
      console.log(json);
      users = json.data.users;
      if (users && users.length > 0) {
        $html = $(template.render({
          users: users
        }));
        $html.hide();
        $sidebar.append($html);
        $html.fadeIn();
        Adstacy.hoveruser($html.find('.hovercard-user'), {openOnLeft: true});
      }
    });
  }
})();
