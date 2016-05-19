(function() {
  'use strict';

  /** @ngInject */
  angular.module('app').controller(
    'HomeController',
    function HomeController(ReadmeResolve) {
      var _self = this;

      _self.readme = ReadmeResolve;

      _self.menu = [
        {
          hash:'section-1',
          label: 'About',
          bold: true
        },
        {
          state:'/plugins',
          label: 'Plugins',
          bold: true
        },
        {
          url:'https://github.com/reisraff/phulp',
          label: 'Github',
          bold: true
        }
      ];
    }
  );

})();
