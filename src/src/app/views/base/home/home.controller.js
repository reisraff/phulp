(function() {
  'use strict';

  /** @ngInject */
  angular.module('app').controller(
    'HomeController',
    function HomeController(PhulpStats, PluginsResolve) {
      var _self = this;

      _self.phulp = PhulpStats.package;
      _self.plugins = PluginsResolve;

      _self.menu = [
        {
          hash:'section-1',
          label: 'Presentation'
        },
        {
          hash:'section-2',
          label: 'About'
        },
        {
          state:'root.plugins',
          label: 'Plugins'
        },
        {
          url:'https://github.com/reisraff/phulp',
          label: 'Github'
        }
      ];
    }
  );

})();
