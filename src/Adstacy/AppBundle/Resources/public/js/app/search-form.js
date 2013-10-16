(function() {
  var $searchForm = $('#search-form');
  var $searchBox = $('#search-box');
  var tagsOption = {
      name: 'tags',
      remote: {
        url: Routing.generate('adstacy_app_api_tags', {q: '_QUERY_'}),
        wildcard: '_QUERY_'
      },
      template: '<a href="{{ url }}">{{ value }}</a>',
      header: '<h3 class="tt-dropdown-header">Tags</h3>',
      engine: Hogan
  };
  var usersOption = {
      name: 'users',
      remote: {
        url: Routing.generate('adstacy_app_api_users', {q: '_QUERY_', cond: 'noment'}),
        wildcard: '_QUERY_'
      },
      header: '<h3 class="tt-dropdown-header">Users</h3>',
      template: '<a href="{{ url }}"><img src="{{ avatar }}" width="35" height="35"> <strong class="real-name">{{ name }}</strong> (<span class="username">{{ username }}</span>)</a>',
      engine: Hogan
  };
  if ($('body').hasClass('logged-in')) {
      usersOption['prefetch'] = Routing.generate('adstacy_app_api_network', {cond: 'noment'});
  }
  $searchBox.typeahead([tagsOption, usersOption]);
  $searchBox.on('typeahead:selected', function(e, datum, name) {
    $searchBox.prop('disabled', true);
    if (name == 'users') {
      window.location.href = Routing.generate('adstacy_app_user_profile', {username: datum.username});
    } else if (name == 'tags') {
      window.location.href = Routing.generate('adstacy_app_search', {q: datum.value});
    }
  });
})();
