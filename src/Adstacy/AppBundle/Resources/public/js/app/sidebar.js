(function() {
  var $sidebar = $('#sidebar');
  var template = Adstacy.templates.sidebar_recommendation;
  if (Adstacy.user) {
    $.get(Routing.generate('adstacy_api_recommendation'), function(data) {
      var json, users, html;
      json = JSON.parse(data);
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
