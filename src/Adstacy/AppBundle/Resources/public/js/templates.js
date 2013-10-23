(function(window) {
  window.templates = {
    comment: '
      <div class="media">
        <div class="pull-left">
          <img src="{{ photo }}" alt="" width="32" height="32" class="media-object">
        </div>
        <div class="media-body">
          <p class="comment-box">{{ content }}</p>
        </div>
      </div>
    '
  };
})(window);
