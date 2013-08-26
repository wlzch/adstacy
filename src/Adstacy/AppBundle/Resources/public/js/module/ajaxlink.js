(function ($) {
  $.fn.ajaxlink = function(options) {
    var settings = $.extend({}, options);

    return this.each(function() {
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
