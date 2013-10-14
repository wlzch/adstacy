(function() {
  var $searchForm = $('#search-form');
  var $searchBox = $('#search-box');
  $searchBox.typeahead([
    {
      name: 'tags',
      remote: {
        url: Routing.generate('adstacy_app_api_tags', {q: '_QUERY_'}),
        wildcard: '_QUERY_'
      },
      template: '<a href="{{ url }}">{{ value }}</a>',
      header: '<h3 class="tt-dropdown-header">Tags</h3>',
      engine: Hogan
    },
    {
      name: 'users',
      prefetch: Routing.generate('adstacy_app_api_network', {cond: 'noment'}),
      remote: {
        url: Routing.generate('adstacy_app_api_users', {q: '_QUERY_', cond: 'noment'}),
        wildcard: '_QUERY_'
      },
      header: '<h3 class="tt-dropdown-header">Users</h3>',
      template: '<a href="{{ url }}"><img src="{{ avatar }}" width="35" height="35"> <strong class="real-name">{{ name }}</strong> (<span class="username">{{ username }}</span>)</a>',
      engine: Hogan
    }
  ]);
  $searchBox.on('typeahead:selected', function(e, datum, name) {
    console.log(e);
    $searchBox.prop('disabled', true);
    if (name == 'users') {
      window.location.href = Routing.generate('adstacy_app_user_profile', {username: datum.username});
    } else if (name == 'tags') {
      window.location.href = Routing.generate('adstacy_app_search', {q: datum.value});
    }
  });
})();
