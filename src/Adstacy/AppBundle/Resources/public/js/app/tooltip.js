(function() {
  $('#create-ads-btn').tooltip({
    container: 'body',
    placement: 'bottom'
  });
  $('.ad').find('[data-toggle=tooltip]').tooltip({
    container: 'body',
    placement: 'top'
  });
})();
