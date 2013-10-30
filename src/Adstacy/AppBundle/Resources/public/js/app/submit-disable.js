(function() {
  $('form.submit-disable').submit(function() {
    var $this = $(this);
    if (!$this.hasClass('validate')) {
      var $btn = $this.find('.submit-disable-btn');
      var text = $btn.attr('data-disable-text') || 'Submitting...';
      $btn.attr('disabled', 'disabled');
      $btn.html(text);
    }
  });
})();
