(function() {
  $('form.submit-disable').submit(function() {
    var $this = $(this);
    var $btn = $this.find('.submit-disable-btn');
    var text = $btn.attr('data-disable-text') || 'Submitting...';
    $btn.attr('disabled', 'disabled');
    $btn.html('<img src="/bundles/adstacyapp/img/spinner.gif" width="14" height="14"> '+ text);
  });
})();
