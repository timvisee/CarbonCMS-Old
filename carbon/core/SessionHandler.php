<?php

/**
 * SessionHandler.php
 *
 * The SessionHandler class handles the sessions of clients.
 * The SessionHandler class automaticly uses cookies or sessions according to the database registry.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

namespace carbon\core;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Handles the sessions of clients.
 * @package core
 * @author Tim Visee
 */
class SessionHandler {
    
    /**
     * false = Sessions
     * true = Cookies
     */
    public static $USE_COOKIES = false;
    public static $COOKIE_DOMAIN = null;
    
    /**
     * Initialize the sessions
     */
    public static function init() {
        // Initialize sessions
        session_start();
    }
    
    /**
     * Check if cookies are used
     * @return boolean True if cookies are used
     */
    public static function useCookies() {
        return SessionHandler::$USE_COOKIES;
    }
    
    /**
     * Set if cookies or sessions should be used
     * @param boolean $use_cookies True to use cookies, false to use sessions
     */
    public static function setUseCookies($use_cookies) {
        SessionHandler::$USE_COOKIES = $use_cookies;
    }
    
    /**
     * Get the cookie domain
     * @return string Cookie domain
     */
    public static function getCookieDomain() {
        return SessionHandler::$COOKIE_DOMAIN;
    }
    
    /**
     * Set the cookie domains
     * @param string Cookie domain
     */
    public static function setCookieDomain($cookie_domain) {
        SessionHandler::$COOKIE_DOMAIN = $cookie_domain;
    }
    
    /**
     * Get a session
     * @param key SessionHandler key
     * @return SessionHandler value
     */
    public static function get($key) {
        // Return session/cookie if its set
        if(SessionHandler::isSession($key)) {
            if(!SessionHandler::$USE_COOKIES)
                return $_SESSION[$key];
            else
                return $_COOKIE[$key];
        }
    }
    
    /**
     * Set a session
     * @param key SessionHandler key
     * @param value SessionHandler value
     * @param expire expiration date (optional)
     */
    public static function set($key, $value, $expire = null) {
        // Parse key
        if($key == null || $key == "")
            return;
        
        // Expiration times
        if($expire == null)
            $expire = 0;
        
        // Set the session/cookie
        if(!SessionHandler::$USE_COOKIES)
            $_SESSION[$key] = $value;
        else
            setcookie($key, $value, $expire, '/', SessionHandler::$COOKIE_DOMAIN);
    }
    
    /**
     * Check if a session is set
     * @param key SessionHandler key
     * @return true if set
     */
    public static function isSession($key) {
        if(!SessionHandler::$USE_COOKIES)
            return isset($_SESSION[$key]);
        else
            return isset($_COOKIE[$key]);
    }
    
    /**
     * Remove or reset a session
     * @param key SessionHandler key
     */
    public static function remove($key) {
        // Remove a session/cookie
        if(SessionHandler::isSession($key)) {
            if(!SessionHandler::$USE_COOKIES)
                unset($_SESSTION[$key]);
            else
                setcookie($key, '', time()-9999999, '/', SessionHandler::$COOKIE_DOMAIN);
        }
    }
    
    /**
     * Unset all sessions
     */
    public static function unsetSessions() {
        // Unset session variables
        session_unset();
        
        echo '<pre>';
        print_r($_COOKIE);
        echo '</pre>';
        
        // Remove/unset all cookies
        foreach($_COOKIE as $cookie) {
            SessionHandler::remove($cookie);
        }
    }
    
    /**
     * Destroy all sessions
     */
    public static function destroySessions() {
        // Destroy sessions
        session_destroy();
        
        // Remove/unset all cookies
        foreach($_COOKIE as $cookie) {
            SessionHandler::remove($cookie);
        }
    }
}