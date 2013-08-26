(function ($) {
  $.fn.ajaxlink = function(type) {
    var types = {
      ads: {
        countSelector: '.promotes-count',
        jsonField: 'promotes_count',
        firstSelector: 'a.promote',
        secondSelector: 'a.unpromote'
      },
      show_wall: {
        countSelector: '.followers-count',
        jsonField: 'followers_count',
        firstSelector: 'a.follow-wall',
        secondSelector: 'a.unfollow-wall'
      },
      show_ad: {
        firstSelector: 'a.follow-wall',
        secondSelector: 'a.unfollow-wall'
      },
      user_profile: {
        countSelector: '.user-followers-count',
        jsonField: 'followers_count',
        firstSelector: 'a.follow-user',
        secondSelector: 'a.unfollow-user'
      }
    };

    return this.each(function() {
      var settings = types[type];
      var $parent = $(this);

      $parent.find(settings.firstSelector+','+settings.secondSelector).click(function(event) {
        var $this = $(this);
        $.post(this.href, function(data) {
          var json = JSON.parse(data);
          if (!json.error) {
            if (settings.countSelector) {
              $parent.find(settings.countSelector).text(json[settings.jsonField]);
            }
            $parent.find(settings.firstSelector).toggleClass('hide');
            $parent.find(settings.secondSelector).toggleClass('hide');
          }
        });

        event.preventDefault();
        event.stopPropagation();
        return false;
      });
    });
  };
}( jQuery ));
