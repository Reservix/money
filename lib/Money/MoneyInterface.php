<?php

namespace Money;

/**
 * MoneyInterface
 */
interface MoneyInterface
{
    /**
     * @param MoneyInterface $other
     *
     * @return bool
     */
    public function isSameCurrency(MoneyInterface $other);

    /**
     * @param MoneyInterface $other
     *
     * @return bool
     */
    public function equals(MoneyInterface $other);

    /**
     * @param MoneyInterface $other
     *
     * @return int
     */
    public function compare(MoneyInterface $other);

    /**
     * @param MoneyInterface $other
     *
     * @return bool
     */
    public function greaterThan(MoneyInterface $other);

    /**
     * @param MoneyInterface $other
     *
     * @return bool
     */
    public function greaterThanOrEqual(MoneyInterface $other);

    /**
     * @param MoneyInterface $other
     *
     * @return bool
     */
    public function lessThan(MoneyInterface $other);

    /**
     * @param MoneyInterface $other
     *
     * @return bool
     */
    public function lessThanOrEqual(MoneyInterface $other);

    /**
     * @return int
     */
    public function getAmount();

    /**
     * @return Currency
     */
    public function getCurrency();

    /**
     * @param MoneyInterface $addend
     *
     * @return Money
     */
    public function add(MoneyInterface $addend);

    /**
     * @param MoneyInterface $subtrahend
     *
     * @return Money
     */
    public function subtract(MoneyInterface $subtrahend);

    /**
     * @param $multiplier
     * @param int $rounding_mode
     *
     * @return Money
     */
    public function multiply($multiplier, $rounding_mode = PHP_ROUND_HALF_UP);

    /**
     * @param $divisor
     * @param int $rounding_mode
     *
     * @return Money
     */
    public function divide($divisor, $rounding_mode = PHP_ROUND_HALF_UP);

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
