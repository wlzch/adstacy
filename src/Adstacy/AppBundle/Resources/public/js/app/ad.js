(function() {
  var $advert = $('.advert');
  var $body = $('body');
  $advert.find('.btn-share').click(Adstacy.events.share);
  $advert.find('.advert-broadcasts .count').click(Adstacy.events.broadcastcountclick);
  $body.click(function() {
    $('.advert-share.open').removeClass('open').hide();
  });
  if (Adstacy.user) {
    Adstacy.broadcast($advert);
    $advert.find('.report').click(Adstacy.events.adreportclick);
    $advert.find('.delete').click(Adstacy.events.deleteAd);
    $advert.find('.advert-img').dblclick(Adstacy.events.adimagedblclick);
  };
})();
