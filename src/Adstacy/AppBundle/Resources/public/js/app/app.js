(function() {
  $('img.lazy').lazyload({
    threshold: 1500,
    failure_limit: 5,
    effect: 'fadeIn'
  });
  $('.advert').ajaxlink('ads');
  $('.user').ajaxlink('users');
  $('.comment-box').keydown(function(event) {
    if (event.which == 13) {
      this.form.submit();
      return false;
    }
  });
})();
