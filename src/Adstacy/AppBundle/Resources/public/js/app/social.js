(function() {
  $('.unfollow-user').hover(
    function() { $(this).text('Unfollow'); },
    function() { $(this).text('Following'); }
  ).click(
    function() { $(this).text('Following'); }
  );
  $('.unpromote').hover(
    function() { $(this).find('.text').text('unbroadcast'); },
    function() { $(this).find('.text').text('broadcasted'); }
  ).click(
    function() { $(this).find('.text').text('broadcasted'); }
  );
  $('.tweet').click(function() {
    window.open(this.href,
      'Twitter tweet',
      'width=626,height=436'
    );

    return false;
  });
  $('.facebook-share').click(function() {
    window.open(this.href,
      'Facebook share',
      'width=626,height=436'
    );

    return false;
  });
})();
