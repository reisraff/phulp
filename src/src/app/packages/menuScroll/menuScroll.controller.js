(function() {
  'use strict';

  /** @ngInject */
  angular.module('app').controller(
    'menuScrollController',
    function menuScrollController($window, $location, $scope, scroll) {
      var _self = this;

      $scope.menuScrollMenuFixed = false;

      _self.clicked = function (hash) {
        scroll.scrollTo(scroll.getPosition(hash) - document.getElementById('menuScroll-element').offsetHeight, 0.2);
        angular.element(document.querySelector('.cd-secondary-nav-trigger')).removeClass('menu-is-open');
        angular.forEach(document.getElementById('menuScroll-element').querySelectorAll('ul'), function (value) {
          angular.element(value).removeClass('is-visible');
        });
      };

      _self.menu = function() {
        angular.element(document.querySelector('.cd-secondary-nav-trigger')).toggleClass('menu-is-open');
        angular.forEach(document.getElementById('menuScroll-element').querySelectorAll('ul'), function (value) {
          angular.element(value).toggleClass('is-visible');
        });
      };
    }
  );

})();
