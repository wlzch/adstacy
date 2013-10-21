$(function() {
  var $chooseImage = $('#choose-advert-image');
  var $chooseText = $('#choose-advert-text');
  var $chooseVideo = $('#choose-advert-video');
  var $advertImage = $('#create-advert-image');
  var $advertText = $('#create-advert-text');
  var $advertVideo = $('#create-advert-video');
  var $advertImages = $('#create-advert-images');
  var $advertImageTrigger = $('#create-advert-image-trigger');
  var $advertType = $('#ad_type');
  $chooseImage.click(function() {
    $advertImage.removeClass('hide');
    $advertImageTrigger.removeClass('hide');
    $advertText.addClass('hide');
    $advertVideo.addClass('hide');
    $advertType.val('image');
  });
  $chooseText.click(function() {
    $advertImage.addClass('hide');
    $advertImageTrigger.addClass('hide');
    $advertText.removeClass('hide');
    $advertVideo.addClass('hide');
    $advertType.val('text');
  });
  $chooseVideo.click(function() {
    $advertImage.addClass('hide');
    $advertImageTrigger.addClass('hide');
    $advertText.addClass('hide');
    $advertVideo.removeClass('hide');
    $advertType.val('video');
  });
  $('#ad_image').fileupload({
    url: Routing.generate('adstacy_app_upload'),
    dataType: 'json',
    imageOrientation: true,
    disableImageResize: /Android(?!.*Chrome)|Opera/.test(window.navigator && navigator.userAgent),
    imageMaxWidth: 1024,
    done: function(e, data) {
      if (data.result.status == 'ok') {
        $.each(data.result.files, function(index, file) {
          $('.image-preview img').attr('src', file.src);
          $('#ad_imagename').val(file.name);
        });
      } else {
        // display error
      }
      $('#progressbar').progressbar('destroy');
    },
    fail: function(e, data) {
      $('#progressbar').progressbar('destroy');
    },
    progressall: function(e, data) {
      var progress = parseInt(data.loaded / data.total * 100, 10);
      $('#progressbar').progressbar({
        value: progress
      });
    }
  }).prop('disabled', !$.support.fileInput)
  .parent().addClass($.support.fileInput ? undefined : 'disabled')
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
        url: Routing.generate('adstacy_api_tags', {q: '_QUERY_'}),
        wildcard: '_QUERY_'
      }
    }
  });
  $('.tt-query').blur(function() {
    $(this).val('');
  });
  var editor = new wysihtml5.Editor('ad_description', {
    toolbar: 'wysihtml5-editor-toolbar',
    parserRules: wysihtml5ParserRules
  });
  $('iframe.wysihtml5-sandbox').wysihtml5_size_matters();
});
