(function ($) {
  $.fn.ajaxlink = function(type, options) {
    var types = {
      ads: {
        countSelector: '.promotes-count',
        jsonField: 'promotes_count',
        firstSelector: 'a.promote',
        secondSelector: 'a.unpromote'
      },
      promote_single: {
        countSelector: '.promotes-count',
        jsonField: 'promotes_count',
        firstSelector: 'a.promote',
        secondSelector: 'a.unpromote'
      },
      follow_user: {
        countSelector: '.user-followers-count',
        jsonField: 'followers_count',
        firstSelector: 'a.follow-user',
        secondSelector: 'a.unfollow-user'
      }
    };
    var options = $.extend({
      loading: '<img src="/bundles/adstacyapp/img/spinner.gif" width="16" height="16" />'
    }, options);

    return this.each(function() {
      var settings = types[type];
      var $parent = $(this);

      $parent.find(settings.firstSelector+','+settings.secondSelector).click(function(event) {
        var $this = $(this);
        var $html = $this.html();
        $this.html(options.loading);
        $.post(this.href, function(data) {
          $this.html($html);
          var json = JSON.parse(data);
          if (!json.error) {
            if (settings.countSelector) {
              $parent.find(settings.countSelector).text(json[settings.jsonField]);
            }
            $parent.find(settings.firstSelector).toggleClass('hide');
            $parent.find(settings.secondSelector).toggleClass('hide');
            if ($this.attr('data-toggle') == 'tooltip') {
              $this.tooltip('hide');
            }
          }
        });

        event.preventDefault();
        event.stopPropagation();

        return false;
      });
    });
  };
}( jQuery ));
