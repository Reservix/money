<?php

namespace Money;

interface CurrencyInterface
{
    /**
     * @param string $name
     *
     * @return $this
     *
     * @throws UnknownCurrencyException
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param \Money\CurrencyInterface $other
     *
     * @return bool
     */
    public function equals(CurrencyInterface $other);
}
