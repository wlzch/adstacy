(function() {
  var $comment = $('textarea.comment-box');
  var $loadComments = $('.load-more-comments');
  var template = Hogan.compile(templates.comment);
  var $deletes = $('a.delete-comment');
  // must bind enter keydown checking first before mentionsInput because ordering matters.
  var deleteComment = function() {
    var $this, $comment;
    $this = $(this);
    $comment = $this.closest('.comment');
    $comment.remove();
    $.post($this.attr('data-href'), function(data) {
      // do nothing;
    });
    return false;
  };
  $comment.keydown(function(event) {
    var $this = $(this);
    var $form = $this.closest('form');
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
  });
  $comment.mentionsInput({
    onDataRequest: function (mode, query, triggerChar, callback) {
      $.getJSON(Routing.generate('adstacy_api_users', {q: query}), function(data) {
        data = _.filter(data, function(item) { return item.username.toLowerCase().indexOf(query.toLowerCase()) > -1 });

        callback.call(this, data);
      });
    }
  });
  $loadComments.click(function() {
    var $this, $that, $advert, $comments, href, html, spinner;
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
        $tmpl.find('a.delete-comment').click(deleteComment);
      });
    });
    return false;
  });
  $deletes.click(deleteComment);
})();
