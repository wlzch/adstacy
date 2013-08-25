(function ($) {
  $.fn.ajaxlink = function(options) {
    var settings = $.extend({}, options);

    return this.each(function() {
      var $this = $(this);
      $this.click(function() {
        $.post(this.href, function(data) {
          var json = JSON.parse(data);
          if (!json.error) {
            var $parent = $this.closest(settings.parentSelector);
            $parent.find(settings.countSelector).text(json[settings.jsonField]);
            $parent.find(settings.firstSelector).toggleClass('hide');
            $parent.find(settings.secondSelector).toggleClass('hide');
          }
        });

        return false;
      });
    });
  };
}( jQuery ));
