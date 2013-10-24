(function() {
  var $comment = $('textarea.comment-box');
  var $loadComments = $('.load-more-comments');
  var template = Hogan.compile(templates.comment);
  // must bind enter keydown checking first before mentionsInput because ordering matters.
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
    var $this = $(this);
    var href = $this.attr('data-href');
    var $comments = $this.closest('.advert-comments-container').find('.advert-comments');
    var html = $this.html();
    $this.html('');
    var spinner = new Spinner({className: 'spinner', length: 2, width: 2, radius: 2}).spin(this);
    $.getJSON(href, function(data) {
      var json = JSON.parse(data);
      var tmpl, left, len, cnt;
      $this.html(html);
      len = json.data.comments.length;
      cnt = parseInt($this.find('.comments-count').text());
      left = cnt - len;
      if (left <= 0) {
        $this.remove();
      } else {
        $this.find('.comments-count').text(left);
        $this.attr('data-href', json.meta.prev);
      }
      spinner.stop();
      $.each(json.data.comments, function(i, comment) {
        $tmpl = $(template.render({
          photo: comment.user.photo,
          username: comment.user.username,
          real_name: comment.user.real_name,
          time: comment.created,
          strtime: new Date(comment.created).toDateString(),
          content: comment.content
        }));
        $comments.prepend($tmpl);
        $tmpl.find('time').timeago();
      });
    });
    return false;
  });
})();
