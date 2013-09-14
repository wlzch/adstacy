(function() {
  $('img.lazy').lazyload({
    threshold: 1500,
    effect: 'fadeIn'
  });
  $('.advert').ajaxlink('ads');
  $('.user').ajaxlink('users');
  $('.comment-box').keydown(function(event) {
    if (event.which == 13 && event.shiftKey) {
      return true;
    } else if (event.which == 13) {
      this.form.submit();
      return false;
    }
  });
})();
