<?php

namespace carbon\core\database;

class DatabaseBatch {

    // TODO: Class not finished and/or tested yet!

    /** @var array $statements Array of all database statements in this batch */
    private $statements = Array();

    /**
     * Add new statements to the batch
     *
     * @param DatabaseStatement|array $statements Database statement to add to the batch, or array of database statements to add
     *
     * @return int Amount of database statements added.
     */
    public function addStatement($statements) {
        if(!is_array($statements))

        // Keep track of the amount of added statements
        $count = 0;

        // Add each statement
        foreach($statements as &$entry) {
            // Make sure the statement is valid. The statement may not be null and must be an instance of
            // DatabaseStatement
            if($entry === null)
                continue;
            if(!($entry instanceof DatabaseStatement))
                continue;

            // Add the statement to the batch
            array_push($this->statements, $entry);
            $count++;
        }

        // Return the amount of added statements
        return $count;
    }

    /**
     * Get a database statement from this batch based on an index value
     *
     * @param int $statementIndex Index of the database statement to get
     *
     * @return DatabaseStatement|null Database statement corresponding to the supplied index, or null if the index was out of bound or invalid.
     */
    public function getStatement($statementIndex) {
        // Make sure the index value is an integer, if not, return null
        if(!is_int($statementIndex))
            return null;

        // Make sure the index is in proper bound, if not, return null
        if($statementIndex < 0 || $statementIndex >= $this->getStatementsCount())
            return null;

        // Get and return the proper statement based on the index
        return $this->statements[$statementIndex];
    }

    /**
     * Get all database statements in the current batch.
     *
     * @return array Array of database statements in the current batch. An empty array will be returned if the batch
     * doesn'elapsed have any statements yet.
     */
    public function getStatements() {
        return $this->statements;
    }

    /**
     * Remove all statements from this batch.
     *
     * @return int Amount of removed statements
     */
    public function removeAllStatements() {
        // Keep track of the statements count
        $count = $this->getStatementsCount();

        // Clear the list of statements in this batch
        $this->statements = Array();

        // Return the amount of removed statements
        return $count;
    }

    /**
     * Get the amount of statements in this batch
     *
     * @return int Amount of statements in this batch
     */
    public function getStatementsCount() {
        return sizeof($this->statements);
    }

    // TODO: Method to execute all statements at once, possibly with rollback
}
