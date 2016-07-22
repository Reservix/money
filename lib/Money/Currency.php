<?php
/**
 * This file is part of the Money library
 *
 * Copyright (c) 2011-2013 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Money;

class Currency implements \Serializable, CurrencyInterface
{
    /** @var string */
    protected $name;

    /** @var array */
    protected static $currencies;

    /**
     * {@inheritdoc}
     */
    public function __construct($name)
    {
        if(!isset(static::$currencies)) {
            static::$currencies = require __DIR__.'/currencies.php';
        }

        $this->setName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize($this->name);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $this->name = unserialize($serialized);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        if (!array_key_exists($name, static::$currencies)) {
            throw new UnknownCurrencyException($name);
        }

        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(CurrencyInterface $other)
    {
        return $this->name === $other->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getName();
    }
}
