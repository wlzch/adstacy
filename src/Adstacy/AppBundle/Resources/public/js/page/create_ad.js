$(function() {
  $('#ad_tags').tagsinput({
    itemText: function(item) {
      if (item && item.length > 0) {
        if (item[0] != '#') return '#'+item;
        return item;
      }
      return false;
    },
    typeahead: {
      name: 'tags',
      remote: {
        url: Routing.generate('adstacy_app_api_tags', {q: '_QUERY_'}),
        wildcard: '_QUERY_'
      }
    }
  });
  var $charsLeft = $('#chars-left');
  var $desc = $('#ad_description');
  var descLength = parseInt($('#chars-left-box').attr('data-desc-length'));
  var checkCharsLeft = function() {
    var val = $desc.val();
    if (val.length > descLength) {
      if (val.length == descLength + 1) {
        return false;
      }

      $desc.val(val.substring(0, descLength - 1));
      return false;
    }
    $charsLeft.text(descLength - val.length);
  };
  $desc.keyup(checkCharsLeft);
  //$desc.keypress(checkCharsLeft);
  var editor = new wysihtml5.Editor('ad_content', {
    toolbar: 'wysihtml5-editor-toolbar',
    parserRules: wysihtml5ParserRules
  });
});
