(function(window) {
  window.templates = {
    comment: '
      <div class="comment media" data-id="{{ id }}">
        <div class="pull-left">
          <img src="{{ photo }}" alt="" width="32" height="32" class="media-object">
        </div>
        <div class="media-heading">
          <a class="realname" href="/{{ username }}">{{ real_name }}
            <span class="username">@{{ username }}</small>
          </a>
          &middot; <time class="timeago" datetime="{{ time }}">{{ strtime }}</time>
          {{#delete}}
            <a href="javascript:;" data-href="/comments/{{ id }}/delete" class="pull-right delete-comment">&times;</a>
          {{/delete}}
        </div>
        <div class="media-body">
          <p>{{{ content }}}</p>
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
      {{#users}}
        <div class="media">
          <a href="#" class="pull-left"><img src="{{ photo }}" width="32" height="32" class="media-object"></a>
          <div class="media-body">
            <div>
              <a class="realname hovercard-user text-overflow" data-username="{{ username }}" href="/{{ username }}">{{ real_name }}</a>
            </div>
            <button data-username="{{ username }}" class="btn btn-sm btn-follow btn-primary follow-user {{#followed}}hide{{/followed}}">Follow</button>
            <button data-username="{{ username }}" class="btn btn-sm btn-follow btn-success unfollow-user {{^followed}}hide{{/followed}}">Following</button>
          </div>
        </div>
      {{/users}}
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
            <a href="/{{ username }}" class="realname">{{ real_name }} <span class="username">{{ username }}</span></a>
            {{#show_button}}
              <button data-username="{{ username }}" class="btn btn-sm btn-follow btn-primary follow-user pull-right {{#followed}}hide{{/followed}}">Follow</button>
              <button data-username="{{ username }}" class="btn btn-sm btn-follow btn-success unfollow-user pull-right {{^followed}}hide{{/followed}}">Following</button>
            {{/show_button}}
          </div>
        {{/users}}
        {{#next}}
            <a href="javascript:;" class="next" data-href="{{ next }}">{{ next_label }}</a>
        {{/next}}
      </div>
    ',
    loader: '
      {{#size}}
        <img class="center loader" src="/bundles/adstacyapp/img/loader-alt.gif" width="{{ size }}" height="{{ size }}">
      {{/size}}
      {{^size}}
        <img class="center loader" src="/bundles/adstacyapp/img/loader-alt.gif">
      {{/size}}
    ',
    trending_ad: '
      {{#ads}}
        <div class="sidebar-trending-advert">
          {{#is_image}}
            <a class="sidebar-trending-img" href="/ads/{{ id }}">
              <img src="{{ image }}" width="{{ width }}" height="{{ height }}" target="_blank">
            </a>
          {{/is_image}}
          {{#is_text}}
            <a class="sidebar-trending-text" href="/ads/{{ id }}" target="_blank">
              <h3>{{ title }}</h3>
            </a>
          {{/is_text}}
          {{#is_youtube}}
            <a class="sidebar-trending-youtube" href="/ads/{{ id }}" target="_blank">
              <img src="http://img.youtube.com/vi/{{ youtube_id }}/0.jpg" width="260" height="195">
            </a>
          {{/is_youtube}}
        </div>
      {{/ads}}
    '
  };
})(window);
