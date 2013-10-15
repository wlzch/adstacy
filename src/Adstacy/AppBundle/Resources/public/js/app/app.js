$(function(){
  $('#site-menu-inner').slimScroll({
    width: '240px',
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

  $('.btn-share').click(function() {
    var tab = $(this).parent().next();
    tab.find('.advert-share').toggle();
    tab.find('.advert-comment').hide();
  });

  $('.btn-comment').click(function() {
    var tab = $(this).parent().next();
    tab.find('.advert-comment').toggle();
    tab.find('.advert-share').hide();
  });

  $('img.lazy').lazyload({
    container: $('.site-adverts'),
    effect: 'fadeIn'
  });
  $().UItoTop({ easingType: 'easeOutQuart' });
  $('.advert').ajaxlink('ads');
  $('.user').ajaxlink('users');
  $('.comment-box').keyup(function(event) {
    var $this = $(this);
    if ($.trim($this.val()).length <= 0) {
      event.preventDefault();
      return false;
    }

    var $parent = $this.parent();
    var $typeahead = $parent.find('.typeahead');
    if ($typeahead.length == 0 || ($typeahead.length > 0 && $typeahead.is(':hidden'))) {
      if (event.which == 13 && event.shiftKey) {
        event.preventDefault();
        return false;
      } else if (event.which == 13) {
        this.blur();
        this.form.submit();
        this.disabled = true;
      }
    }
  });
  var users;
  var mention = function(users) {
    $('.comment-box').mention({
      key: 'username',
      delimiter: '@',
      users: users
    });
  };
  var getUsers = function() {
    $.getJSON('/api/users.json', function(data) {
      var users = JSON.parse(data);
      mention(users);
      if (Modernizr.localstorage) {
        localStorage.setItem('users', JSON.stringify(users));
      }
    });
  }
  if (Modernizr.localstorage) {
    users = localStorage.getItem('users');
    if ($.isArray(users)) {
      mention(JSON.parse(users));
    } else {
      getUsers();
    }
  } else {
    getUsers();
  }
})();
