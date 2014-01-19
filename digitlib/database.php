<?php
/**
 * Database library for microsite
 * @author Muhamad Rifki <rifki@rifkilabs.net>
 * @version 1.1
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
                die('Database not selected! '.mysql_error($this->connection));
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
     * Select Table
     * @param type $table
     * @param type $fields
     * @param type $where
     * @param type $limit
     * @param type $orderby
     * @return boolean
     */
    public function select($table, $fields, $where = null, $orderby = null, $limit = null)
    {
        $sql = "SELECT ";
        if (isset($fields) && $fields !== null && $fields !== "") {
            $sql .= "{$fields}\n";
        } else {
            $sql .= "* ";
        }

        if (isset($table) && $table !== null && $table !== "") {
            $sql .= "FROM {$table} ";
        } else {
            exit('Table is required not empty!');
        }

        if (isset($where) && $where !== null && $where !== "")
            $sql .= "WHERE {$where}";

        if (isset($orderby) && $orderby !== null && $orderby !== "")
            $sql .= "ORDER BY {$orderby} ";

        if (isset($limit) && $limit !== null && $limit !== "")
            $sql .= "LIMIT ";

        $result = $this->query($sql);

        if (num_rows($result) > 0)
            return true;
        else
            return false;
    }

    /**
     * Insert into table
     * @param string $table
     * @param array $data
     */
    public function insert($table, $data)
    {
        if (isset($table) && isset($data)) {
            if (is_array($data)) {
                $fields         = array_keys($data);
                $values         = array_map("mysql_real_escape_string", array_values($data));
                //print_r($values);
                $implodeField   = implode(",", $fields);
                $implodeValue   = implode(",", $values);
                $this->query("INSERT INTO {$table} (".$implodeField.") VALUES ('".$implodeValue."');") or die(mysql_error());
            }
        }

    }

    /**
     * Update table
     * @param unknown_type $data
     * @param unknown_type $table
     * @param unknown_type $where
     */
    public function update($table, $data = array(), $where = array())
    {
        //if (is_array($data) && is_array($where)) {
        # set column
        foreach ($data as $key => $value)
            $sql = "UPDATE {$table} SET {$key} = {$value} ";

        # where
        foreach ($where as $key => $value)
            $sql .= "WHERE {$key} = {$value}";

        # run
        $this->query($sql);
        return true;
        //}
    }

    /**
     * Fetching array data
     * @param unknown_type $result
     */
    public function fetchAll($result)
    {
        return mysql_fetch_array($result);
    }

    public function find($table, $fields = null, $where = "", $term = "")
    {
        $sql = "SELECT ";
        if (isset($fields) && $fields !== null && $fields !== "") {
            $sql .= "{$fields}\n";
        } else {
            $sql .= "* ";
        }

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
