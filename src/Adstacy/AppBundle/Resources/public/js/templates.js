(function(window) {
  window.templates = {
    comment: '
      <div class="media">
        <div class="pull-left">
          <img src="{{ photo }}" alt="" width="32" height="32" class="media-object">
        </div>
        <div class="media-body">
          <p class="media-heading">
            <a class="realname" href="/{{ username }}">{{ real_name }}</a>
            <span class="username">@{{ username }}</small>
            ~ <time class="timeago" datetime="{{ time }}">{{ strtime }}</time>
          </p>
          <p>{{ content }}</p>
        </div>
      </div>
    '
  };
})(window);
