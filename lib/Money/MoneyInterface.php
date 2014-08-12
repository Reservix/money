<?php

namespace Money;

/**
 * MoneyInterface
 */
interface MoneyInterface
{
    /**
     * @param Money $other
     *
     * @return bool
     */
    public function isSameCurrency(Money $other);

    /**
     * @param Money $other
     *
     * @return bool
     */
    public function equals(Money $other);

    /**
     * @param Money $other
     *
     * @return int
     */
    public function compare(Money $other);

    /**
     * @param Money $other
     *
     * @return bool
     */
    public function greaterThan(Money $other);

    /**
     * @param Money $other
     *
     * @return bool
     */
    public function lessThan(Money $other);

    /**
     * @return int
     */
    public function getAmount();

    /**
     * @return Currency
     */
    public function getCurrency();

    /**
     * @param Money $addend
     *
     * @return Money
     */
    public function add(Money $addend);

    /**
     * @param Money $subtrahend
     *
     * @return Money
     */
    public function subtract(Money $subtrahend);

    /**
     * @param $multiplier
     * @param int $rounding_mode
     *
     * @return Money
     */
    public function multiply($multiplier, $rounding_mode = Money::ROUND_HALF_UP);

    /**
     * @param $divisor
     * @param int $rounding_mode
     *
     * @return Money
     */
    public function divide($divisor, $rounding_mode = Money::ROUND_HALF_UP);

    /**
     * Allocate the money according to a list of ratio's
     *
     * @param array $ratios List of ratio's
     *
     * @return Money
     */
    public function allocate(array $ratios);

    /**
     * @return bool
     */
    public function isZero();

    /**
     * @return bool
     */
    public function isPositive();

    /**
     * @return bool
     */
    public function isNegative();

    /**
     * @param $string
     *
     * @return int
     *
     * @throws \Money\InvalidArgumentException
     */
    public static function stringToUnits($string);
}
