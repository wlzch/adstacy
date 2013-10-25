(function() {
  var $comment = $('textarea.comment-box');
  var $loadComments = $('.load-more-comments');
  var $deletes = $('a.delete-comment');
  // must bind enter keydown checking first before mentionsInput because ordering matters.
  $comment.keydown(Adstacy.events.commentbox);
  $comment.mentionsInput(Adstacy.options.mentionsInput);
  $loadComments.click(Adstacy.events.loadComments);
  $deletes.click(Adstacy.events.deleteComment);
})();
