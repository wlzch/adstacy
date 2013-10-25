(function(window) {
  var Adstacy = {};
  var $body = $('body');
  if ($body.attr('data-user')) {
    Adstacy.user = JSON.parse($body.attr('data-user'));
  }
  Adstacy.templates = {
    comment: Hogan.compile(templates.comment)
  };
  Adstacy.events = {
    commentbox: function(event) {
      var $this = $(this);
      var $form = $this.closest('form');
      var template = Adstacy.templates.comment;
      if ($.trim($this.val()).length <= 0) {
        return;
      }

      if (event.which == 13) {
        var $mentions = $('.mentions-autocomplete-list');
        if (!$mentions.is(':hidden')) {
          return;
        } else {
          var $tmpl = $(template.render({
            photo: Adstacy.user.photo,
            username: Adstacy.user.username,
            real_name: Adstacy.user.real_name,
            time: new Date(),
            strtime: new Date().toDateString(),
            content: $this.val()
          }));
          $tmpl.insertBefore($this.closest('.media'));
          $tmpl.find('time').timeago();
          var serialized = $form.serialize();
          $this.val(''); //bug
          $.post($form.attr('action'), serialized, function(data) {
            // do nothing
          });

          return;
        }
      }
      return;
    },
    deleteComment: function(event) {
      var $this, $comment;
      $this = $(this);
      $comment = $this.closest('.comment');
      $comment.remove();
      $.post($this.attr('data-href'), function(data) {
        // do nothing;
      });
      return false;
    },
    loadComments: function(event) {
      var $this, $that, $advert, $comments, href, html, spinner, template;
      template = Adstacy.templates.comment;
      $this = $(this);
      $that = $(this).find('a');
      $advert = $(this).closest('.advert');
      var adUsername = $advert.attr('data-username');
      href = $that.attr('data-href');
      $comments = $that.closest('.advert-comments-container').find('.advert-comments');
      html = $that.html();
      $that.html('');
      spinner = new Spinner({className: 'spinner', length: 2, width: 2, radius: 2}).spin(this);
      $.getJSON(href, function(data) {
        var json = JSON.parse(data);
        var tmpl, left, len, cnt;
        spinner.stop();
        $that.html(html);
        len = json.data.comments.length;
        cnt = parseInt($that.find('.comments-count').text());
        left = cnt - len;
        if (left <= 0) {
          $this.remove();
        } else {
          $that.find('.comments-count').text(left);
          $that.attr('data-href', json.meta.prev);
        }
        $.each(json.data.comments, function(i, comment) {
          $tmpl = $(template.render({
            id: comment.id,
            photo: comment.user.photo,
            username: comment.user.username,
            real_name: comment.user.real_name,
            time: comment.created,
            strtime: new Date(comment.created).toDateString(),
            content: comment.content,
            delete: (Adstacy.user.username == comment.user.username) || (Adstacy.user.username == adUsername)
          }));
          $comments.prepend($tmpl);
          $tmpl.find('time').timeago();
          $tmpl.find('a.delete-comment').click(Adstacy.events.deleteComment);
        });
      });
      return false;
    }
  };
  Adstacy.options = {
    mentionsInput: {
      onDataRequest: function (mode, query, triggerChar, callback) {
        $.getJSON(Routing.generate('adstacy_api_users', {q: query}), function(data) {
          data = _.filter(data, function(item) { return item.username.toLowerCase().indexOf(query.toLowerCase()) > -1 });

          callback.call(this, data);
        });
      }
    },
    jscrollAd: {
      callback: function(event) {
        var $ads = $('.jscroll-added:last .advert');
        $ads.find('img.lazy').lazyload();
        $ads.ajaxlink('ads');
        var $commentBoxes = $ads.find('textarea.comment-box');
        $commentBoxes.keydown(Adstacy.events.commentbox);
        $commentBoxes.mentionsInput(Adstacy.options.mentionsInput);
        $ads.find('.load-more-comments').click(Adstacy.events.loadComments);
        $ads.find('.a.delete-comment').click(Adstacy.events.deleteComment);
      }
    }
  };
  window.Adstacy = Adstacy;
})(window);
