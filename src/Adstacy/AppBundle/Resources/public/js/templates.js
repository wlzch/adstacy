(function(window) {
  window.templates = {
    comment: '
      <div class="comment media">
        <div class="pull-left">
          <img src="{{ photo }}" alt="" width="32" height="32" class="media-object">
        </div>
        <div class="media-body">
          <div class="media-heading">
            <a class="realname" href="/{{ username }}">{{ real_name }}
              <span class="username">@{{ username }}</small>
            </a>
            &middot; <time class="timeago" datetime="{{ time }}">{{ strtime }}</time>
            {{#delete}}
              <a href="javascript:;" data-href="/comments/{{ id }}/delete" class="pull-right delete-comment">&times;</a>
            {{/delete}}
          </div>
          <p>{{ content }}</p>
        </div>
      </div>
    ',
    modal: '
      <div class="modal fade" id="adstacy-modal" tabindex="-1" role="dialog"> 
          <div class="modal-dialog">
            <div class="modal-content">
              {{#header}}
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{{{header}}}</h4>
              </div>
              {{/header}}
              <div class="modal-body">
                {{{ body }}}
              </div>
              {{#footer}}
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                {{{ footer }}}
              </div>
              {{/footer}}
            </div>
          </div>
        </div>
    ',
    alert: '
      <div class="alert alert-{{ type }} alert-dismissable">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <p>{{{ message }}}</p>
      </div>
    '
  };
})(window);
