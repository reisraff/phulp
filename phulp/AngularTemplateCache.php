<?php

class AngularTemplateCache implements \Phulp\PipeInterface
{
    private $filename;
    private $options = [
        'module' => null,
    ];

    public function __construct($filename, array $options)
    {
        $this->filename = $filename;
        $this->options = array_merge($this->options, $options);
    }

    public function execute(\Phulp\Source $src)
    {
        $templateHeader = 'angular.module("<%= module %>"<%= standalone %>).run(["$templateCache", function($templateCache) {';
        $templateBody = '$templateCache.put("<%= url %>","<%= contents %>");';
        $templateFooter = '}]);';

        $puts = [];
        foreach ($src->getDistFiles() as $key => $file) {
            $root = rtrim($this->options['root'], '/') . DIRECTORY_SEPARATOR;
            $url = $root . $file->getRelativepath() . '/' . $file->getName();
            $content = str_replace('"', '\"', $file->getContent());

            $puts[] = preg_replace(
                ['/<%= url %>/', '/<%= contents %>/'],
                [$url, $content],
                $templateBody
            );

            $src->removeDistFile($key);
        }

        $content = sprintf(
            '%s%s%s',
            preg_replace(['/<%= module %>/', '/<%= standalone %>/'], [$this->options['module'], null], $templateHeader),
            implode(PHP_EOL, $puts),
            $templateFooter
        );

        $src->addDistFile(new \Phulp\DistFile($content, $this->filename));
    }
}
