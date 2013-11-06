(function() {
  var $comment = $('textarea.comment-box');
  var $loadComments = $('.load-more-comments');
  var $deletes = $('a.delete-comment');
  $loadComments.click(Adstacy.events.loadComments);
  if (Adstacy.user) {
    // must bind enter keydown checking first before mentionsInput because ordering matters.
    $comment.keydown(Adstacy.events.commentbox);
    $comment.mentionsInput(Adstacy.options.mentionsInput);
    $deletes.click(Adstacy.events.deleteComment);
  }
})();
