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

class Money implements MoneyInterface, \Serializable
{
    /**
     * @var int
     */
    protected $amount;

    /** @var \Money\Currency */
    protected $currency;

    /**
     * Create a Money instance
     * @param  integer $amount    Amount, expressed in the smallest units of $currency (eg cents)
     * @param  \Money\Currency $currency
     * @throws \Money\InvalidArgumentException
     */
    public function __construct($amount, Currency $currency)
    {
        if (!is_int($amount)) {
            throw new InvalidArgumentException("The first parameter of Money must be an integer. It's the amount, expressed in the smallest units of currency (eg cents)");
        }
        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * Convenience factory method for a Money object
     * @example $fiveDollar = Money::USD(500);
     * @param string $method
     * @param array $arguments
     * @return \Money\Money
     */
    public static function __callStatic($method, $arguments)
    {
        return new Money($arguments[0], new Currency($method));
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([$this->amount, $this->currency]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->amount, $this->currency) = unserialize($serialized);
    }

    /**
     * {@inheritdoc}
     */
    public function isSameCurrency(Money $other)
    {
        return $this->currency->equals($other->currency);
    }

    /**
     * @throws \Money\InvalidArgumentException
     */
    private function assertSameCurrency(Money $other)
    {
        if (!$this->isSameCurrency($other)) {
            throw new InvalidArgumentException('Different currencies');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function equals(Money $other)
    {
        return
            $this->isSameCurrency($other)
            && $this->amount === $other->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function compare(Money $other)
    {
        $this->assertSameCurrency($other);
        if ($this->amount < $other->amount) {
            return -1;
        } elseif ($this->amount == $other->amount) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function greaterThan(Money $other)
    {
        return 1 === $this->compare($other);
    }

    /**
     * @param \Money\Money $other
     * @return bool
     */
    public function greaterThanOrEqual(Money $other)
    {
        return 1 == $this->compare($other)
            or 0 == $this->compare($other);
    }

    /**
     * {@inheritdoc}
     */
    public function lessThan(Money $other)
    {
        return -1 === $this->compare($other);
    }

    /**
     * @param \Money\Money $other
     * @return bool
     */
    public function lessThanOrEqual(Money $other)
    {
        return -1 == $this->compare($other)
            or 0 == $this->compare($other);
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function add(Money $addend)
    {
        $this->assertSameCurrency($addend);

        return new self($this->amount + $addend->amount, $this->currency);
    }

    /**
     * {@inheritdoc}
     */
    public function subtract(Money $subtrahend)
    {
        $this->assertSameCurrency($subtrahend);

        return new self($this->amount - $subtrahend->amount, $this->currency);
    }

    /**
     * @throws \Money\InvalidArgumentException
     */
    private function assertOperand($operand)
    {
        if (!is_int($operand) && !is_float($operand)) {
            throw new InvalidArgumentException('Operand should be an integer or a float');
        }
    }

    /**
     * @throws \Money\InvalidArgumentException
     */
    private function assertRoundingMode($rounding_mode)
    {
        if (!in_array($rounding_mode, array(PHP_ROUND_HALF_DOWN, PHP_ROUND_HALF_EVEN, PHP_ROUND_HALF_ODD, PHP_ROUND_HALF_UP))) {
            throw new InvalidArgumentException('Rounding mode should be Money::ROUND_HALF_DOWN | Money::ROUND_HALF_EVEN | Money::ROUND_HALF_ODD | Money::ROUND_HALF_UP');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function multiply($multiplier, $rounding_mode = PHP_ROUND_HALF_UP)
    {
        $this->assertOperand($multiplier);
        $this->assertRoundingMode($rounding_mode);

        $product = (int) round($this->amount * $multiplier, 0, $rounding_mode);

        return new Money($product, $this->currency);
    }

    /**
     * {@inheritdoc}
     */
    public function divide($divisor, $rounding_mode = PHP_ROUND_HALF_UP)
    {
        $this->assertOperand($divisor);
        $this->assertRoundingMode($rounding_mode);

        $quotient = (int) round($this->amount / $divisor, 0, $rounding_mode);

        return new Money($quotient, $this->currency);
    }

    /**
     * {@inheritdoc}
     */
    public function allocate(array $ratios)
    {
        $remainder = $this->amount;
        $results = array();
        $total = array_sum($ratios);

        foreach ($ratios as $ratio) {
            $share = (int) floor($this->amount * $ratio / $total);
            $results[] = new Money($share, $this->currency);
            $remainder -= $share;
        }
        for ($i = 0; $remainder > 0; $i++) {
            $results[$i]->amount++;
            $remainder--;
        }

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function isZero()
    {
        return $this->amount === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isPositive()
    {
        return $this->amount > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isNegative()
    {
        return $this->amount < 0;
    }

    /**
     * {@inheritdoc}
     */
    public static function stringToUnits( $string )
    {
        $sign = "(?P<sign>[-\+])?";
        $digits = "(?P<digits>\d*)";
        $separator = "(?P<separator>[.,])?";
        $decimals = "(?P<decimal1>\d)?(?P<decimal2>\d)?";
        $pattern = "/^".$sign.$digits.$separator.$decimals."$/";

        if (!preg_match($pattern, trim($string), $matches)) {
            throw new InvalidArgumentException("The value could not be parsed as money");
        }

        $units = $matches['sign'] == "-" ? "-" : "";
        $units .= $matches['digits'];
        $units .= isset($matches['decimal1']) ? $matches['decimal1'] : "0";
        $units .= isset($matches['decimal2']) ? $matches['decimal2'] : "0";

        return (int) $units;
    }
}
