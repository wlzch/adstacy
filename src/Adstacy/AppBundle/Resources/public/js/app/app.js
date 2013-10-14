(function() {
  var $body = $('body');
  $('img.lazy').lazyload({
    threshold: 2000,
    failure_limit: 5,
    effect: 'fadeIn',
    skip_invisible: false
  });
  $().UItoTop({ easingType: 'easeOutQuart' });
  $('.advert').ajaxlink('ads');
  $('.user').ajaxlink('users');
})();
