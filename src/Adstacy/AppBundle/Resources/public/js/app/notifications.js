(function() {
  $('.notifications').find('li').click(function() {
    var $this = $(this);
    var href = $this.find('.notification').attr('data-href');
    window.location = href;
  });
})();
