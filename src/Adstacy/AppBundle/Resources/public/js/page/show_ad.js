$(function() {
  var $window = $(window);
  $('#show-advert').ajaxlink('promote_single');
  $('#show-advert-owner').ajaxlink('follow_single');
  $('#show-advert-owner').scrollToFixed({
    marginTop: 90
  });
  var $deleteBtn = $('#delete-comment-btn');
  var $deleteModal = $('#delete-comment-modal');
  $deleteBtn.click(function() {
    var $this = $(this);
    $deleteModal.modal('hide');
    $('#'+$this.attr('data-comment-id')).remove();
    $.post($this.attr('data-href'), function(data) {
      // do nothing
    });
  });
  $deleteModal.on('show.bs.modal', function(e) {
    var $link = $(e.relatedTarget);
    var $comment = $link.closest('.comment');
    var $this = $(this);
    $deleteBtn.attr('data-href', $link.attr('data-href'));
    $deleteBtn.attr('data-comment-id', $comment.attr('id'));
  });
});
