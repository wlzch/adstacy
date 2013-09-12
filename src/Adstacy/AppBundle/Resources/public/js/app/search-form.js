(function() {
  var $searchForm = $('#search-form');

  $searchForm.find('.dropdown-menu li').click(function() {
    var $type = $searchForm.find('#search-type');
    $type.val($(this).find('a').attr('data-val'));
    $searchForm.find('#search-type-text').text($(this).find('a').text());
  });
  $searchForm.submit(function() {
    var $box = $(this).find('#search-box');
    if ($box.val().length == 0) return false;
  });
})();
