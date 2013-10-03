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
      var data = [
        { id:1, name:'Kenneth Auchenberg', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact', value: '@kenneth' },
        { id:2, name:'Jon Froda', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact', value: '@jon' },
        { id:3, name:'Anders Pollas', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact', value: '@anders' },
        { id:4, name:'Kasper Hulthin', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact', value: '@kasper' },
        { id:5, name:'Andreas Haugstrup', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact', value: '@andreas' },
        { id:6, name:'Pete Lacey', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact', value: '@pete' }
      ];

      data = _.filter(data, function(item) { return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1 });

      callback.call(this, data);
    }
  });

})();
