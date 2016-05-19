(function() {
  'use strict';

  /** @ngInject */
  angular.module('app').controller(
    'PluginsListController',
    function PluginsListController($http, PluginsResolve) {
      var _self = this;

      _self.query = null;
      _self.pluginsResolve = PluginsResolve;
      _self.plugins = _self.pluginsResolve.results;

      _self.more = function () {
        $http.get(_self.pluginsResolve.next).then(function (response) {
          _self.pluginsResolve = response.data;
          angular.forEach(_self.pluginsResolve.results, function (value, key) {
            _self.plugins.push(value);
          });
        });
      };

      _self.search = function () {
        if (_self.query !== '') {
          $http.get('https://packagist.org/search.json?tags=phulpplugin&q=' + _self.query).then(function (response) {
            _self.pluginsResolve = response.data;
            _self.plugins = _self.pluginsResolve.results;
          });
        } else {
          $http.get('https://packagist.org/search.json?tags=phulpplugin').then(function (response) {
            _self.pluginsResolve = response.data;
            _self.plugins = _self.pluginsResolve.results;
          });
        }
      };
    }
  );

})();
