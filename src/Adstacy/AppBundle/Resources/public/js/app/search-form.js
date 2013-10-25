(function() {
  var $searchForm = $('#search-form');
  var $searchBox = $('#search-box');
  var tagsOption = {
      name: 'tags',
      remote: {
        url: Routing.generate('adstacy_api_tags', {q: '_QUERY_'}),
        wildcard: '_QUERY_'
      },
      template: '<a href="{{ url }}">{{ value }}</a>',
      header: '<div class="tt-dropdown-header">Ads</div>',
      engine: Hogan
  };
  var usersOption = {
      name: 'users',
      remote: {
        url: Routing.generate('adstacy_api_users', {q: '_QUERY_', cond: 'noment'}),
        wildcard: '_QUERY_'
      },
      header: '<div class="tt-dropdown-header">People</div>',
      template: '<a href="{{ url }}"><img src="{{ avatar }}" width="32" height="32"> <strong class="real-name">{{ name }}</strong> <span class="username text-muted">@{{ username }}</span></a>',
      engine: Hogan
  };
  if ($('body').hasClass('logged-in')) {
      usersOption['prefetch'] = Routing.generate('adstacy_api_network', {cond: 'noment'});
  }
  $searchBox.typeahead([tagsOption, usersOption]);
  $searchBox.on('typeahead:selected', function(e, datum, name) {
    if (name == 'users') {
      $searchBox.val('@'+$searchBox.val());
    }
  });
})();
