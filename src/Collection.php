<?php

namespace Phulp;

use Doctrine\Common\Collections\ArrayCollection;

class Collection extends ArrayCollection
{
    /**
     * Type of the elements.
     *
     * @var array
     */
    protected $type = null;

    /**
     * Exception error message
     *
     * @var string
     */
    private static $exceptionErrorMessage = 'Argument %d passed to %s::%s() must be type of "%s", "%s" given';

    /**
     * Initializes a new Collection.
     *
     * @param array $elements
     *
     * @throws \UnexpectedValueException
     */
    public function __construct(array $collection = [], $type = null)
    {
        $this->type = $type;

        foreach ($collection as $item) {
            if (! $this->checkType($item)) {
                throw new \UnexpectedValueException('Mixed types in array');
            }
        }

        parent::__construct($collection);
    }

    /**
     * Checks the immutability of the elements type
     *
     * @return boolean
     */
    protected function checkType($item)
    {
        $type = $this->getItemType($item);
        if (! $this->type) {
            $this->type = $type;

            return true;
        }

        if ($this->type != $type) {
            return false;
        }

        return true;
    }

    /**
     * Returns which is the type of the item.
     *
     * @param mixed $item
     *
     * @return string
     */
    protected function getItemType($item)
    {
        return is_object($item) ? get_class($item) : gettype($item);
    }

    /**
     * Returns which is the type of the elements.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \UnexpectedValueException
     */
    public function set($key, $value)
    {
        if (! $this->checkType($value)) {
            throw new \UnexpectedValueException(
                sprintf(
                    self::$exceptionErrorMessage,
                    2,
                    __CLASS__,
                    'set',
                    $this->getType(),
                    $this->getItemType($value)
                )
            );
        }

        parent::set($key, $value);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \UnexpectedValueException
     */
    public function add($value)
    {
        if (! $this->checkType($value)) {
            throw new \UnexpectedValueException(
                sprintf(
                    self::$exceptionErrorMessage,
                    1,
                    __CLASS__,
                    'add',
                    $this->getType(),
                    $this->getItemType($value)
                )
            );
        }

        parent::add($value);

        return true;
    }

    /**
     * Returns a string representation of this object.
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . '::' . $this->getType() . '@' . spl_object_hash($this);
    }
}
