$.fn.promote = function() {

  return this.each(function() {
    var $this = $(this);
    $this.click(function() {
      $.post(this.href, function(data) {
        var json = JSON.parse(data);
        if (!json.error) {
          $this.parent().find('.promotes-count').text(json.promotes_count);
          if ($this.hasClass('promote')) {
            $this.attr('href', Routing.generate('adstacy_app_ad_unpromote', {id: json.id}));
            $this.find('span').text(' Unpromote');
          } else {
            $this.attr('href', Routing.generate('adstacy_app_ad_promote', {id: json.id}));
            $this.find('span').text(' Promote');
          }
          $this.toggleClass('btn-default promote');
          $this.toggleClass('btn-primary unpromote');
        }
      });

      return false;
    });
  });
}
