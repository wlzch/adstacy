(function() {
  var $advert = $('.advert');
  var $body = $('body');
  $advert.find('.btn-share').click(Adstacy.events.share);
  $advert.ajaxlink('ads');
  $advert.find('.advert-action .report').click(function() {
    var $this = $(this);

    Adstacy.alert('success', 'Ad has been reported');
    $.post(this.href, function(data) {
      // do nothing
    });

    $body.click();
    return false;
  });
  $advert.find('.delete').click(Adstacy.events.deleteAd);
  $('body').click(function() {
    $('.advert-share.open').removeClass('open').hide();
  });
})();
