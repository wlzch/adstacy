(function() {
  var $comment = $('textarea.comment-box');
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

})();
