(function() {
  $('.advert').each(function() {
    var $this = $(this);
    $this.find('.btn-share').click(Adstacy.events.share);
    $this.ajaxlink('ads');
    $this.find('.advert-action .report').click(function() {
      var $this = $(this);
      $.post(this.href, function(data) {
        console.log(data);
        // display message
      });

      return false;
    });
    $this.find('.delete').click(Adstacy.events.deleteAd);
  });
  $('body').click(function() {
    $('.advert-share.open').removeClass('open').hide();
  });
})();
