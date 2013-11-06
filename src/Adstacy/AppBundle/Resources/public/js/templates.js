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
    ',
    sidebar_recommendation: '
      <div id="side-suggest-friend" class="side-aside">
        <h2>People you may know</h2>
        {{#users}}
          <div class="media">
            <a href="#" class="pull-left"><img src="{{ photo }}" width="32" height="32" class="media-object"></a>
            <div class="media-body">
              <div>
                <a class="realname hovercard-user" data-username="{{ username }}" href="#">{{ real_name }}</a>
              </div>
              <a class="btn btn-primary btn-sm">Follow</a>
            </div>
          </div>
        {{/users}}
      </div>
    ',
    hovercarduser: '
      <div class="card-user">
        <img src="{{ photo }}" class="profilepic" width="100" height="100">
        <p>{{ about }}</p>
      </div>
    ',
    user_mini: '
      <div class="users-mini">
        {{#users}}
          <div class="user-mini">
            <img src="{{ photo }}" class="profilepic" width="50" height="50">
            <a href="#" class="realname">{{ real_name }} <span class="username">{{ username }}</span></a>
            <a href="#" class="btn btn-sm btn-primary pull-right">Follow</a>
          </div>
        {{/users}}
      </div>
    ',
    loader: '
      {{#size}}
        <img class="center loader" src="/bundles/adstacyapp/img/loader-alt.gif" width="{{ size }}" height="{{ size }}">
      {{/size}}
      {{^size}}
        <img class="center loader" src="/bundles/adstacyapp/img/loader-alt.gif">
      {{/size}}
    '
  };
})(window);
