(function() {
  'use strict';

  /** @ngInject */
  angular.module('app').service(
    'scroll',
    function scroll() {
      var _self = this;

      _self.getPosition = function (elementId) {
        var e = document.getElementById(elementId);
        var y = e.offsetTop;
        var node = e;

        while (node.offsetParent && node.offsetParent !== document.body) {
          node = node.offsetParent;
          y += node.offsetTop;
        }

        return y;
      };

      _self.getCurrentPosition = function () {
        // Firefox, Chrome, Opera, Safari
        if (self.pageYOffset) {
          return self.pageYOffset;
        }
        // Internet Explorer 6 - standards mode
        if (document.documentElement && document.documentElement.scrollTop) {
          return document.documentElement.scrollTop;
        }
        // Internet Explorer 6, 7 and 8
        if (document.body.scrollTop) {
          return document.body.scrollTop;
        }

        return 0;
      };

      _self.scrollToHash = function(elementId, seconds) {
        _self.scrollTo(_self.getPosition(elementId), seconds);
      };

      _self.scrollTo = function (position, seconds) {
        seconds = seconds <= 0 ? 1000 : seconds * 1000;

        var start = _self.getCurrentPosition();
        var stop = position;
        var distance = stop > start ? stop - start : start - stop;
        var frames = 25;
        var frameSeconds = 0;
        var step = distance / frames;

        for (var i = 1; i <= frames; i++) {
          var nextPosition = stop > start ? (start + i * step) : (start - i * step);

          window.setTimeout('window.scrollTo(0, ' + nextPosition + ')', frameSeconds);
          frameSeconds += seconds / frames;
        }
      };
    }
  );

})();
