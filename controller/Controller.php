<?php

/**
 * Controller.php
 * Controller class file for Carbon CMS.
 * @author Tim Visée
 * @version 0.1
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visée 2012-2013, All rights reserved.
 */

namespace controller;

use model;
use \core\Database;
use \core\SessionHandler;
use \view\View;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * Controller class, parent controller
 * @package core
 * @author Tim Visée
 */
class Controller {
    
    /**
     * @var Database $db Database instance
     * @var Model $model The model
     * @var View $view The view
     */
    private $db;
    private $model;
    protected $view;
    
    /**
     * Constructor
     * @param Database $db Database instance
     */
    function __construct(Database $db) {
        // Store the database instance
        $this->db = $db;
        
        // Construct the View
        $this->view = new View();
    }
    
    /**
     * Get the database instance
     * @return Database Database instance
     */
    public function getDatabase() {
        return $this->db;
    }
    
    /**
     * Get the model
     * @return Model Model
     */
    public function getModel() {
        return $this->model;
    }
    
    /**
     * Load a model by name
     * @param string $model_name Name of the model to loadLocalesList
     */
    public function loadModel($model_name) {
        // Get the path the model is on
        $model_path = __DIR__ . '/../model/'.strtolower($model_name).'_model.php';
        
        // Make sure the model file exists
        if(!file_exists($model_path)) {
            // TODO: Show error page instead of killing the page!
            die('Carbon CMS: The model \'' . $model_name . '\' does not exist!');
        }
        
        // Parse the models class name
        $model_class = ucfirst(strtolower($model_name)).'_Model';
        
        // Get the model's class name with namespace
        $model_class_namespace = '\\model\\'.$model_class;
        
        // Construct the model
        if(strtolower($model_name) != 'page')
            $this->model = new $model_class_namespace($this->getDatabase());
        else
            $this->model = new $model_class_namespace($this->getDatabase(), $this->getPage());
    }
    
    /**
     * Get the view
     * @return View View
     */
    public function getView() {
        return $this->view;
    }
}