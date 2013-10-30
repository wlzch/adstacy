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
    alert: Hogan.compile(templates.alert)
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
  Adstacy.events = {
    commentbox: function(event) {
      if (!Adstacy.user) return;
      var $this = $(this);
      var $form = $this.closest('form');
      var template = Adstacy.templates.comment;
      if ($.trim($this.val()).length <= 0) {
        return;
      }

      if (event.which == 13) {
        var $mentions = $('.mentions-autocomplete-list');
        if (!$mentions.is(':hidden')) {
          return;
        } else {
          var $tmpl = $(template.render({
            photo: Adstacy.user.photo,
            username: Adstacy.user.username,
            real_name: Adstacy.user.real_name,
            time: new Date(),
            strtime: new Date().toDateString(),
            content: $this.val()
          }));
          $tmpl.insertBefore($this.closest('.comment'));
          $tmpl.find('time').timeago();
          var serialized = $form.serialize();
          $this.val(''); //bug
          $.post($form.attr('action'), serialized, function(data) {
            // do nothing
          });

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
      $comment.remove();
      $.post($this.attr('data-href'), function(data) {
        // do nothing;
      });
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
      spinner = new Spinner({className: 'spinner', length: 2, width: 2, radius: 2}).spin(this);
      $this.attr('data-loading', 'true');

      $.getJSON(href, function(data) {
        var json = JSON.parse(data);
        var tmpl, left, len, cnt;
        spinner.stop();
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
        Adstacy.alert('success', 'Ctrl + C to copy url', {duration: 400, timeout: 1500});
      }
      event.preventDefault();
      return false;
    },
    deleteAd: function(event) {
      var $this = $(this);
      var href = this.href;
      Adstacy.modal({
        header: $this.attr('data-modal-header'),
        body: $this.attr('data-modal-body'),
        footer: '<button type="button" class="btn btn-primary delete-sure">'+$this.attr('data-modal-delete')+'</button>',
        events: [
          {
            selector: '.delete-sure',
            name: 'click',
            fn: function(event) {
              $this.closest('.advert').remove();
              $(this).closest('.modal').modal('hide');
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
        $ads.find('img.lazy').lazyload();
        $ads.ajaxlink('ads');
        var $commentBoxes = $ads.find('textarea.comment-box');
        $commentBoxes.keydown(Adstacy.events.commentbox);
        $commentBoxes.mentionsInput(Adstacy.options.mentionsInput);
        $ads.find('.load-more-comments').click(Adstacy.events.loadComments);
        $ads.find('a.delete-comment').click(Adstacy.events.deleteComment);
        $ads.find('.btn-share').click(Adstacy.events.share);
        $ads.find('.delete').click(Adstacy.events.deleteAd);
      }
    }
  };
  window.Adstacy = Adstacy;
})(window);
