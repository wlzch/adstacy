(function() {
  $('.btn-share').click(function(event) {
    var $share = $(this).parent().parent().next().find('.advert-share');
    $share.show();
    $share.find('.url').select();
    $share.addClass('open');
    event.preventDefault();
    return false;
  });
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
