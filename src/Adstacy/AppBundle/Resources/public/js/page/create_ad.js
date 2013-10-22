$(function() {
  var $chooseImage = $('#choose-advert-image');
  var $chooseText = $('#choose-advert-text');
  var $chooseVideo = $('#choose-advert-video');
  var $advertImage = $('#create-advert-image');
  var $advertText = $('#create-advert-text');
  var $advertVideo = $('#create-advert-video');
  var $advertImages = $('#create-advert-images');
  var $advertUrl = $('#create-advert-url');
  var $advertUrlOk = $('#create-advert-url-ok');
  var $advertImageTrigger = $('#create-advert-image-trigger');
  var $advertType = $('#ad_type');
  var $uploadImageContainer = $('#upload-image-container');
  var $progress = $('#progressbar');
  var $imagePreview = $('.image-preview');
  var $modal = $('#create-advert-image-modal');
  var $modalHeader = $modal.find('.modal-header');
  var $modalBody = $modal.find('.modal-body');

  $chooseImage.click(function() {
    $advertImage.removeClass('hide');
    $advertImageTrigger.removeClass('hide');
    $advertText.addClass('hide');
    $advertVideo.addClass('hide');
    $advertType.val('image');
    $imagePreview.show();
  });
  $chooseText.click(function() {
    $advertImage.addClass('hide');
    $advertImageTrigger.addClass('hide');
    $advertText.removeClass('hide');
    $advertVideo.addClass('hide');
    $advertType.val('text');
    $imagePreview.hide();
  });
  $chooseVideo.click(function() {
    $advertImage.addClass('hide');
    $advertImageTrigger.addClass('hide');
    $advertText.addClass('hide');
    $advertVideo.removeClass('hide');
    $advertType.val('video');
    $imagePreview.hide();
  });
  var previewImage = function(file) {
    var $img = $imagePreview.find('img');
    if ($img.length == 0) {
      $imagePreview.append($('<img>').attr('src', file.src));
    } else {
      $img.attr('src', file.src);
    }
    $('#ad_imagename').val(file.name);
    $modal.modal('hide');
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
          previewImage(file);
        });
      } else {
        // display error
      }
      $progress.progressbar('destroy');
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
    $advertUrl.val('');
  });
  var uploadFromUrl = function() {
    $uploadImageContainer.hide();
    var modalBody = $modalBody[0];
    var url = $advertUrl.val();
    var spinner = new Spinner({className: 'spinner spinner-margin'}).spin(modalBody);
    $.getJSON(Routing.generate('adstacy_app_upload_url', {url: url}), function(data) {
      if (data.status == 'ok' && data.files.length > 0) {
        previewImage(data.files[0]);
      } else {
        // show error here
        $uploadImageContainer.show();
      }
      spinner.stop();

      return false;
    });

    return false;
  };
  $advertUrl.keydown(function(e) {
    if (e.which == 13) {
      uploadFromUrl();
      e.preventDefault();
      return false;
    }
  });
  $advertUrlOk.click(function() {
    uploadFromUrl();
  });
});
