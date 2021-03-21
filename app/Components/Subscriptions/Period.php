<?php

namespace App\Components\Subscriptions;


use Carbon\Carbon;
use Illuminate\Support\Facades\Lang;

class Period
{
    /**
     * The interval constants.
     */
    const DAY = 'day';
    const WEEK = 'week';
    const MONTH = 'month';
    const YEAR = 'year';

    /**
     * Map Interval to Carbon methods.
     *
     * @var array
     */
    protected static $intervalMapping = [
        self::DAY => 'addDays',
        self::WEEK => 'addWeeks',
        self::MONTH => 'addMonths',
        self::YEAR => 'addYears',
    ];

    /**
     * Starting date of the period.
     *
     * @var \Carbon\Carbon
     */
    protected $startAt;

    /**
     * Ending date of the period.
     *
     * @var \Carbon\Carbon
     */
    protected $endAt;

    /**
     * Interval
     *
     * @var string
     */
    protected $intervalUnit;

    /**
     * Interval count
     *
     * @var int
     */
    protected $intervalCount = 1;

    /**
     * Period constructor.
     * @param string $intervalUnit
     * @param int $intervalCount
     * @param null $startAt
     */
    public function __construct(string $intervalUnit = 'month', int $intervalCount = 1, $startAt = null)
    {
        if ($startAt instanceof \DateTime) {
            $this->startAt = Carbon::instance($startAt);
        } elseif (is_int($startAt)) {
            $this->startAt = Carbon::createFromTimestamp($startAt);
        } elseif (empty($startAt)) {
            $this->startAt = new Carbon();
        } else {
            $this->startAt = Carbon::parse($startAt);
        }

        if (!self::isValidIntervalUnit($intervalUnit)) {
            echo 'here ' . $intervalUnit;
            die;
            throw new \InvalidArgumentException("Interval unit `{$intervalUnit}` is invalid");
        }

        $this->intervalUnit = $intervalUnit;

        if ($intervalCount >= 0) {
            $this->intervalCount = $intervalCount;
        }

        $this->calculate();
    }

    /**
     * Get start date.
     *
     * @return \Carbon\Carbon
     */
    public function getStartDate()
    {
        return $this->startAt;
    }

    /**
     * Get end date.
     *
     * @return \Carbon\Carbon
     */
    public function getEndDate()
    {
        return $this->endAt;
    }

    /**
     * Get period interval.
     *
     * @return string
     */
    public function getIntervalUnit()
    {
        return $this->intervalUnit;
    }

    /**
     * Get period interval count.
     *
     * @return int
     */
    public function getIntervalCount()
    {
        return $this->intervalCount;
    }

    /**
     * Calculate the end date of the period.
     *
     * @return void
     */
    protected function calculate()
    {
        $method = $this->getMethod();
        $this->endAt = (clone $this->startAt)->$method($this->intervalCount);
    }

    /**
     * Get computation method.
     *
     * @return string
     */
    protected function getMethod()
    {
        return self::$intervalMapping[$this->intervalUnit];
    }

    /**
     * Get all available intervals.
     *
     * @return array
     */
    public static function getAllIntervals()
    {
        $intervals = [];

        foreach (array_keys(self::$intervalMapping) as $interval) {
            $intervals[$interval] = trans('plans::messages.' . $interval);
        }

        return $intervals;
    }

    /**
     * Check if a given interval is valid.
     *
     * @param  string $intervalUnit
     * @return bool
     */
    public static function isValidIntervalUnit($intervalUnit): bool
    {
        return array_key_exists($intervalUnit, self::$intervalMapping);
    }
}