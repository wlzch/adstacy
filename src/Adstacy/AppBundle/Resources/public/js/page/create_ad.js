$(function() {
  var $container, $form, $chooseImage, $chooseText, $chooseVideo, $advertImage, $advertText, $advertImages, $advertUrl,
    $advertUrlOk, $advertImageTrigger, $inputType, $uploadImageContainer, $adImagesContainer, $progress, $imagePreview, $progressbar,
    $modal, $modalHeader, $modalBody, $inputVideo, $inputTitle, $inputDescription, $inputTags, $inputImage, $youtube, $save;

  $container = $('#create-advert');
  $form = $container.find('form');
  $chooseImage = $container.find('#choose-advert-image');
  $chooseText = $container.find('#choose-advert-text');
  $chooseVideo = $container.find('#choose-advert-video');
  $advertImage = $container.find('#create-advert-image');
  $advertText = $container.find('#create-advert-text');
  $advertVideo = $container.find('#create-advert-video');
  $advertImages = $container.find('#create-advert-images');
  $advertUrl = $container.find('#create-advert-url');
  $advertUrlOk = $container.find('#create-advert-url-ok');
  $advertImageTrigger = $container.find('#create-advert-image-trigger');
  $uploadImageContainer = $container.find('#upload-image-container');
  $adImagesContainer = $container.find('#ad-images-container');
  $progress = $container.find('#progressbar');
  $progressbar = $progress.find('.progress-bar');
  $imagePreview = $container.find('.image-preview');
  $modal = $container.find('#create-advert-image-modal');
  $modalHeader = $modal.find('.modal-header');
  $modalBody = $modal.find('.modal-body');
  $inputImage = $container.find('#ad_imagename');
  $inputVideo = $container.find('#ad_youtubeUrl');
  $inputType = $container.find('#ad_type');
  $inputTitle = $container.find('#ad_title');
  $inputDescription = $container.find('#ad_description');
  $inputTags = $container.find('#ad_tags');
  $youtube = $container.find('#ytplayer');
  $save = $container.find('#ad_save');

  $chooseImage.click(function() {
    $(this).removeClass('btn-default').addClass('btn-primary');
    $chooseText.removeClass('btn-primary').addClass('btn-default');
    $chooseVideo.removeClass('btn-primary').addClass('btn-default');
    $advertImage.removeClass('hide');
    $adImagesContainer.removeClass('hide');
    $advertImageTrigger.removeClass('hide');
    $advertText.addClass('hide');
    $advertVideo.addClass('hide');
    $inputType.val('image');
    $imagePreview.show();
    $save.text(Translator.trans('form_ad.submit_image'));
  });
  $chooseText.click(function() {
    $(this).removeClass('btn-default').addClass('btn-primary');
    $chooseImage.removeClass('btn-primary').addClass('btn-default');
    $chooseVideo.removeClass('btn-primary').addClass('btn-default');
    $advertImage.addClass('hide');
    $adImagesContainer.addClass('hide');
    $advertImageTrigger.addClass('hide');
    $advertText.removeClass('hide');
    $advertVideo.addClass('hide');
    $inputType.val('text');
    $imagePreview.hide();
    $save.text(Translator.trans('form_ad.submit_text'));
  });
  $chooseVideo.click(function() {
    $(this).removeClass('btn-default').addClass('btn-primary');
    $chooseImage.removeClass('btn-primary').addClass('btn-default');
    $chooseText.removeClass('btn-primary').addClass('btn-default');
    $advertImage.addClass('hide');
    $adImagesContainer.addClass('hide');
    $advertImageTrigger.addClass('hide');
    $advertText.addClass('hide');
    $advertVideo.removeClass('hide');
    $inputType.val('youtube');
    $imagePreview.hide();
    $save.text(Translator.trans('form_ad.submit_video'));
  });
  var previewImage = function(file) {
    var $img = $imagePreview.find('img');
    if ($img.length == 0) {
      $imagePreview.append($('<img>').attr('src', file.src));
    } else {
      $img.attr('src', file.src);
    }
    $inputImage.val(file.name);
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
    },
    fail: function(e, data) {
      $uploadImageContainer.hide();
    },
    always: function(e, data) {
      $progress.hide();
      $progress.css('width', '0%');
    },
    progressall: function(e, data) {
      var progress = parseInt(data.loaded / data.total * 100, 10);
      $progressbar.css('width', progress+'%');
    },
    start: function(e) {
      $uploadImageContainer.hide(); 
      $progress.show();
    }
  }).prop('disabled', !$.support.fileInput)
  .parent().addClass($.support.fileInput ? undefined : 'disabled')
  $inputTags.tagsinput({
    itemText: function(item) {
      if (item && item.length > 0) {
        if (item[0] != '#') return '#'+item;
        return item;
      }
      return false;
    },
    confirmKeys: [13, 32, 44, 188],
    typeahead: {
      name: 'inputtags',
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
  editor.on('load', function() {
    $(editor.composer.iframe).wysihtml5_size_matters();
  });
  $modal.on('show.bs.modal', function() {
    $uploadImageContainer.show();
    $advertUrl.val('');
  });
  var uploadFromUrl = function() {
    $uploadImageContainer.hide();
    var modalBody = $modalBody[0];
    var url = $advertUrl.val();
    $uploadImageContainer.hide();
    $modalBody.append(Adstacy.templates.loader.render());
    $.getJSON(Routing.generate('adstacy_app_upload_url', {url: url}), function(data) {
      if (data.status == 'ok' && data.files.length > 0) {
        previewImage(data.files[0]);
      } else {
        Adstacy.alert('error', data.errors);
        $uploadImageContainer.show();
      }
      $modalBody.find('.loader').remove();

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
  (function() {
    var insertImage = function(id) {
      if ($youtube.find('img').length > 0) {
        $youtube.find('img').attr('src', 'http://img.youtube.com/vi/'+id+'/0.jpg');
      } else {
        $youtube.append($('<img>').attr('src', 'http://img.youtube.com/vi/'+id+'/0.jpg'));
      }
    };
    var lastVal = $inputVideo.val();
    var player, matches, val, yt;
    yt = $youtube[0];
    if (lastVal != '') {
      matches = /https?:\/\/(?:[0-9A-Z-]+\.)?(?:youtu\.be\/|youtube\.com\S*[^\w\-\s])([\w\-]{11})(?=[^\w\-]|$)(?![?=&+%\w.-]*(?:['"][^<>]*>|<\/a>))[?=&+%\w.-]*/ig.exec(lastVal);
      insertImage(matches[1]);
    }
    $inputVideo.keyup(function(e) {
      if (!e.ctrlKey && !e.altKey) {
        val = $(this).val();
        if (lastVal != val) {
          lastVal = val;
          matches = /https?:\/\/(?:[0-9A-Z-]+\.)?(?:youtu\.be\/|youtube\.com\S*[^\w\-\s])([\w\-]{11})(?=[^\w\-]|$)(?![?=&+%\w.-]*(?:['"][^<>]*>|<\/a>))[?=&+%\w.-]*/ig.exec(val);
          if (matches) {
            insertImage(matches[1]);
          }
        }
      }
    });
  })();
  (function() {
    // validation
    var err = function(message) {
      Adstacy.alert('error', message, {timeout: 5000});

      return false;
    }
    $form.submit(function() {
      var $this, type, tags;
      $this = $(this);
      type = $inputType.val();
      tags = $inputTags.val();
      if (type == 'image') {
        if (!$inputImage.val()) return err(Translator.trans('ad.image.not_null'));
      } else if (type == 'text') {
        if (!$inputTitle.val()) return err(Translator.trans('ad.title.not_blank'));
        if (!$inputDescription.val().trim()) return err(Translator.trans('ad.description.not_blank'));
      } else if (type == 'youtube') {
        if (!$inputVideo.val()) return err(Translator.trans('ad.youtube_url.not_blank'));
        matches = /https?:\/\/(?:[0-9A-Z-]+\.)?(?:youtu\.be\/|youtube\.com\S*[^\w\-\s])([\w\-]{11})(?=[^\w\-]|$)(?![?=&+%\w.-]*(?:['"][^<>]*>|<\/a>))[?=&+%\w.-]*/ig.exec($inputVideo.val());
        if (!matches) return err(Translator.trans('ad.youtube_url.not_valid'));
      } else {
        return err(Translator.trans('ad.error'));
      }
      if (!tags || tags.split(',').length <= 0) return err(Translator.trans('ad.tags.min_count', {'{{ limit }}': 1}));

      var $btn = $this.find('.submit-disable-btn');
      var text = $btn.attr('data-disable-text') || Translator.trans('submitting');
      $btn.attr('disabled', 'disabled');
      $btn.html(text);
    });
    $
  })();
});
