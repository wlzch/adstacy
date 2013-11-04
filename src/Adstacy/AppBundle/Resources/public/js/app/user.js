(function() {
  if (!$.browser.mobile) {
    var $hovercard = $('.hovercard-user');
    var template = Adstacy.templates.hovercarduser;
    $hovercard.hovercard({
      detailsHTML: '<p>loading...</p>',
      delay: 300,
      onHoverIn: function() {
        var $this, $user, route, $detail;
        $this = $(this);
        $user = $this.find('.hovercard-user');
        $detail = $this.find('.hc-details');
        var route = Routing.generate('adstacy_api_user_show', {username: $user.attr('data-username')});
        $.get(route, function(data) {
          var json, user
          json = JSON.parse(data);
          user = json.data.user;
          $detail.html(template.render({
            photo: user.photo,
            about: user.about
          }));
        });
      }
    });
  }
})();
