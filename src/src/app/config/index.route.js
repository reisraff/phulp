(function() {
  'use strict';

  /** @ngInject */
  angular.module('app').config(
    function routerConfig($stateProvider, $urlRouterProvider) {
      /** @ngInject */
      var PhulpStats = function ($http) {
        return $http.get('https://packagist.org/packages/reisraff/phulp.json').then(function (response) {
          return response.data;
        });
      };

      /** @ngInject */
      var PluginsResolve = function ($http) {
        return $http.get('https://packagist.org/search.json?tags=phulpplugin').then(function (response) {
          return response.data;
        });
      };

      var states = [
        {
          stateName: 'root',
          stateData: {
            abstract: true,
            templateUrl: 'app/views/base/base.html'
          }
        },
        {
          stateName: 'root.home',
          stateData: {
            url: '/home',
            views: {
              'content': {
                templateUrl: 'app/views/base/home/home.html',
                controller: 'HomeController',
                controllerAs: 'controller',
                resolve: {
                  PhulpStats: PhulpStats,
                  PluginsResolve: PluginsResolve
                }
              }
            }
          }
        },
        {
          stateName: 'root.plugins',
          stateData: {
            url: '/plugins',
            views: {
              'content': {
                templateUrl: 'app/views/base/plugins/list/list.html',
                controller: 'PluginsListController',
                controllerAs: 'controller',
                resolve: {
                  'PluginsResolve': PluginsResolve
                }
              }
            }
          }
        }
      ];

      angular.forEach (states, function (state) {
        $stateProvider.state(state.stateName, state.stateData);
      });

      $urlRouterProvider.otherwise('/home');
    }
  );

})();
