<?php

require_once 'db-pdo-class.php';

class rev_db_class
{

    public static $wpdb;
    public $sdsdb;
    public $mysqli;
    public $prefix;
    public $insert_id;
    public $database = 'revolution6';
    public $username = 'ps';
    public $password = '1234';

    public function __construct()
    {
        $this->prefix = DB_PREFIX;
    }

    public function _real_escape($string)
    {
        //  return $this->sdsdb->escape($string);
        return $this->escape($string);
    }
    public static function escape($text, $extra = false)
    {
        if (is_int($text) || is_float($text)) {
            return $text;
        }
        $text = str_replace("'", "''", $text);
        return addcslashes($text, "\000\n\r\\\032");
    }
    public static function _escape($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                if (is_array($v))
                    //$data[$k] = DB::connection()->getPdo()->quote($v);
                    $data[$k] = $v;
                else
                    $data[$k] = self::escape($v);
            }
        } else {
            //  $data = $this->sdsdb->escape( $data );
            $data = self::escape($data);;
        }



        return $data;
    }
    public function runSql($query)
    {
        //global $wpdb; 
        //$this->query($query);	
        DB::statement(DB::raw($query));
        $this->checkForErrors("Regular query error");
    }
    private function throwError($message, $code = -1)
    {
        RevSliderFunctions::throwError($message, $code);
    }

    public function fetch($tableName, $where = "", $orderField = "", $groupByField = "", $sqlAddon = "")
    {
        // $shopify_token = get_shopify_token();
        //var_dump($shopify_token);die();
        //global $wpdb;
        $wpdb = $this->sdsdb;
        $query = "select * from $tableName";
        if ($where) {
            $query .= " where $where";
        }


        if ($orderField) $query .= " order by $orderField";
        if ($groupByField) $query .= " group by $groupByField";
        if ($sqlAddon) $query .= " " . $sqlAddon;


        $response = $this->get_results($query, ARRAY_A);


        $this->checkForErrors("fetch");

        return ($response);
    }



    private function checkForErrors($prefix = "")
    {

        $errno = '';

        if (!empty($errno)) {

            $message = '';

            $message = '';

            $this->throwError($message);
        }
    }
    /**
     * 
     * fetch only one item. if not found - throw error
     */
    public function fetchSingle($tableName, $where = "", $orderField = "", $groupByField = "", $sqlAddon = "")
    {
        $response = $this->fetch($tableName, $where, $orderField, $groupByField, $sqlAddon);
        if (empty($response))
            $this->throwError("Record not found");
        $record = $response[0];
        return ($record);
    }
    public function frontFetch($tableName, $where = "", $orderField = "", $groupByField = "", $sqlAddon = "")
    {

        //var_dump($shopify_token);die();
        //global $wpdb;
        $wpdb = $this->sdsdb;
        $query = "select * from $tableName";
        if ($where) {
            $query .= " where $where";
        }

        if ($orderField) $query .= " order by $orderField";
        if ($groupByField) $query .= " group by $groupByField";
        if ($sqlAddon) $query .= " " . $sqlAddon;

        $response = $this->get_results($query, ARRAY_A);
        $this->checkForErrors("fetch");
        if (empty($response))
            $this->throwError("Record not found");
        $record = $response[0];
        return ($record);
        //return($response);
    }

    // Execute a SQL query and return whether it was successful or not
    // (FYI: You can also use DB::Safe() to return a safe quoted string for SQL)
    public function execute($sql)
    {
        $results = DB::Execute($sql);
        if ($results) {
            return true;
        } else {
            return FALSE;
        }
    }

    /**
     * prepare statement to avoid sql injections
     */

    public function query($sql)
    {

        $results = DB::Execute($sql);
        if ($results)
         $this->insert_id = $results;
            return true;
        return FALSE;
    }

    public function getLastInsertID()
    {
        return DB::getPdo()->lastInsertId();
    }

    public function delete($table, $where)
    {
        //global $wpdb; 
        //$shopify_token = get_shopify_token();
        //track
        //RevSliderFunctions::validateNotEmpty($table,"table name");
        //RevSliderFunctions::validateNotEmpty($where,"where");
        $where_string = "";
        if (!empty($where) && is_array($where)) {
            $where_string .= " ";
            $wherestr = '';
            $c = 0;
            foreach ($where as $k => $val) {
                if ($c > 0)
                    $wherestr .= " AND ";

                $wherestr .= "{$k}=";
                if (is_string($val))
                    $wherestr .= '"' . $this->_escape($val) . '"';
                else
                    $wherestr .= $val;

                $c++;
            }
            $where_string .= $wherestr;
        } else {
            $where_string = $where;
        }
        $query = "delete from $table where $where_string ";
        DB::Execute($query);
        $this->checkForErrors("Delete query error");
    }
    public function update($table, $data, $where = '', $limit = 0, $null_values = false, $use_cache = true, $add_prefix = false)
    {
        //$shopify_token = get_shopify_token();
        $wherestr = '';
        $c = 0;
        $sql = "UPDATE {$table} SET ";
        if (!empty($data))
            foreach ($data as $k => $d) {
                if ($c > 0)
                    $sql .= ', ';

                if (is_string($d))
                    $sql .= "$k=\"" . addslashes($d) . "\"";
                else {
                    $sql .= "$k=$d";
                }

                $c++;
            }
        $sql .= " ";
        $c = 0;
        if (!empty($where) && is_array($where)) {
            //$sql .= "WHERE shopify_token = '$shopify_token' AND ";
            $sql .= "WHERE ";
            foreach ($where as $k => $val) {
                if ($c > 0)
                    $wherestr .= " AND ";
                $wherestr .= "{$k}=";
                if (is_string($val))
                    $wherestr .= '"' . $this->_escape($val) . '"';
                else
                    $wherestr .= $val;
                $c++;
            }
            $sql .= $wherestr;
        }

        if (DB::Execute($sql))
            return true;
        return false;
    }


    public function insert($table, $data, $null_values = false, $use_cache = true, $type = 1, $add_prefix = false)
    {

        $c = 0;
        $cols = '';
        $vals = '';
        $sql = "INSERT INTO {$table}";

        if (!empty($data)) {
            // $data['shopify_token'] = get_shopify_token();
            $cols .= '(';
            $vals .= ' VALUES(';
            foreach ($data as $k => $d) {
                if ($c > 0) {
                    $cols .= ', ';
                    $vals .= ', ';
                }
                $cols .= $k;

                if (is_string($d))
                    //$vals .= "\"".addslashes($d)."\"";
                    $vals .= "'" . addslashes($d) . "'";
                else {
                    $vals .= $d;
                }

                $c++;
            }
            $cols .= ')';
            $vals .= ')';
        } else {
        }

        $sql .= "{$cols} {$vals}";
        $insertedId = DB::Execute($sql);
        if ($insertedId)
            return $this->insert_id = $insertedId;
        return false;
    }

    public function Insert_ID()
    {
        return DB::getPdo()->lastInsertId();
    }

    public function get_var($sql, $case = null)
    {
        $sql .= ' LIMIT 1';
        $results = DB::Query($sql);
        if (isset($results[0])) {
            $results = (array)$results[0];
        }
        if (!empty($results)) {
            return array_shift($results);
        }
        return false;
    }

    public function get_row($sql)
    {

        $sql .= ' LIMIT 1';
        $results = DB::Query($sql);
        if (isset($results[0])) {
            $results = (array)$results[0];
        }

        return $results;
        return false;
    }

    public function get_results($sql)
    {
        //$results = DB::select( DB::raw($sql));
        $results = DB::Query($sql);
        //track
        return $results;
        if (!empty($results))
             return $results;
            //return json_decode(json_encode($results), true);
        return false;
    }
    public function runSqlR($query)
    {
        $return = $this->get_results($query, ARRAY_A);
        return $return;
    }
    public function prepare($query, $args)
    {
        if (is_null($query))
            return;
        // This is not meant to be foolproof -- but it will catch obviously incorrect usage.

        $args = func_get_args();
        array_shift($args);
        // If args were passed as an array (as in vsprintf), move them up
        if (isset($args[0]) && is_array($args[0]))
            $args = $args[0];
        $query = str_replace("'%s'", '%s', $query); // in case someone mistakenly already singlequoted itpublic function insert
        $query = str_replace('"%s"', '%s', $query); // doublequote unquoting
        $query = preg_replace('|(?<!%)%f|', '%F', $query); // Force floats to be locale unaware
        $query = preg_replace('|(?<!%)%s|', "'%s'", $query); // quote the strings, avoiding escaped strings like %%s
        array_walk($args, array($this, 'escape_by_ref'));
        return @vsprintf($query, $args);
    }
    public function escape_by_ref(&$string)
    {
        if (!is_float($string))
            $string = $this->_real_escape($string);
    }

    public static function rev_db_instance()
    {
        if (!self::$wpdb instanceof rev_db_class) {
            return self::$wpdb = new rev_db_class();
        }
        return self::$wpdb;
    }
}