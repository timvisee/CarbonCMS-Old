<?php

/**
 * EventManager.php
 *
 * The EventManager class manages all the events registered by plugins.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

namespace core;

use core\event\Event;
use core\event\Listener;
use core\plugin\Plugin;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * Manages all the events registered by plugins.
 * @package core
 * @author Tim Visee
 */
class EventManager {
    
    private $registered_events = array();
    
    /**
     * Constructor
     */
    public function __construct() { }
    
    /**
     * Register events from an event listener
     * @param Listener $l Event listener
     * @param Plugin $p Plugin to register the events for
     */
    public function registerEvents(Listener $l, Plugin $p) {
        // Make sure any of the parameters is not null
        if($l == null || $p == null)
            return;
        
        // Get the event class and it's methods
        $event_class = new \ReflectionClass($l);
        $event_methods = $event_class->getMethods();
        
        // Loop through each method and check if it could be used as event listener, if so register the function
        foreach($event_methods as $method) {
            // Make sure the method has one parameter
            if(sizeof($method->getParameters()) != 1)
                continue;
            
            // The method has to be public (or it can't be called!)
            if(!$method->isPublic())
                continue;
            
            // Get the parameter name and class of the current method
            $param_arr = $method->getParameters();
            $param = $param_arr[0];
            $param_class = $param->getClass()->getName();
            
            // Check if the class of the parameter is an instance of an Event
            if($this->isEvent($param_class)) {
                // Get the method/function name and the name of the event
                $event_name = $param_class;
                $function_name = $method->getName();
                
                // TODO: Make sure the function is not already registered!
                
                // Register the event
                array_push($this->registered_events,
                        new RegisteredEvent($event_name, $p, $l, $function_name)
                        );
            }
        }
    }
    
    /**
     * Get all registered events
     * @return array Array of registered events, empty array when no event was registered yet
     */
    public function getRegisteredEvents() {
        return $this->registered_events;
    }
    
    /**
     * Get the amount of registered events
     * @return int Amount of registered events
     */
    public function getRegisteredEventsCount() {
        return sizeof($this->registered_events);
    }
    
    /**
     * Unregister all events
     * @return int Amount of unregistered events
     */
    public function unregisterEvents() {
        $unregistered_amount = sizeof($this->registered_events);
        $this->registered_events = Array();
        return $unregistered_amount;
    }

    // TODO: Method unregisterEventsFromPlugin(); not tested yet!
    /**
     * Unregister all events registered by a specific plugin
     * @param Plugin $p Plugin to unregister the events from
     */
    public function unregisterEventsFromPlugin(Plugin $p) {
        // Loop through each registered event
        for($i = 0; $i < sizeof($this->registered_events); $i++) {
            // Get the RegisteredEvent instance of the current entry
            $e = $this->registered_events[$i];

            // Does the plugin of the current entry equal to the param plugin
            if($e->getPlugin()->equals($p)) {
                // Unregister the current entry
                array_splice($this->registered_events, $i, 1);
                $i--;
            }
        }
    }
    
    /**
     * Call an event
     * @param Event $event Event to call
     * @param Plugin $plugin Plugin to call event too, null to call to every enabled plugin (optional)
     */
    public function callEvent(Event $event, Plugin $plugin = null) {
        // Get the name of the event
        $event_name = $event->getEventName();
        
        // Loop through each registered event to check if it should be called
        foreach($this->registered_events as $entry) {
            
            // Make sure the name of the current event equals to the event name that should be called
            if($event_name == $entry->getEventName()) {
                
                // Only call this registered event if the plugin equals to $plugin, or if $plugin equals to null
                if($entry->getPlugin()->equals($plugin) || $plugin == null)
                    $entry->callFunction($event);
            }
        }
    }
    
    /**
     * Check if an event has been registered
     * @param Listener $listener Event listener
     * @param string $function_name Function name
     * @param Plugin $plugin Plugin
     * @return boolean True if this event was registered
     */
    public function isEventRegistered(Listener $listener, string $function_name, Plugin $plugin) {
        // Loop through each registered event to check if it equals to the method parameters
        foreach($this->registered_events as $entry) {
            
            // Check if the listener equals
            if($entry->getListener() == $listener) {
                
                // Check if the function name equals
                if($entry->getFunctionName() == $function_name) {
                    
                    // Check if the plugin equals
                    return ($entry->getPlugin()->equals($plugin));
                }
            }
        }
    }
    
    /**
     * Check if a EventName corresponds to an event
     * @param string $event_name Event name to check
     * @return boolean True if event
     */
    public function isEvent($event_name) {
        // TODO: 'core\event\Event' and NOT just 'Event' because namespaces are being used!
        // TODO: Use something like getClass() from the Event plugin
        return is_subclass_of($event_name, 'lib\event\Event');
    }
}

class RegisteredEvent {
    
    private $event_name;
    private $plugin;
    private $listener;
    private $function_name;
    
    /**
     * Constructor
     * @param string $event_name Event name
     * @param Plugin $plugin Plugin of the registered event
     * @param Listener $listener The event listener
     * @param string $function_name The name of the function to call once the event is being called
     */
    public function __construct($event_name, Plugin $plugin, Listener $listener, $function_name) {
        $this->event_name = $event_name;
        $this->plugin = $plugin;
        $this->listener = $listener;
        $this->function_name = $function_name;
    }
    
    /**
     * Get the name of the event
     * @return string Event name
     */
    public function getEventName() {
        return $this->event_name;
    }
    
    /**
     * Get the plugin of the event
     * @return Plugin Plugin
     */
    public function getPlugin() {
        return $this->plugin;
    }
    
    /**
     * Get the listener for the event
     * @return Listener Listener for the event
     */
    public function getListener() {
        return $this->listener;
    }
    
    /**
     * Get the function name that will be called once the event is being called
     * @return string Function name that will be called
     */
    public function getFunctionName() {
        return $this->function_name;
    }
    
    /**
     * Call the function
     */
    public function callFunction(Event $event) {
        // Get the listener and the function name
        $listener = $this->listener;
        $function = $this->function_name;
        
        // Make sure the event name is from the same type
        if($this->getEventName() != $event->getEventName()) {
            throw new Exception('Carbon CMS: Event was \'' . $event->getEventName() . '\' tried to be called as \'' . $this->getEventName() . '\', call canceled!');
        }
        
        // Call the event
        $listener->$function($event);
    }
}