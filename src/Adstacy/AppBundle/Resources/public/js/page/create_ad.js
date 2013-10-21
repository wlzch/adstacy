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
  var $uploadImageContainer = $('#upload-image-container');
  var $progress = $('#progressbar');
  var $imagePreview = $('.image-preview');
  var $modal = $('#create-advert-image-modal');

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
  var previewImage = function(src) {
    var $img = $imagePreview.find('img');
    if ($img.length == 0) {
      $imagePreview.append($('<img>').attr('src', src));
    } else {
      $img.attr('src', src);
    }
  };
  $('#ad_image').fileupload({
    url: Routing.generate('adstacy_app_upload'),
    dataType: 'json',
    imageOrientation: true,
    disableImageResize: /Android(?!.*Chrome)|Opera/.test(window.navigator && navigator.userAgent),
    imageMaxWidth: 1024,
    done: function(e, data) {
      if (data.result.status == 'ok') {
        $.each(data.result.files, function(index, file) {
          previewImage(file.src);
          $('#ad_imagename').val(file.name);
        });
      } else {
        // display error
      }
      $progress.progressbar('destroy');
      $modal.modal('hide');
    },
    fail: function(e, data) {
      $progress.progressbar('destroy');
      $uploadImageContainer.hide();
    },
    progressall: function(e, data) {
      var progress = parseInt(data.loaded / data.total * 100, 10);
      $progress.progressbar({
        value: progress
      });
    },
    start: function(e) {
      $uploadImageContainer.hide();    
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
    parserRules: wysihtml5ParserRules,
    stylesheets: "/bundles/adstacyapp/css/wysiwyg-styles.css"
  });
  $('iframe.wysihtml5-sandbox').wysihtml5_size_matters();
  $modal.on('show.bs.modal', function() {
    $uploadImageContainer.show();
  });
});
