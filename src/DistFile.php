<?php

namespace Phulp;

class DistFile
{
    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string|null $dir
     */
    private $dir = null;

    /**
     * @var mixed $content
     */
    private $content;

    /**
     * @param string $name the relative name PS: Never.... absolute name
     * @param mixed $content
     */
    public function __construct($name, $content)
    {
        $this->name = $name;
        $this->setDir($name);
        $this->content = $content;
    }

    /**
     * Gets the value of name.
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the value of name.
     *
     * @param string $name $name the name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        $this->setDir($name);

        return $this;
    }

    /**
     * Gets the value of content.
     *
     * @return mixed $content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets the value of content.
     *
     * @param mixed $content $content the content
     *
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Gets the value of dir.
     *
     * @return string $dir
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * @param string $name
     */
    private function setDir($name)
    {
        if (preg_match('/\//', $name)) {
            $this->dir = substr($name, 0, strrpos($name, '/'));
        }
    }
}