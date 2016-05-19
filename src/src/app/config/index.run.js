(function() {
  'use strict';

  /** @ngInject */
  angular.module('app').run(
    function run($rootScope, ngProgress, scroll) {
      $rootScope.$on('$stateChangeStart', function () {
        ngProgress.start();
      });

      $rootScope.$on('$stateChangeSuccess', function () {
        ngProgress.complete();
        scroll.scrollTo(0, 0.2);
      });
    }
  );

})();
