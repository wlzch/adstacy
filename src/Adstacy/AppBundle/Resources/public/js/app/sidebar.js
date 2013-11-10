(function() {
  if (!$.browser.mobile) {
    var $sidebar, $trending, $recommendation, loadTrending, loadRecommendation;
    $sidebar = $('#sidebar');
    $trending = $sidebar.find('#sidebar-trending');
    $recommendation = $sidebar.find('#side-suggest-friend');
    loadTrending = function() {
      $.get(Routing.generate('adstacy_api_trending'), function(data) {
        var json, $html, ads;
        json = JSON.parse(data);
        ads = json.data.ads;
        if (ads && ads.length > 0) {
          $html = $(Adstacy.templates.trending_ad.render({
            'ads': ads
          }));
          $trending.find('.sidebar-trending-advert').remove();
          $trending.hide();
          $trending.append($html);
          $trending.fadeIn(1000);
        }
      });
    };
    loadTrending();
    setInterval(loadTrending, 30000);
    if (Adstacy.user) {
      loadRecommendation = function() {
        $.get(Routing.generate('adstacy_api_recommendation'), function(data) {
          var json, users, $html;
          json = JSON.parse(data);
          users = json.data.users;
          if (users && users.length > 0) {
            $html = $(Adstacy.templates.sidebar_recommendation.render({
              users: users
            }));
            $recommendation.find('.media').remove();
            $recommendation.hide();
            $recommendation.append($html);
            $recommendation.fadeIn();
            Adstacy.follow($recommendation.find('.media'));
            Adstacy.hoveruser($recommendation.find('.hovercard-user'), {openOnLeft: true});
          }
        });
      };
      loadRecommendation();
      setInterval(loadRecommendation, 30000);
    }
  }
})();
