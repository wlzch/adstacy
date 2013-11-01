(function ($) {
  $.fn.ajaxlink = function(type, options) {
    var types = {
      users: {
        countSelector: '.user-followers-count',
        jsonField: 'followers_count',
        firstSelector: 'a.follow-user',
        secondSelector: 'a.unfollow-user'
      },
      follow_single: {
        countSelector: '.user-followers-count',
        jsonField: 'followers_count',
        firstSelector: 'a.follow-user',
        secondSelector: 'a.unfollow-user'
      }
    };
    var options = $.extend({
      image_size: 16
    }, options);

    return this.each(function() {
      var settings = types[type];
      var $parent = $(this);
      var size = settings.image_size || options.image_size;

      $parent.find(settings.firstSelector+','+settings.secondSelector).click(function(event) {
        var $this = $(this);
        $parent.find(settings.firstSelector).toggleClass('hide');
        $parent.find(settings.secondSelector).toggleClass('hide');
        $.post(this.href, function(data) {
          var json = JSON.parse(data);
          if (!json.error) {
            if (settings.countSelector) {
              $parent.find(settings.countSelector).text(json[settings.jsonField]);
            }
          }
        });

        return false;
      });
    });
  };
}( jQuery ));
