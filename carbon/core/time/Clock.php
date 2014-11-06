<?php

namespace carbon\core\time;

// Prevent direct requests to this file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class Clock {

    /** @var float timer Variable used to calculate the time difference between two times */
    private $timer = 0;
    /** @var float Stores the elapsed time in seconds each time the clock is paused */
    private $elapsed = 0;
    /** @var bool True if the clock has started, false if not. The use of reset() will reset this state. */
    private $started = false;
    /** @var bool True if the clock is paused, false if not. */
    private $paused = false;

    /** Default format identifier */
    const TIME_FORMAT_DEFAULT = 0;
    /** Microseconds format identifier */
    const TIME_FORMAT_MICROS = 1;
    /** Milliseconds format identifier */
    const TIME_FORMAT_MILLIS = 2;
    /** Seconds format identifier */
    const TIME_FORMAT_SECONDS = 3;

    /**
     * Constructor
     *
     * @param bool $start True to automatically start the clock as soon as it's constructed.
     */
    public function __construct($start = false) {
        // Should the clock be started
        if($start === true)
            $this->start();
    }

    /**
     * Start the clock. The clock will be resumed if it was started already but paused.
     *
     * @return bool True if the clock has been started. False if it started already.
     */
    public function start() {
        // Make sure the clock isn't started already
        if($this->isStarted()) {
            // Resume the clock if it was paused, then return false
            $this->resume();
            return false;
        }

        // Set the timer
        $this->timer = microtime(true);

        // Set the started state and return true
        $this->started = true;
        return true;
    }

    /**
     * Pause the clock. The clock has to be active in order to pause it.
     *
     * @return bool True if the clock has been paused while it was active.
     */
    public function pause() {
        // Make sure the clock is active
        if(!$this->isActive())
            return false;

        // Sum up the time on the timer with the elapsed time and reset the timer
        $this->elapsed += (microtime(true) - $this->timer);
        $this->timer = 0;

        // Set the paused state, and return true
        $this->paused = true;
        return true;
    }

    /**
     * Resume the paused clock. The clock has to be started but paused in order to resume it.
     *
     * @return bool True if the clock has been resumed while it was started but paused.
     */
    public function resume() {
        // Make sure the clock is started but paused
        if(!$this->isStarted() || !$this->isPaused())
            return false;

        // Set the timer
        $this->timer = microtime(true);

        // Set the paused state and return true
        $this->paused = false;
        return true;
    }

    /**
     * Check whether the clock is started.
     * As soon as the clock has recorded time this state will be true, even though the clock might be paused.
     *
     * @return bool True if the clock has started, false otherwise.
     */
    public function isStarted() {
        return $this->started;
    }

    /**
     * Check whether the clock is currently active.
     * If the clock is active, the clock is started and isn'elapsed currently paused.
     *
     * @return bool True if the clock is active, false otherwise.
     */
    public function isActive() {
        // Return true if the clock is active
        return $this->isStarted() && !$this->isPaused();
    }

    /**
     * Check whether the clock is currently paused. The clock has to be started.
     * @return bool True if the clock is started and currently paused, false otherwise.
     */
    public function isPaused() {
        // Make sure the clock is started
        if(!$this->isStarted())
            return false;

        // Return true if the clock is paused
        return $this->paused;
    }

    /**
     * Reset the state of the clock. This also resets the started state of the clock.
     */
    public function reset() {
        $this->timer = 0;
        $this->elapsed = 0;
        $this->started = false;
        $this->paused = false;
    }

    /**
     * Get the elapsed time in seconds unless a different time format is used.
     *
     * @param int|null Preferred time format to return the elapsed time in.
     * The time will be returned in seconds if the default time format is used.
     * Null may be used to use the default time format.
     * Unknown time formats will cause the method to return the time in the default time format.
     *
     * @return float Elapsed time.
     */
    public function getTime($timeFormat = self::TIME_FORMAT_DEFAULT) {
        // Make sure the clock started, return zero if not
        if(!$this->isStarted())
            return 0;

        // Get the elapsed time and check whether the clock is active or not, if so, include the current timer time.
        $elapsed = $this->elapsed;
        if($this->isActive())
            $elapsed += microtime(true) - $this->timer;

        // Check whether the time should be returned in microseconds
        if($timeFormat === self::TIME_FORMAT_MICROS)
            return ($elapsed * 1000000);

        // Check whether the time should be returned in milliseconds
        if($timeFormat === self::TIME_FORMAT_MILLIS)
            return ($elapsed * 1000);

        // Return the elapsed time in seconds
        return $elapsed;
    }
}