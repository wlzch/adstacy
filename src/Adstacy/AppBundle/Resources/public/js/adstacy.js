(function(window) {
  var Adstacy = {};
  var $body = $('body');
  if ($body.attr('data-user')) {
    Adstacy.user = JSON.parse($body.attr('data-user'));
  }
  window.Adstacy = Adstacy;
})(window);
