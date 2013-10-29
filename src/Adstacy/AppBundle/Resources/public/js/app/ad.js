(function() {
  $('.btn-share').click(Adstacy.events.share);
  $('body').click(function() {
    $('.advert-share.open').removeClass('open').hide();
  });
  $('.advert').ajaxlink('ads');
  $('.advert-action .report').click(function() {
    var $this = $(this);
    $.post(this.href, function(data) {
      console.log(data);
      // display message
    });

    return false;
  });
})();
