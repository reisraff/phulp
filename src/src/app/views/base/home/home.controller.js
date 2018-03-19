(function() {
  'use strict';

  /** @ngInject */
  angular.module('app').controller(
    'HomeController',
    function HomeController(PhulpStats, PluginsResolve, $sce) {
      var _self = this;

      _self.phulp = PhulpStats.package;
      _self.plugins = PluginsResolve;

      _self.code = $sce.trustAsHtml("&lt;?php\n\n"
+ "<span style=\"color:#e28964\">use</span> <span style=\"color:#9b859d\">Phulp\\Output</span> <span style=\"color:#e28964\">as</span> <span style=\"color:#9b859d\">out</span>;\n\n"
+ "<span style=\"color:#aeaeae;font-style:italic\">// Define the default task</span>\n"
+ "<span style=\"color:#3e87e3\">$phulp</span><span style=\"color:#e28964\">-></span>task(<span style=\"color:#65b042\">'default'</span>, <span style=\"color:#99cf50\">function</span> (<span style=\"color:#3e87e3\">$phulp</span>) {\n"
+ "    <span style=\"color:#3e87e3\">$phulp</span><span style=\"color:#e28964\">-></span>start([<span style=\"color:#65b042\">'exec_command'</span>]);\n"
+ "});\n\n"
+ "<span style=\"color:#aeaeae;font-style:italic\">// Define the exec_command task</span>\n"
+ "<span style=\"color:#3e87e3\">$phulp</span><span style=\"color:#e28964\">-></span>task(<span style=\"color:#65b042\">'exec_command'</span>, <span style=\"color:#99cf50\">function</span> (<span style=\"color:#3e87e3\">$phulp</span>) {\n"
+ "    <span style=\"color:#3e87e3\">$return</span> <span style=\"color:#e28964\">=</span> <span style=\"color:#3e87e3\">$phulp</span><span style=\"color:#e28964\">-></span>exec([\n"
+ "        <span style=\"color:#65b042\">'command'</span> <span style=\"color:#e28964\">=></span> <span style=\"color:#65b042\">'ls -lh'</span>,\n"
+ "        <span style=\"color:#65b042\">'cwd'</span> <span style=\"color:#e28964\">=></span> <span style=\"color:#65b042\">'/tmp'</span>\n"
+ "    ]);\n\n"
+ "<span style=\"color:#e28964\">    if</span> (<span style=\"color:#3e87e3\">$return</span>[<span style=\"color:#65b042\">'exit_code'</span>] <span style=\"color:#e28964\">==</span> <span style=\"color:#3387cc\">0</span>) {\n"
+ "        <span style=\"color:#9b859d\">out</span><span style=\"color:#e28964\">::</span>out(<span style=\"color:#9b859d\">out</span><span style=\"color:#e28964\">::</span>colorize(<span style=\"color:#65b042\">'Command Output: '</span> <span style=\"color:#e28964\">.</span> <span style=\"color:#3e87e3\">$return</span>[<span style=\"color:#65b042\">'output'</span>], <span style=\"color:#65b042\">'green'</span>));\n"
+ "    }<span style=\"color:#e28964\"> else</span> {\n"
+ "        <span style=\"color:#9b859d\">out</span><span style=\"color:#e28964\">::</span>out(<span style=\"color:#9b859d\">out</span><span style=\"color:#e28964\">::</span>colorize(<span style=\"color:#65b042\">'Command Output: '</span> <span style=\"color:#e28964\">.</span> <span style=\"color:#3e87e3\">$return</span>[<span style=\"color:#65b042\">'output'</span>], <span style=\"color:#65b042\">'red'</span>));\n"
+ "    }\n"
+ "});\n");

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
