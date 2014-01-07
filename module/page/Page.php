<?php

/**
 * Page.php
 * Page class for Carbon CMS.
 * @author Tim Visée
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visée 2012, All rights reserved.
 */

namespace module\page;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

class Page {
    
    private $id;
    
    /**
     * Page class constructor
     * @param page_id the page id
     */
    public function __construct($page_id) {
        $this->id = $page_id;
    }
    
    /**
     * Get the page id
     * @return the page id
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Check if there's any page with this page id
     * @param page_id the page id to check
     * @return true if there's any page with this id
     */
    public static function isPageWithId($config, $db, $page_id) {
        $statement = $db->prepare('SELECT `id` FROM `'.$config->getValue('database', 'table_prefix').'page` WHERE `id`=:page_id');
        $statement->bindValue(':page_id', $page_id);
        $statement->execute();
        return ($statement->rowCount() > 0);
    }
}