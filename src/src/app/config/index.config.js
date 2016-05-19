(function() {
  'use strict';

  /** @ngInject */
  angular.module('app').config(
    function config(ngProgressProvider, $locationProvider) {
      // ngProgress Configuration
      ngProgressProvider.setColor('#4F5B93');
      ngProgressProvider.setHeight('4px');

      // location configuration
      $locationProvider.hashPrefix('!');
    }
  );

})();
