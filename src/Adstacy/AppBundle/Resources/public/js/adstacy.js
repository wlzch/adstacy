(function(window) {
  var Adstacy = {};
  var $body = $('body');
  if ($body.attr('data-user')) {
    Adstacy.user = JSON.parse($body.attr('data-user'));
    Adstacy.loggedIn = true;
  }
  Adstacy.templates = {
    comment: Hogan.compile(templates.comment),
    modal: Hogan.compile(templates.modal),
    alert: Hogan.compile(templates.alert),
    hovercarduser: Hogan.compile(templates.hovercarduser),
    sidebar_recommendation: Hogan.compile(templates.sidebar_recommendation),
    user_mini: Hogan.compile(templates.user_mini),
    loader: Hogan.compile(templates.loader)
  };
  Adstacy.modal = function(options) {
    var $modal = $('#adstacy-modal');
    var template = Adstacy.templates.modal;
    options = $.extend({}, {
      header: false,
      body: false,
      footer: false,
      close: true,
      events: []
    }, options);
    $modal.remove();
    $modal = $(template.render({
      header: options.header,
      body: options.body,
      footer: options.footer
    }));
    $body.append($modal);
    $.each(options.events, function(i, _event) {
      $modal.find(_event.selector).on(_event.name, _event.fn);
    });
    $modal.modal({show: true});
    $modal.on('hidden.bs.modal', function() {
      $modal.remove();
    });

    return $modal;
  };
  Adstacy.alert = function(type, message, options) {
    var types = type ? [type] : ['success', 'error'];
    var $container = $('.alert-container');
    var $alert;
    var template = Adstacy.templates.alert;
    options = $.extend({}, {
      duration: 1000,
      timeout: 3000
    }, options);
    if ($container.length <= 0) {
      $container = $('<div class="alert-container">');
      $body.append($container);
    }
    if (message) {
      message = $.isArray(message) ? message[0] : message;
      $alert = $(template.render({type: type, message: message}));
      $container.append($alert);
    } else {
      $.each(types, function() {
        $alert = $('.alert-'+this);
        $container.append($alert);
      });
      $alert = $('.alert');
    }
    $alert.fadeIn(options.duration);
    setTimeout(function() {
      $alert.fadeOut(options.duration, function() {
        $alert.remove();
        if ($container.find('.alert').length <= 0) {
          $container.remove();
        }
      });
    }, options.timeout);
  };
  Adstacy.hoveruser = function($hovercard, options) {
    if (!$.browser.mobile) {
      var template = Adstacy.templates.hovercarduser;
      options = $.extend({}, {
        detailsHTML: '<p>loading...</p>',
        delay: 300,
        onHoverIn: function() {
          var $this, $user, route, $detail;
          $this = $(this);
          $user = $this.find('.hovercard-user');
          $detail = $this.find('.hc-details');
          var route = Routing.generate('adstacy_api_user_show', {username: $user.attr('data-username')});
          $.get(route, function(data) {
            var json, user
            json = JSON.parse(data);
            user = json.data.user;
            $detail.html(template.render({
              photo: user.photo,
              about: user.about
            }));
          });
        }
      }, options);
      $hovercard.hovercard(options);
    }
  };
  Adstacy.follow = function($user, cb) {
    if (Adstacy.user) {
      $user.find('.btn-follow').click(function() {
        var $this, username, href, that;
        $this = $(this);
        that = this;
        username = $this.attr('data-username');
        if ($this.hasClass('follow-user')) {
          href = Routing.generate('adstacy_app_user_follow', {username: username});
        } else if ($this.hasClass('unfollow-user')) {
          href = Routing.generate('adstacy_app_user_unfollow', {username: username});
        } else {
          return false;
        }
        $this.parent().find('.btn-follow').toggleClass('hide');
        $.post(href, function(data) {
          if (cb) cb.call(that, JSON.parse(data));
        });
      });
      $user.find('.unfollow-user').hover(
        function() { $(this).text('Unfollow'); },
        function() { $(this).text('Following'); }
      ).click(
        function() { $(this).text('Following'); }
      );
    }
  };
  Adstacy.broadcast = function($advert) {
    if (Adstacy.user) {
      $advert.find('.btn-promote').click(function() {
        var $this, $parent, $ad, href;
        $this = $(this);
        $ad = $this.closest('.advert');
        $ad.find('.btn-promote').toggleClass('hide');
        if ($this.hasClass('promote')) {
          href = Routing.generate('adstacy_app_ad_promote', {id: $ad.attr('data-id')});
        } else if ($this.hasClass('unpromote')) {
          href = Routing.generate('adstacy_app_ad_unpromote', {id: $ad.attr('data-id')});
        } else {
          return false;
        }
        $.post(href);
      });

      $advert.find('.unpromote').hover(
        function() { $(this).find('.text').text('unbroadcast'); },
        function() { $(this).find('.text').text('broadcasted'); }
      ).click(
        function() { $(this).find('.text').text('broadcasted'); }
      );
    }
  };
  Adstacy.parseMention = function(text) {
    return text.replace(/@([^@ ]+)/g, function(match, contents) {
      return '<a class="mention" href="'+Routing.generate('adstacy_app_user_profile', {username: contents})+'">'+match+'</a>';
    });
  };
  Adstacy.parseUrl = function(text) {
    var regexp = /((http|https|ftp):\/\/(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)/gi;
    return text.replace(regexp, function(match, contents) {
      return '<a class="url" href="'+contents+'">'+match+'</a>';
    });
  };
  Adstacy.toggleExpander = function(object, expander) {
    if (object.height() > 600) {
      expander.removeClass('hide');
    }
    else {
      expander.addClass('hide');
    }
  };
  Adstacy.collapseAd = function(object, isAssigned) {
    object.each(function() {
      var $this = $(this);
      var $expander = $this.children('.advert-expand');
      var $item = $this.children(':first-child');
      Adstacy.toggleExpander($item, $expander);

      if(!isAssigned) {
        $expander.click(function() {
          $this.toggleClass('limit');
          $expander.toggleClass('collapsed');
        });
      }
    });
  };

  Adstacy.events = {
    broadcastcountclick: function() {
      var $this, $ad, $modal, $modalBody;
      $this = $(this);
      $ad = $this.closest('.advert');
      $modal = Adstacy.modal({
        header: 'Broadcasts'
      });
      $modalBody = $modal.find('.modal-body');
      var fn = function(route) {
        $modalBody.html(Adstacy.templates.loader.render());
        $.getJSON(route, function(data) {
          var json, template, $html, html, body, users;
          json = JSON.parse(data);
          template = Adstacy.templates.user_mini;
          $.each(json.data.broadcasts, function() {
            this.show_button = Adstacy.user && this.self !== true;
          });
          $html = $(template.render({
            users: json.data.broadcasts,
            next: json.meta.next,
            logged_in: Adstacy.user,
            next_label: Translator.trans('ads.broadcasts.next')
          }));
          $modalBody.html('');
          $modalBody.append($html);
          $html.find('a.next').click(function() {
            fn($(this).attr('data-href'));
          });
          Adstacy.follow($html.find('.user-mini'));
        });
      };
      fn(Routing.generate('adstacy_api_ad_broadcasts', {id: $ad.attr('data-id')}));
    },
    adimagedblclick: function() {
      var $this, $ad, $btn;
      $this = $(this);
      $ad = $this.closest('.advert');
      $btn = $ad.find('.btn-promote:not(.hide)');
      $btn.click();
    },
    adreportclick: function() {
      $body.click();
      Adstacy.alert('success', Translator.trans('ads.report.success'));
      $.post(this.href);
      return false;
    },
    commentbox: function(event) {
      if (!Adstacy.user) return;
      var $this, $form, template, $mentions, $tmpl, serialized;
      $this = $(this);
      $form = $this.closest('form');
      template = Adstacy.templates.comment;
      if ($.trim($this.val()).length <= 0) {
        return;
      }

      if (event.which == 13) {
        $mentions = $('.mentions-autocomplete-list');
        if (!$mentions.is(':hidden')) {
          return;
        } else {
          $tmpl = $(template.render({
            photo: Adstacy.user.photo,
            username: Adstacy.user.username,
            real_name: Adstacy.user.real_name,
            time: new Date(),
            strtime: new Date().toDateString(),
            content: Adstacy.parseMention(Adstacy.parseUrl($this.val()))
          }));
          $tmpl.insertBefore($this.closest('.comment'));
          $tmpl.find('time').timeago();
          serialized = $form.serialize();
          $this.val(''); //bug
          event.preventDefault();
          $.post($form.attr('action'), serialized);

          return;
        }
      }
      return;
    },
    deleteComment: function(event) {
      if (!Adstacy.user) return;
      var $this, $comment;
      $this = $(this);
      $comment = $this.closest('.comment');
      $comment.fadeOut('slow', function() {
        $comment.remove();
      });
      $.post($this.attr('data-href'));
      return false;
    },
    loadComments: function(event) {
      var $this, $that, $advert, $comments, href, html, spinner, template, adUsername;
      $this = $(this);
      if ($this.attr('data-loading') == 'true') return false;
      $that = $(this).find('a');
      $advert = $(this).closest('.advert');
      template = Adstacy.templates.comment;
      adUsername = $advert.attr('data-username');
      href = $that.attr('data-href');
      $comments = $that.closest('.advert-comments-container').find('.advert-comments');
      html = $that.html();
      $that.html('');
      $this.html(Adstacy.templates.loader.render({size: 15}));
      $this.attr('data-loading', 'true');

      $.getJSON(href, function(data) {
        var json = JSON.parse(data);
        var tmpl, left, len, cnt;
        $that.html(html);
        len = json.data.comments.length;
        cnt = parseInt($that.find('.comments-count').text());
        left = cnt - len;
        if (left <= 0) {
          $this.remove();
        } else {
          $that.find('.comments-count').text(left);
          $that.attr('data-href', json.meta.prev);
        }
        $.each(json.data.comments, function(i, comment) {
          $tmpl = $(template.render({
            id: comment.id,
            photo: comment.user.photo,
            username: comment.user.username,
            real_name: comment.user.real_name,
            time: comment.created,
            strtime: new Date(comment.created).toDateString(),
            content: comment.content,
            delete: Adstacy.user && ((Adstacy.user.username == comment.user.username) || (Adstacy.user.username == adUsername))
          }));
          $comments.prepend($tmpl);
          $tmpl.find('time').timeago();
          $tmpl.find('a.delete-comment').click(Adstacy.events.deleteComment);
          $this.removeAttr('data-loading');
        });
      });
      return false;
    },
    share: function(event) {
      var $share = $(this).parent().parent().next().find('.advert-share');
      if (!$share.hasClass('open')) {
        $share.show();
        $share.find('.url').select();
        $share.addClass('open');
        Adstacy.alert('success', Translator.trans('ctrl_copy'), {duration: 400, timeout: 1500});
      }
      event.preventDefault();
      return false;
    },
    deleteAd: function(event) {
      var $this = $(this);
      var href = this.href;
      Adstacy.modal({
        header: Translator.trans('ads.delete.confirm_title'),
        body: Translator.trans('ads.delete.confirm_message'),
        footer: '<button type="button" class="btn btn-primary delete-sure">'+Translator.trans('ads.delete.button')+'</button>',
        events: [
          {
            selector: '.delete-sure',
            name: 'click',
            fn: function(event) {
              $this.closest('.advert').remove();
              $(this).closest('.modal').modal('hide');
              Adstacy.alert('success', Translator.trans('flash.ad.delete.success'));
              $.post(href, function(data) {
                // do nothing
              });
              return false;
            }
          }
        ]
      });

      return false;
    }
  };
  var filterUser = function(query, users) {
    return _.filter(users, function(user) { return user.username && user.username.toLowerCase().indexOf(query.toLowerCase()) > -1 });
  }
  Adstacy.options = {
    mentionsInput: {
      onDataRequest: function (mode, query, triggerChar, callback) {
        var users, that;
        that = this;
        if (query && query.length >= 2) {
          if (Adstacy.user) {
            users = $.jStorage.get('adstacy.network');
            if (!users) {
              $.getJSON(Routing.generate('adstacy_api_network'), function(data) {
                $.jStorage.set('adstacy.network', data);
                $.jStorage.setTTL('adstacy.network', 864000);
                users = filterUser(query, users);
                callback.call(that, users);
              });
            } else {
              users = filterUser(query, users);
              callback.call(that, users);
            }
          }
          $.getJSON(Routing.generate('adstacy_api_users', {q: query}), function(data) {
            users = users.concat(filterUser(query, data));

            callback.call(that, users);
          });
        }
      }
    },
    jscrollAd: {
      callback: function(event) {
        var $ads = $('.jscroll-added:last .advert');
        $ads.find('img.lazy').lazyload({
          load: function() {
            var $parent = $(this).parent('.advert-img');
            $parent.css('height', 'auto');
            Adstacy.toggleExpander($parent, $parent.siblings());
          }
        });
        var $commentBoxes = $ads.find('textarea.comment-box');
        $commentBoxes.keydown(Adstacy.events.commentbox);
        $commentBoxes.mentionsInput(Adstacy.options.mentionsInput);
        Adstacy.broadcast($ads);
        $ads.find('.load-more-comments').click(Adstacy.events.loadComments);
        $ads.find('.timeago').timeago();
        $ads.find('.btn-share').click(Adstacy.events.share);
        $ads.find('.advert-img').dblclick(Adstacy.events.adimagedblclick);
        Adstacy.collapseAd($ads.find('.limit'), false);
        Adstacy.hoveruser($ads.find('.hovercard-user'));
        $ads.find('.delete').click(Adstacy.events.deleteAd);
        $ads.find('.report').click(Adstacy.events.adreportclick);
        $ads.find('a.delete-comment').click(Adstacy.events.deleteComment);
        $ads.find('.advert-broadcasts .count').click(Adstacy.events.broadcastcountclick);
      }
    },
    jscrollUser: {
      callback: function(event) {
        var $users = $('.jscroll-added:last .user');
        Adstacy.follow($users);
      }
    }
  };
  window.Adstacy = Adstacy;
})(window);
