<?php

function get_vars() {
    require(dirname(dirname(__FILE__)) . '/config.php');
    
    return array(
        'host' => $host,
        'user' => $username,
        'password' => $password,
        'db' => $database
    );
}

class DB extends mysqli {

    public function __construct() {
        $configvars = get_vars();
    	$host = $configvars['host'];
        $user = $configvars['user'];
        $pass = $configvars['password'];
        $db   = $configvars['db'];
        parent::__construct($host, $user, $pass, $db);

        if (mysqli_connect_error()) {
            die('Connect Error (' . mysqli_connect_errno() . ') '
                    . mysqli_connect_error());
        }
    }

    /**
     * 
     * @param string $table  Table name.
     * @param array $where  Where clause (not required).
     * @param array $columns  Select columns (not required).
     * @param array $order order by 'fields' => id, 'dir' => 'ASC'
     * @param array $limit takes 'from' => 0, 'to' => 100
     * @return An array of stdClasses.
     */
    function get_records($table, $where = null, $columns = null, $order = null, $limit = null) {
        if (is_array($columns)) {
            $fields = implode(',', $columns);
            $sql = 'SELECT ' . $fields . ' FROM ' . $table;
        } else {
            $sql = 'SELECT * FROM ' . $table;
        }
        if (is_array($where)) {
            $sql .= $this->format_where($where);
        }

        if (!is_null($order)) {
            $sql .= ' ORDER BY ' . $order['fields'] . ' ' . $order['dir'];
        }

        if (!is_null($limit)) {
            $sql .= ' LIMIT ' . $limit['from'] . ', ' . $limit['to'];
        }

        $result = $this->query($sql);
        $data = array();
        $i = 0;
        while ($rawdata = $result->fetch_assoc()) {
            $data[$i] = new stdClass();
            foreach ($rawdata as $key => $row) {
                $data[$i]->$key = $row;
            }
            $i++;
        }
        $result->free();
        return $data;
    }

    function get_fields($table, $field, $where) {
        $sql = 'SELECT ' . $field . ' FROM ' . $table;
        $wheresql = $this->format_where($where);
        $result = $this->query($sql . $wheresql);
        $data = array();
        $i = 0;
        while ($rawdata = $result->fetch_assoc()) {
            $data[$i] = new stdClass();
            foreach ($rawdata as $key => $row) {
                $data[$i]->$key = $row;
            }
            $i++;
        }
        $result->free();
        return $data;
    }

    /**
     * Insert a record into a table.
     *
     * @param string $table short name for table e.g. 'users'
     * @param array $data Data to insert.
     * @return int The insert id. 
     */
    function insert_record($table, $data) {
        // require dirname(dirname(__FILE__)) . '/config.php';
        // require $root . '/lib/general.php';
        $tableheadings = array_keys($data);
        $tableheadings = implode(',', $tableheadings);
        $info = array();
        foreach ($data as $key => $value) {
            if (!is_numeric($value)) {
                $string = htmlspecialchars($value);
                $info[$key] = '"' . $string . '"';
            } else {
                $info[$key] = $value;
            }
        }
        $values = implode(',', $info);
        $sql = 'INSERT INTO ' . $table . ' (' . $tableheadings . ') VALUES (' . $values . ')';
        $result = $this->query($sql);
        if (!$result) {
            printf('error: ' . $this->error);
        }
        return $this->insert_id;
    }

    function update_record($table, $data) {

        $info = array();
        foreach ($data as $key => $value) {
            if (!is_numeric($value)) {
                $info[$key] = '"' . $value . '"';
            } else {
                $info[$key] = $value;
            }
        }

        $setsql = ' SET ';
        foreach ($info as $key => $value) {
            $setsql .= $key . ' = ' . $value . ', ';
        }
        $setsql = substr($setsql, 0, -2);
        $where = ' WHERE id = ' . $info['id'] . '';
        $sql = 'UPDATE ' . $table . $setsql . $where;
        $result = $this->query($sql);
    }

    function count_records($table, $where) {
        $wheresql = $this->format_where($where);
        $sql = 'SELECT count("id") AS recordcount FROM ' . $table . $wheresql;
        $result = $this->query($sql);
        $count = $result->fetch_object()->recordcount;
        $result->free();
        return $count;
    }

    function delete_records($table, $where) {
        $wheresql = $this->format_where($where);
        $sql = 'DELETE FROM ' . $table . $wheresql;
        $result = $this->query($sql);
    }
    
    /**
     * General method for sql queries
     *
     * @param string $sql SQL statement
     * @param array $params An array of parameters using the key.
     * @return mixed Depends on the statement.
     */
    public function execute_sql($sql, $params = null) {
    	if (isset($params)) {
    	    // For the moment the key for the param needs to be in the following format {:parameter}
    	    foreach ($params as $key => $param) {
	        if (!is_numeric($param)) {
	    	    $param = '"' . $param . '"';
	    	}
	    	$pattern = "/\:"  . $key . "/";
	    	$sql = preg_replace($pattern, $param, $sql);
    	    }
    	}
    	$result = $this->query($sql);
    	$data = array();
        $i = 0;
        while ($rawdata = $result->fetch_assoc()) {
            $data[$i] = new stdClass();
            foreach ($rawdata as $key => $row) {
                $data[$i]->$key = $row;
            }
            $i++;
        }
        $result->free();
        return $data;
    }

    /**
     * Takes an array of where data.
     *
     * @param array $where Where data.
     * @return string $where data now as an sql snippet.
     */
    protected function format_where($where) {
        $count = count($where);
        $wheresql = ' WHERE ';
        foreach ($where as $key => $value) {
            if (is_numeric($value)) {
            	$wheresql .= $key . ' = ' . $value;
            } else {
            	$wheresql .= $key . ' = "' . $value . '"';
            }
            if ($count > 1) {
                $wheresql .= ' AND ';
                $count --;
            }
        }
        return $wheresql;
    }

    public function __destruct() {
    	$this->close();
    }
}


?>