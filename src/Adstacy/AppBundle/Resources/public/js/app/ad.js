(function() {
  var $advert = $('.advert');
  var $body = $('body');
  $advert.find('.btn-share').click(Adstacy.events.share);
  $advert.find('.btn-promote').click(Adstacy.events.broadcastclick);
  $advert.find('.report').click(Adstacy.events.adreportclick);
  $advert.find('.delete').click(Adstacy.events.deleteAd);
  $advert.find('.advert-img').dblclick(Adstacy.events.adimagedblclick);
  $body.click(function() {
    $('.advert-share.open').removeClass('open').hide();
  });
})();
