(function() {
  if (Adstacy.user) {
    var $notifBtn = $('#notif-btn');
    var $notifCount = $('.notif-count');
    $notifBtn.find('button.dropdown-toggle').click(function() {
      var $this = $(this);
      var href = $this.attr('data-href');
      if (parseInt($notifCount.text()) > 0) {
        $notifCount.text(0);
        $.post(href, function(data) {
          //nothing to do
        });
      }
    });
  }
})();
