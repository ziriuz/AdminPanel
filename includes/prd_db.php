<?php

$sql = new DB(DB_HOST,DB_LOGIN, DB_PASS, DB_NAME,false);
$sql->open();
$sql->query("SET NAMES 'utf8';");
class DB
{
    // Connection parameters 
    var $host = '';
    var $user = '';
    var $password = '';
    var $database = '';
    var $persistent = true;

    // Database connection handle 
    var $conn = NULL;

    // Query result 
    var $result = false;

    function DB($host, $user, $password, $database, $persistent = true)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
        $this->persistent = $persistent;
    }

    function open()
    {
        // Choose the appropriate connect function 
        if ($this->persistent) {
            $this->conn = mysqli_connect('p:'.$this->host, $this->user, $this->password,$this->database);
        } else {
            $this->conn = mysqli_connect($this->host, $this->user, $this->password,$this->database);
        }
        if (mysqli_connect_errno($this->conn)) {
          return false;
        }
        // Select the requested database 
        if (!mysqli_select_db($this->conn,$this->database)) {
            return false;
        } else
            return true;
    }

    function close()
    {
        return (mysqli_close($this->conn));
    }

    function error()
    {
        return (mysqli_error($this->conn));
    }

    function query($sql = '')
    {
        $this->result = mysqli_query($this->conn,$sql);
        return ($this->result != false);
    }

    function affectedRows()
    {
        return (mysqli_affected_rows($this->conn));
    }

    function numRows()
    {
        return (mysqli_num_rows($this->result));
    }
    function foundRows()
    {
      $this->query("SELECT FOUND_ROWS();");
      $dummy = $this->fetchArray();
      return $dummy[0];
    }
    function fetchObject()
    {
        return (mysqli_fetch_object($this->result));
    }

    function fetchArray()
    {
        return (mysqli_fetch_array($this->result, MYSQLI_NUM));
    }

    function fetchAssoc()
    {
        return (mysqli_fetch_assoc($this->result));
    }

    function freeResult()
    {
        return (mysqli_free_result($this->result));
    }
    function escape_string($str)
    {
        return (mysqli_real_escape_string($this->conn,$str));
    }
}
?>
