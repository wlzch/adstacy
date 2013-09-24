(function() {
  $('img.lazy').lazyload({
    threshold: 2000,
    failure_limit: 5,
    effect: 'fadeIn',
    skip_invisible: false
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
