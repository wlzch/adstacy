(function() {
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
