(function() {
  if (Adstacy.user) {
    var $notifBtn, $notifCount;
    $notifBtn = $('#notif-btn');
    $notifCount = $('.notif-count');
    $notifBtn.click(function() {
      var $this, href;
      $this = $(this);
      href = $this.attr('data-href');
      if (parseInt($notifCount.text()) > 0) {
        $notifCount.text(0);
        $.post(href);
      }
    });
  }
})();
