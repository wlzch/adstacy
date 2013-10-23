(function() {
  var $comment = $('textarea.comment-box');
  // must bind enter keydown checking first before mentionsInput because ordering matters.
  $comment.keydown(function(event) {
    var $this = $(this);
    var $form = $this.closest('form');
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
        $.post($form.attr('action'), $form.serialize(), function(data) {
          console.log(data);
          this.disabled = false;
        });
        this.disabled = true;

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
