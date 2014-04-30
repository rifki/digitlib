<?php
/**
 * Database library for microsite
 * @author Muhamad Rifki <rifki@rifkilabs.net>
 * @version 1.0.1
 */

require_once 'config.php';

class Database
{
    protected $connection;

    public function __construct()
    {
        // load connection
        $this->getConnection();
    }

    /**
     * MySQL connection
     */
    public function getConnection()
    {
        $this->connection = @mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
        if (! $this->connection) {
            die("Could not connect to database! ".mysql_error($this->connection));
        } else {
            $select_db = @mysql_select_db(DB_NAME);
            if (! $select_db) {
                die('Database not selected! ');//mysql_error($this->connection)
            }
        }
    }

    /**
     * Query database
     * @param unknown_type $sql
     */
    public function query($sql)
    {
        return mysql_query($sql, $this->connection);
    }

    /**
     * Fetching array data
     * @param unknown_type $result
     */
    public function fetchAll($result)
    {
        return mysql_fetch_array($result);
    }

    /**
     * Fetching object
     * @param $result
     */
    public function fetchObject($result)
    {
        return mysql_fetch_object($result);
    }

    /**
     * Fetch a result row as an associative array
     * @param unknown_type $result
     * @return multitype:
     */
    public function fetchAssoc($result)
    {
        return mysql_fetch_assoc($result);
    }

    /**
     * Get number of rows in result
     * @param unknown_type $result
     * @return number
     */
    public function numRows($result)
    {
        return mysql_num_rows($result);
    }

    /**
     * Use mysql escape string
     * @param type $escape_string
     * @return type string
     */
    public function quote($quote)
    {
        return mysql_real_escape_string($quote);
    }

    /**
     * insert id
     */
    public function insertId()
    {
        return mysql_insert_id($this->connection);
    }
}
