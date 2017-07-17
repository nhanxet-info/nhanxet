(function($) {
Drupal.behaviors.fc_log = {
  attach: function (context, settings) {
//    jQuery.repeat(180000, function() {
    jQuery.repeat(150000, function() {
      var query = window.location.search.substring(1);
      var qs = parse_query_string(query);
      var next_page = parseInt(qs.limit);
      var limit = parseInt(next_page) + 20;
      var url = qs.url;
//      window.location.href = 'http://nhanxet.local/get_content?limit=' + limit + '&' + url + '=' + next_page;

      var las_url = 'http://nhanxet.local/get_content?limit=' + limit + '&url=' + url + '=' + next_page;
      console.log('url: ' + url);
      console.log('limit: '+ limit);
      console.log('las_url: ' + las_url);
//      window.location.href = las_url;
      
    });
    function parse_query_string(query) {
      var vars = query.split("&");
      var query_string = {};
      for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split("=");
        // If first entry with this name
        if (typeof query_string[pair[0]] === "undefined") {
          query_string[pair[0]] = decodeURIComponent(pair[1]);
          // If second entry with this name
        } else if (typeof query_string[pair[0]] === "string") {
          var arr = [query_string[pair[0]], decodeURIComponent(pair[1])];
          query_string[pair[0]] = arr;
          // If third or later entry with this name
        } else {
          query_string[pair[0]].push(decodeURIComponent(pair[1]));
        }
      }
      return query_string;
    }
  }
};
})(jQuery);