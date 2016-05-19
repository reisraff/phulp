(function() {
  'use strict';

  /** @ngInject */
  angular.module('app').directive(
    'menuScroll',
    function menuScroll($window) {
      return {
        restrict: 'EA',
        scope: {
          items: '=list'
        },
        controller: 'menuScrollController',
        controllerAs: 'controller',
        templateUrl: 'app/packages/menuScroll/menuScroll.html',
        link: function ($scope) {
          var $page = angular.element($window);
          var menu = angular.element(document.getElementById('menuScroll-element'));
          var offsetLimit = menu[0].offsetTop - menu[0].offsetHeight;

          function menuTrigger () {

            if ($window.innerWidth >= 1170) {
              if ($window.pageYOffset >= offsetLimit) {
                $scope.menuScrollMenuFixed = true;
                menu.addClass('is-fixed');
                setTimeout(function() {
                  menu.addClass('animate-children');
                }, 50);
              } else {
                $scope.menuScrollMenuFixed = false;
                menu.removeClass('is-fixed');
                setTimeout(function() {
                  menu.removeClass('animate-children');
                }, 50);
              }
            }

            angular.forEach(document.querySelectorAll('.cd-section'), function(value) {
               var actual = angular.element(value);

                angular.forEach(document.querySelectorAll('.cd-anchor'), function(value) {
                  var actualAnchor = angular.element(value);

                  if (actualAnchor.attr('hash') === actual.attr('id')) {
                    if (
                      ( actual[0].offsetTop - menu[0].offsetHeight <= $window.pageYOffset ) &&
                      ( actual[0].offsetTop +  actual[0].offsetHeight - menu[0].offsetHeight > $window.pageYOffset )
                    ) {
                      actualAnchor.addClass('cd-active');
                    } else {
                      actualAnchor.removeClass('cd-active');
                    }
                  }
                });
            });

            $scope.$apply();
          }

          $page.bind('scroll', menuTrigger);
        }
      };
    }
  );

})();
