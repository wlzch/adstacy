(function() {
  var $comment = $('textarea.comment-box');
  // must bind enter keydown checking first before mentionsInput because ordering matters.
  $comment.keydown(function(event) {
    var $this = $(this);
    if ($.trim($this.val()).length <= 0) {
      return ;
    }

    if (event.which == 13 && event.shiftKey) {
      return;
    } else if (event.which == 13) {
      var $mentions = $('.mentions-autocomplete-list');
      if (!$mentions.is(':hidden')) {
        return;
      } else {
        this.blur();
        this.form.submit();
        this.disabled = true;

        return true;
      }
    }
    return;
  });
  $comment.mentionsInput({
    onDataRequest: function (mode, query, triggerChar, callback) {
      $.getJSON(Routing.generate('adstacy_app_api_users', {q: query}), function(data) {
        data = _.filter(data, function(item) { return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1 });

        callback.call(this, data);
      });
    }
  });

})();
