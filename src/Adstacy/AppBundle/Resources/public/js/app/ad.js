(function() {
  var $advert = $('.advert');
  var $body = $('body');
  $advert.find('.btn-share').click(Adstacy.events.share);
  $advert.ajaxlink('ads');
  $advert.find('.advert-action .report').click(function() {
    var $this = $(this);

    Adstacy.alert('success', Translator.trans('ads.report.success'));
    $.post(this.href, function(data) {
      // do nothing
    });

    $body.click();
    return false;
  });
  $advert.find('.delete').click(Adstacy.events.deleteAd);
  $advert.find('.advert-img').dblclick(Adstacy.events.adimagedblclick);
  $body.click(function() {
    $('.advert-share.open').removeClass('open').hide();
  });
})();
