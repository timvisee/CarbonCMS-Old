<?php

namespace carbon\core\time;

// Prevent direct requests to this file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class Profiler extends Clock {

    /** @var bool True to allow timings of zero seconds to be returned, false otherwise. */
    private $allowZero = true;

    /**
     * Constructor
     *
     * @param bool $start True to automatically start the clock as soon as it's constructed.
     */
    public function __construct($start = false) {
        parent::__construct($start);
    }

    /**
     * Get the elapsed time with proper human-understandable formatting.
     *
     * @param int|null $timeFormat The time format that should be used on the returned value.
     * The default time format is dynamic and depends on the amount of time elapsed.
     * Null or an invalid value will cause the method to return the time in the default time format.
     *
     * @return string Elapsed time with proper human-understandable formatting.
     */
    public function getTimeProper($timeFormat = self::TIME_FORMAT_DEFAULT) {
        // Get the elapsed time
        $time = $this->getTime();

        // Return invalid timings
        if($time < 0)
            switch($timeFormat) {
            case self::TIME_FORMAT_MICROS:
                return '? &micro;s';
            case self::TIME_FORMAT_MILLIS:
                return '? ms';
            default:
                return '? s';
            }

        // Return timings of zero
        if($time == 0 && $this->allowZero)
            switch($timeFormat) {
            case self::TIME_FORMAT_MICROS:
                return '0 &micro;s';
            case self::TIME_FORMAT_MILLIS:
                return '0 ms';
            default:
                return '0 s';
            }

        // Return timings for an elapsed time less than one microsecond
        if($time < 0.000001)
            switch($timeFormat) {
            case self::TIME_FORMAT_MICROS:
                return '<1 &micro;s';
            case self::TIME_FORMAT_MILLIS:
                return '<1 ms';
            default:
                return '<1 s';
            }

        // Return timings in specified formats
        switch($timeFormat) {
        case self::TIME_FORMAT_MICROS:
            return round($time * 1000000) . ' &micro;s';
        case self::TIME_FORMAT_MILLIS:
            return round($time * 1000) . ' ms';
        case self::TIME_FORMAT_SECONDS:
            return round($time) . ' s';
        default:
            break;
        }

        // Return timings for an elapsed time between one micro- and millisecond
        if(round($time * 1000000) < 1000)
            return round($time * 1000000) . ' &micro;s';

        // Return timings for an elapsed time between one millisecond and one second
        if(round($time * 1000) < 1000)
            return round($time * 1000) . ' ms';

        // Return the timing in seconds
        return round($time) . ' s';
    }

    /**
     * Check whether a timing of zero seconds may be returned.
     *
     * @return bool True if so, false if not.
     */
    public function isAllowZero() {
        return $this->allowZero;
    }

    /**
     * Set whether a timing of zero seconds may be returned.
     *
     * @param $allowZero True if so, false if not.
     */
    public function setAllowZero($allowZero) {
        $this->allowZero = $allowZero;
    }
}