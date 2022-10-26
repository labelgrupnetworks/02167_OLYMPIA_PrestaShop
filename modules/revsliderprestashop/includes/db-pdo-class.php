
<?php


/**
 * Ultimate MySQL PDO database utility class with built-in debugging and logging
 *
 * This database class uses a global PDO database connection to make it easier
 * to retrofit into existing projects or use in new projects. The class is
 * static (you do not need to create any type of object to use it) for easy of
 * use, performance, and convenient code completion. All of the code is in a
 * single file to make it incredibly easy to install and learn. Some basic
 * knowledge of how PDO "placeholders" work is helpful but not necessary. Every
 * effort to use them is applied to stop SQL injection hacks and also because:
 *
 * "There is a common misconception about how the placeholders in prepared
 * statements work. They are not simply substituted in as (escaped) strings,
 * and the resulting SQL executed. Instead, a DBMS asked to "prepare" a
 * statement comes up with a complete query plan for how it would execute that
 * query, including which tables and indexes it would use."
 * http://php.net/manual/en/pdo.prepare.php
 *
 * I also made every efforts to take care of all the details like try/catch
 * error checking, PHP error logging, security, full transaction processing,
 * and using as little memory and being as lightweight as possible while still
 * containing a lot of great features.
 *
 * @author Jeff Williams
 * @version 2.0
 */
class DB {

    /**
     * Determines the name of the global PDO database variable to use internally
     */
    const PDO_DB = 'db';

    /**
     * This is an event function that is called every time there is an error.
     * You can add code into this function to do things such as:
     * 1. Log errors into the database
     * 2. Send an email with the error message
     * 3. Save out to some type of log file
     * 4. Make a RESTful API call
     * 5. Run a script or program
     * 6. Set a session or global variable
     * Or anything you might want to do when an error occurs.
     *
     * @param string $error The error description [$exception->getMessage()]
     * @param int $error_code [OPTIONAL] The error number [$exception->getCode()]
     */
    protected static function ErrorEvent($error, $error_code = 0) {

        // Send this error to the PHP error log
        if (empty($error_code)) {
            error_log($error, 0);
        } else {
            error_log(self::PDO_DB . ' error ' . $error_code . ': ' . $error, 0);
        }

    }

    /**
     * Connects to a MySQL PDO database.
     *
     * NOTE: I chose to pass back an error on this routine because a database
     * connection error is serious and the author might want to send out email
     * or other communications to alert them. If the connection is successful,
     * then FALSE is returned (as in there was not an error to report).
     *
     * @param string $username Database user name
     * @param string $password Database password
     * @param string $database Database or schema name
     * @param string $hostname [OPTIONAL] Host name of the server
     * @param boolean $silent_errors [OPTIONAL] Show no errors on queries
     * @return boolean/string The error if there was one otherwise FALSE
     */
    public static function Connect($username, $password, $database,
        $hostname = 'localhost', $silent_errors = false) {

        try {

            // Connect to the MySQL database
            $GLOBALS[self::PDO_DB] = new PDO('mysql:' .
                'host=' . $hostname . ';' .
                'dbname=' . $database,
                $username, $password);

            // If we are connected...
            if ($GLOBALS[self::PDO_DB]) {

                // The default error mode for PDO is PDO::ERRMODE_SILENT.
                // With this setting left unchanged, you'll need to manually
                // fetch errors, after performing a query
                if (!$silent_errors) {
                    $GLOBALS[self::PDO_DB]->setAttribute(
                        PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                }

                // Connection was successful
                $error = false;

            } else {

                // Connection was not successful
                $error = true;

            }

        // If there was an error...
        } catch (PDOException $e) {

            // Get the error
            $error = 'Database Connection Error (' . __METHOD__ . '): ' .
                $e->getMessage();

            // Send the error to the error event handler
            self::ErrorEvent($error, $e->getCode());

        }

        // Return the results
        return $error;
    }

    /**
     * Executes a SQL statement using PDO
     *
     * @param string $sql SQL
     * @param array $placeholders [OPTIONAL] Associative array placeholders for binding to SQL
     * array("name' => 'Cathy', 'city' => 'Cardiff')
     * @param booplean $debug [OPTIONAL] If set to true, will output results and query info
     * @return boolean (Last INSERT ID if INSERT or) TRUE if success otherwise FALSE
     */
    public static function Execute($sql, $placeholders = false, $debug = false) {

        // Set the variable initial values
        $query = false;
        $count = false;
        $id = false;
        $time = false;

        // Is there already a transaction pending? No nested transactions in MySQL!
        $existing_transaction = $GLOBALS[self::PDO_DB]->inTransaction();

        // Is this a SQL INSERT statement? Check the first word...
        $insert = (strtoupper(strtok(trim($sql), ' '))) === 'INSERT';

        // Set a flag
        $return = false;

        try {

            // Begin a transaction
            if (!$existing_transaction) {
                $GLOBALS[self::PDO_DB]->beginTransaction();
            }

            // Create the query object
            $query = $GLOBALS[self::PDO_DB]->prepare($sql);

            // If there are values in the passed in array
            if (!empty($placeholders) &&
                is_array($placeholders) &&
                count($placeholders) > 0) {

                // Loop through the placeholders and values
                foreach ($placeholders as $field => $value) {

                    // Determine the datatype
                    if (is_int($value)) {
                        $datatype = PDO::PARAM_INT;
                    } elseif (is_bool($value)) {
                        $datatype = PDO::PARAM_BOOL;
                    } elseif (is_null($value)) {
                        $datatype = PDO::PARAM_NULL;
                    } elseif ($value instanceof DateTime) {
                        $value = $value->format('Y-m-d H:i:s');
                        $placeholders[$field] = $value;
                        $datatype = PDO::PARAM_STR;
                    } else {
                        $datatype = PDO::PARAM_STR;
                    }

                    // Bind the placeholder and value to the query
                    $query->bindValue($field, $value, $datatype);
                }
            }

            // Start a timer
            $time_start = microtime(true);

            // Execute the query
            $query->execute();

            // Find out how long the query took
            $time_end = microtime(true);
            $time = $time_end - $time_start;

            // If this was an INSERT...
            if ($insert) {

                // Get the last inserted ID (has to be done before the commit)
                $id = $GLOBALS[self::PDO_DB]->lastInsertId();

            }

            // Debug only
            if ($debug) {

                // Rollback the transaction
                if (!$existing_transaction) {
                    $GLOBALS[self::PDO_DB]->rollback();
                }

                // Output debug information
                self::DumpDebug(__FUNCTION__,
                    $sql, $placeholders, $query, false, $time, $count, $id);

                // Exit
                die();
            }

            // Commit the transaction
            if (!$existing_transaction) {
                $GLOBALS[self::PDO_DB]->commit();
            }

            // If this was an INSERT...
            if ($insert) {

                // Hand back the last inserted ID
                $return = $id;

            } else {

                // Query was successful
                $return = true;

            }

        } catch (PDOException $e) { // If there was an error...

            // Get the error
            $error = 'Database Error (' . __METHOD__ . '): '
                . $e->getMessage() . ' ' . $sql;

            // Send the error to the error event handler
            self::ErrorEvent($error, $e->getCode());

            // If we are in debug mode...
            if ($debug) {

                // Output debug information
                self::DumpDebug(__FUNCTION__,
                    $sql, $placeholders, $query, false, $time, $count, $id, $error);

            }

            // Rollback the transaction
            if (!$existing_transaction) {
                $GLOBALS[self::PDO_DB]->rollback();
            }

        } catch (Exception $e) { // If there was an error...

            // Get the error
            $error = 'General Error (' . __METHOD__ . '): ' . $e->getMessage();

            // Send the error to the error event handler
            self::ErrorEvent($error, $e->getCode());

            // If we are in debug mode...
            if ($debug) {

                // Output debug information
                self::DumpDebug(__FUNCTION__,
                    $sql, $placeholders, $query, false, $time, $count, $id, $error);

            }

            // Rollback the transaction
            if (!$existing_transaction) {
                $GLOBALS[self::PDO_DB]->rollback();
            }

        }

        // Clean up
        unset($query);

        // If this was a successful INSERT with an ID...
        if ($return && $id) {

            // Return the ID instead
            $return = $id;

        }

        // Return [the ID or] true if success and false if failure
        return $return;
    }

    /**
     * Executes a SQL query using PDO and returns records
     *
     * @param string $sql SQL
     * @param array $placeholders [OPTIONAL] Associative array placeholders for binding to SQL
     * array('name' => 'Cathy', 'city' => 'Cardiff')
     * @param booplean $debug [OPTIONAL] If set to true, will output results and query info
     * @param integer $fetch_parameters [OPTIONAL] PDO fetch style record options
     * @return array Array with values if success otherwise FALSE
     */
    public static function Query($sql, $placeholders = false,
        $debug = false, $fetch_parameters = PDO::FETCH_ASSOC) {

        // Set the variable initial values
        $query = false;
        $return = false;
        $time = false;

        try {

            // Create the query object
            $query = $GLOBALS[self::PDO_DB]->prepare($sql);

            // If there are values in the passed in array
            if (!empty($placeholders) &&
                is_array($placeholders) &&
                count($placeholders) > 0) {

                // Loop through the placeholders and values
                foreach ($placeholders as $field => $value) {

                    // Determine the datatype
                    if (is_int($value)) {
                        $datatype = PDO::PARAM_INT;
                    } elseif (is_bool($value)) {
                        $datatype = PDO::PARAM_BOOL;
                    } elseif (is_null($value)) {
                        $datatype = PDO::PARAM_NULL;
                    } elseif ($value instanceof DateTime) {
                        $value = $value->format('Y-m-d H:i:s');
                        $placeholders[$field] = $value;
                        $datatype = PDO::PARAM_STR;
                    } else {
                        $datatype = PDO::PARAM_STR;
                    }

                    // Bind the placeholder and value to the query
                    $query->bindValue($field, $value, $datatype);
                }
            }

            // Start a timer
            $time_start = microtime(true);

            // Execute the query
            $query->execute();

            // Find out how long the query took
            $time_end = microtime(true);
            $time = $time_end - $time_start;

            // Query was successful
            $return = $query->fetchAll($fetch_parameters);

            // Debug only
            if ($debug) {

                // Output debug information
                self::DumpDebug(__FUNCTION__,
                    $sql, $placeholders, $query, $return, $time, count($return));

            }

        } catch (PDOException $e) { // If there was an error...

            // Get the error
            $error = 'Database Error (' . __METHOD__ . '): '
                . $e->getMessage() . ' ' . $sql;

            // Send the error to the error event handler
            self::ErrorEvent($error, $e->getCode());

            // If we are in debug mode...
            if ($debug) {

                // Output debug information
                self::DumpDebug(__FUNCTION__, $sql, $placeholders, $query,
                    $return, $time, false, false, $error);

            }

            // die($e->getMessage());
            $return = false;

        } catch (Exception $e) { // If there was an error...

            // Get the error
            $error = 'General Error (' . __METHOD__ . '): ' . $e->getMessage();

            // Send the error to the error event handler
            self::ErrorEvent($error, $e->getCode());

            // If we are in debug mode...
            if ($debug) {

                // Output debug information
                self::DumpDebug(__FUNCTION__, $sql, $placeholders, $query,
                    $return, $time, false, false, $error);

            }

            // die($e->getMessage());
            $return = false;

        }

        // Clean up
        unset($query);

        // Return results if success and false if failure
        return $return;
    }

    /**
     * Executes a SQL query using PDO and returns one row
     *
     * @param string $sql SQL
     * @param array $placeholders [OPTIONAL] Associative array placeholders for binding to SQL
     * array('name' => 'Cathy', 'city' => 'Cardiff')
     * @param booplean $debug [OPTIONAL] If set to true, will output results and query info
     * @param integer $fetch_parameters [OPTIONAL] PDO fetch style record options
     * @return array Array with values if success otherwise FALSE
     */
    public static function QueryRow($sql, $placeholders = false,
        $debug = false, $fetch_parameters = PDO::FETCH_ASSOC) {

        // It's better on resources to add LIMIT 1 to the end of your SQL
        // statement if there are multiple rows that will be returned
        $results = self::Query($sql, $placeholders, $debug, $fetch_parameters);

        // If one or more records were returned
        if (is_array($results) && count($results) > 0) {

            // Return the first element of the array which is the first row
            return $results[key($results)];

        } else {

            // No records were returned
            return false;

        }
    }

    /**
     * Executes a SQL query using PDO and returns a single value only
     *
     * @param string $sql SQL
     * @param array $placeholders Associative array placeholders for binding to SQL
     * array('name' => 'Cathy', 'city' => 'Cardiff')
     * @param booplean $debug If set to true, will output results and query info
     * @return unknown A returned value from the database if success otherwise FALSE
     */
    public static function QueryValue($sql, $placeholders = false, $debug = false) {

        // It's better on resources to add LIMIT 1 to the end of your SQL
        // if there are multiple rows that will be returned
        $results = self::QueryRow($sql, $placeholders, $debug, PDO::FETCH_NUM);

        // If a record was returned
        if (is_array($results)) {

            // Return the first element of the array which is the first row
            return $results[0];

        } else {

            // No records were returned
            return false;

        }
    }

    /**
     * Selects records from a table using PDO
     *
     * @param string $table Table name
     * @param array $values [OPTIONAL] Array or string containing the field names
     * array('name', 'city') or 'name, city'
     * @param array/string [OPTIONAL] $where Array containing the fields and values or a string
     * $where = array();
     * $where['id >'] = 1234;
     * $where[] = 'first_name IS NOT NULL';
     * $where['some_value <>'] = 'text';
     * @param array/string [OPTIONAL] $order Array or string containing field order
     * @param booplean $debug [OPTIONAL] If set to true, will output results and query info
     * @param integer $fetch_parameters [OPTIONAL] PDO fetch style record options
     * @return array Array with values if success otherwise FALSE
     */
    public static function Select($table, $values = '*', $where = false,
        $order = false, $debug = false, $fetch_parameters = PDO::FETCH_ASSOC) {

        // If the values are in an array
        if (is_array($values)) {

            // Join the fields
            $sql = 'SELECT ' . implode(', ', $values);

        } else { // It's a string

            // Create the SELECT
            $sql = 'SELECT ' . trim($values);

        }

        // Create the SQL WHERE clause
        $where_array = self::WhereClause($where);
        $sql .= ' FROM ' . trim($table) . $where_array['sql'];

        // If the order values are in an array
        if (is_array($order)) {

            // Join the fields
            $sql .= ' ORDER BY ' . implode(', ', $order);

        } elseif ($order) { // It's a string

            // Specify the order
            $sql .= ' ORDER BY ' . trim($order);

        }

        // Execute the query and return the results
        return self::Query($sql, $where_array['placeholders'],
            $debug, $fetch_parameters);
    }

    /**
     * Selects a single record from a table using PDO
     *
     * @param string $table Table name
     * @param array $values [OPTIONAL] Array or string containing the field names
     * array('name', 'city') or 'name, city'
     * @param array/string [OPTIONAL] $where Array containing the fields and values or a string
     * $where = array();
     * $where['id >'] = 1234;
     * $where[] = 'first_name IS NOT NULL';
     * $where['some_value <>'] = 'text';
     * @param booplean $debug [OPTIONAL] If set to true, will output results and query info
     * @param integer $fetch_parameters [OPTIONAL] PDO fetch style record options
     * @return array Array with values if success otherwise FALSE
     */
    public static function SelectRow($table, $values = '*', $where = false,
        $debug = false, $fetch_parameters = PDO::FETCH_ASSOC) {

        // If the values are in an array
        if (is_array($values)) {

            // Join the fields
            $sql = 'SELECT ' . implode(', ', $values);

        } else { // It's a string

            // Create the SELECT
            $sql = 'SELECT ' . trim($values);

        }

        // Create the SQL WHERE clause
        $where_array = self::WhereClause($where);
        $sql .= ' FROM ' . trim($table) . $where_array['sql'];

        // Make sure only one row is returned
        $sql .= ' LIMIT 1';

        // Execute the query and return the results
        return self::QueryRow($sql, $where_array['placeholders'],
            $debug, $fetch_parameters);
    }

    /**
     * Selects a single record from a table using PDO
     *
     * @param string $table Table name
     * @param string $field The name of the field to return
     * @param array/string [OPTIONAL] $where Array containing the fields and values or a string
     * $where = array();
     * $where['id >'] = 1234;
     * $where[] = 'first_name IS NOT NULL';
     * $where['some_value <>'] = 'text';
     * @param booplean $debug [OPTIONAL] If set to true, will output results and query info
     * @param integer $fetch_parameters [OPTIONAL] PDO fetch style record options
     * @return array Array with values if success otherwise FALSE
     */
    public static function SelectValue($table, $field, $where = false, $debug = false) {

        // Return the row
        $results = self::SelectRow($table, $field, $where, $debug, PDO::FETCH_NUM);

        // If a record was returned
        if (is_array($results)) {

            // Return the first element of the array which is the first row
            return $results[0];

        } else {

            // No records were returned
            return false;

        }

    }

    /**
     * Inserts a new record into a table using PDO
     *
     * @param string $table Table name
     * @param array $values Associative array containing the fields and values
     * array('name' => 'Cathy', 'city' => 'Cardiff')
     * @param booplean $debug [OPTIONAL] If set to true, will output results and query info
     * @return boolean Returns the last inserted ID or TRUE otherwise FALSE
     */
    public static function Insert($table, $values, $debug = false) {

        // Create the SQL statement with PDO placeholders created with regex
        $sql = 'INSERT INTO ' . trim($table) . ' ('
        . implode(', ', array_keys($values)) . ') VALUES ('
        . implode(', ', preg_replace('/^([A-Za-z0-9_-]+)$/', ':${1}', array_keys($values)))
        . ')';

        // Execute the query
        return self::Execute($sql, $values, $debug);
    }

    /**
     * Updates an existing record into a table using PDO
     *
     * @param string $table Table name
     * @param array $values Associative array containing the fields and values
     * array('name' => 'Cathy', 'city' => 'Cardiff')
     * @param array/string $where [OPTIONAL] Array containing the fields and values or a string
     * $where = array();
     * $where['id >'] = 1234;
     * $where[] = 'first_name IS NOT NULL';
     * $where['some_value <>'] = 'text';
     * @param booplean $debug [OPTIONAL] If set to true, will output results and query info
     * @return boolean TRUE if success otherwise FALSE
     */
    public static function Update($table, $values, $where = false, $debug = false) {

        // Create the initial SQL
        $sql = 'UPDATE ' . trim($table) . ' SET ';

        // Create SQL SET values
        $output = array();
        foreach ($values as $key => $value) {
            $output[] = $key . ' = :' . $key;
        }

        // Concatenate the array values
        $sql .= implode(', ', $output);

        // Create the SQL WHERE clause
        $where_array = self::WhereClause($where);
        $sql .= $where_array['sql'];

        // Execute the query
        return self::Execute($sql,
            array_merge($values, $where_array['placeholders']), $debug);
    }

    /**
     * Deletes a record from a table using PDO
     *
     * @param string $table Table name
     * @param array/string $where [OPTIONAL] Array containing the fields and values or a string
     * $where = array();
     * $where['id >'] = 1234;
     * $where[] = 'first_name IS NOT NULL';
     * $where['some_value <>'] = 'text';
     * @param booplean $debug [OPTIONAL] If set to true, will output results and query info
     * @return boolean TRUE if success otherwise FALSE
     */
    public static function Delete($table, $where = false, $debug = false) {

        // Create the SQL
        $sql = 'DELETE FROM ' . trim($table);

        // Create the SQL WHERE clause
        $where_array = self::WhereClause($where);
        $sql .= $where_array['sql'];

        // Execute the query
        return self::Execute($sql, $where_array['placeholders'], $debug);
    }

    /**
     * Begin transaction processing
     *
     */
    public static function TransactionBegin() {
        try {

            // Begin transaction processing
            $success = $GLOBALS[self::PDO_DB]->beginTransaction();

        } catch (PDOException $e) { // If there was an error...

            // Return false to show there was an error
            $success = false;

            // Send the error to the error event handler
            self::ErrorEvent('Database Error (' . __METHOD__ . '): ' .
                $e->getMessage(), $e->getCode());

        } catch (Exception $e) { // If there was an error...

            // Return false to show there was an error
            $success = false;

            // Send the error to the error event handler
            self::ErrorEvent('General Error (' . __METHOD__ . '): ' .
                $e->getMessage(), $e->getCode());

        }

        return $success;
    }

    /**
     * Commit and end transaction processing
     *
     */
    public static function TransactionCommit() {
        try {

            // Commit and end transaction processing
            $success = $GLOBALS[self::PDO_DB]->commit();

        } catch (PDOException $e) { // If there was an error...

            // Return false to show there was an error
            $success = false;

            // Send the error to the error event handler
            self::ErrorEvent('Database Error (' . __METHOD__ . '): ' .
                $e->getMessage(), $e->getCode());

        } catch (Exception $e) { // If there was an error...

            // Return false to show there was an error
            $success = false;

            // Send the error to the error event handler
            self::ErrorEvent('General Error (' . __METHOD__ . '): ' .
                $e->getMessage(), $e->getCode());

        }

        return $success;
    }

    /**
     * Roll back transaction processing
     *
     */
    public static function TransactionRollback() {
        try {

            // Roll back transaction processing
            $success = $GLOBALS[self::PDO_DB]->rollback();

        } catch (PDOException $e) { // If there was an error...

            // Return false to show there was an error
            $success = false;

            // Send the error to the error event handler
            self::ErrorEvent('Database Error (' . __METHOD__ . '): ' .
                $e->getMessage(), $e->getCode());

        } catch (Exception $e) { // If there was an error...

            // Return false to show there was an error
            $success = false;

            // Send the error to the error event handler
            self::ErrorEvent('General Error (' . __METHOD__ . '): ' .
                $e->getMessage(), $e->getCode());

        }

        return $success;
    }

    /**
     * Converts a Query() or Select() array of records into a simple array
     * using only one column or an associative array using another column as a key
     *
     * @param array $array The array returned from a PDO query using fetchAll
     * @param string $value_field The name of the field that holds the value
     * @param string $key_field [OPTIONAL] The name of the field that holds the key
     * making the return value an associative array
     * @return array Returns an array with only the specified data
     */
    public static function ConvertQueryToSimpleArray($array, $value_field, $key_field = false) {
        // Create an empty array
        $return = array();

        // Loop through the query results
        foreach ($array as $element) {

            // If we have a key
            if ($key_field) {
                // Add this key
                $return[$element[$key_field]] = $element[$value_field];
            } else { // No key field
                // Append to the array
                $return[] = $element[$value_field];
            }
        }

        // Return the new array
        return $return;
    }

    /**
     * This function returns a SQL query as an HTML table
     *
     * @param string $sql SQL
     * @param array $placeholders [OPTIONAL] Associative array placeholders for binding to SQL
     * array("name' => 'Cathy', 'city' => 'Cardiff')
     * @param boolean $showCount (Optional) TRUE if you want to show the row count,
     * FALSE if you do not want to show the count
     * @param string $styleTable (Optional) Style information for the table
     * @param string $styleHeader (Optional) Style information for the header row
     * @param string $styleData (Optional) Style information for the cells
     * @return string HTML containing a table with all records listed
     */
    public static function GetHTML($sql, $placeholders = false, $showCount = true,
        $styleTable = null, $styleHeader = null, $styleData = null) {

        // Set default style information
        if ($styleTable === null) {
            $tb = "border-collapse:collapse;empty-cells:show";
        } else {
            $tb = $styleTable;
        }
        if ($styleHeader === null) {
            $th = "border-width:1px;border-style:solid;background-color:navy;color:white";
        } else {
            $th = $styleHeader;
        }
        if ($styleData === null) {
            $td = "border-width:1px;border-style:solid";
        } else {
            $td = $styleData;
        }

        // Get the records
        $records = DB::Query($sql, $placeholders);

        // If there was no error...
        if (is_array($records)) {

            // If records were returned...
            if (count($records) > 0) {

                // Begin the table
                $html = "";
                if ($showCount) $html = "Total Count: " . count($records) . "<br />\n";
                $html .= "<table style=\"$tb\" cellpadding=\"2\" cellspacing=\"2\">\n";

                // Create the header row
                $html .= "\t<tr>\n";
                foreach (array_keys($records[0]) as $value) {
                    $html .= "\t\t<td style=\"$th\"><strong>" . htmlspecialchars($value) . "</strong></td>\n";
                }
                $html .= "\t</tr>\n";

                // Create the rows with data
                foreach ($records as $row) {
                    $html .= "\t<tr>\n";
                    foreach ($row as $key => $value) {
                        $html .= "\t\t<td style=\"$td\">" . htmlspecialchars($value) . "</td>\n";
                    }
                    $html .= "\t</tr>\n";
                }

                // Close the table
                $html .= "</table>";

            } else { // No records were returned
                $html = "No records were returned.";
            }

        } else { // There was an error with the SQL
            $html = false;
        }

        // Return the table HTML code
        return $html;
    }

    /**
     * Converts empty values to NULL
     *
     * @param unknown_type $value Any value
     * @param boolean $includeZero Include 0 as NULL?
     * @param boolean $includeFalse Include FALSE as NULL?
     * @param boolean $includeBlankString Include a blank string as NULL?
     * @return unknown_type The value or NULL if empty
     */
    public static function EmptyToNull($value, $includeZero = true,
        $includeFalse = true, $includeBlankString = true) {
        $return = $value;
        if (!$includeFalse && $value === false) {
            // Skip
        } elseif (!$includeZero && ($value === 0 || trim($value) === '0')) {
            // Skip
        } elseif (!$includeBlankString && trim($value) === '') {
            // Skip
        } elseif (is_string($value)) {
            if (strlen(trim($value)) == 0) {
                $return = null;
            } else {
                $return = trim($value);
            }
        } elseif (empty($value)) {
            $return = null;
        }
        return $return;
    }

    /**
     * Returns a quoted string that is safe to pass into an SQL statement
     *
     * @param string $value A string value or DateTime object
     * @return string The newly encoded string with quotes
     */
    public static function Safe($value) {

        // If it's a string...
        if (is_string($value)) {

            // Use PDO to encode it
            return $GLOBALS[self::PDO_DB]->quote($value);

        // If it's a DateTime object...
        } elseif ($value instanceof DateTime) {

            // Format the date as a string for MySQL and use PDO to encode it
            return $GLOBALS[self::PDO_DB]->quote($value->format('Y-m-d H:i:s'));

        // It's something else...
        } else {

            // Return the original value
            return $value;

        }
    }

    /**
     * Builds a SQL WHERE clause from an array
     *
     * @param array/string $where Array containing the fields and values or a string
     * $where = array();
     * $where['id >'] = 1234;
     * $where[] = 'first_name IS NOT NULL';
     * $where['some_value <>'] = 'text';
     * @return array An associative array with both a 'sql' and 'placeholders' key
     */
    protected static function WhereClause($where) {

        // Create an array to hold the place holder values (if any)
        $placeholders = array();

        // Create a variable to hold SQL
        $sql = '';

        // If an array was passed in...
        if (is_array($where)) {

            // Create an array to hold the WHERE values
            $output = array();

            // loop through the array
            foreach ($where as $key => $value) {

                // If a key is specified for a PDO place holder field...
                if (is_string($key)) {

                    // Extract the key
                    $extracted_key = preg_replace('/^(\s*)([^\s=<>]*)(.*)/',
                        '${2}', $key);

                    // If no < > = was specified...
                    if (trim($key) == $extracted_key) {

                        // Add the PDO place holder with an =
                        $output[] = trim($key) . ' = :' . $extracted_key;

                    } else { // A comparison exists...

                        // Add the PDO place holder
                        $output[] = trim($key) . ' :' . $extracted_key;

                    }

                    // Add the placeholder replacement values
                    $placeholders[$extracted_key] = $value;

                } else { // No key was specified...

                    $output[] = $value;

                }
            }

            // Concatenate the array values
            $sql = ' WHERE ' . implode(' AND ', $output);

        } elseif ($where) {
            $sql = ' WHERE ' . trim($where);
        }

        // Set the place holders to false if none exist
        if (count($placeholders) == 0) {
            $placeholders = false;
        }

        // Return the sql and place holders
        return array(
            "sql" => $sql,
            "placeholders" => $placeholders);
    }

    /**
     * Dump debug information to the screen
     *
     * @param string $source The source to show on the debug output
     * @param string $sql SQL
     * @param array $placeholders [OPTIONAL] Placeholders array
     * @param object $query [OPTIONAL] PDO query object
     * @param int $count [OPTIONAL] The record count
     * @param int $id [OPTIONAL] Last inserted ID
     * @param string $error [OPTIONAL] Error text
     */
    private static function DumpDebug($source, $sql, $placeholders = false,
        $query = false, $records = false, $time = false,
        $count = false, $id = false, $error = false) {

        // Format the source
        $source = strtoupper($source);

        // If there was an error specified
        if ($error) {

            // Show the error information
            print "\n<br>\n--DEBUG " . $source . " ERROR FROM " . self::PDO_DB . "--\n<pre>";
            print_r($error);

        }

        // If the number of seconds is specified...
        if ($time !== false) {
            // Show how long it took
            print "</pre>\n--DEBUG " . $source . " TIMER FROM " . self::PDO_DB . "--\n<pre>\n";
            echo number_format($time, 6) . ' ms';
        }

        // Output the SQL
        print "</pre>\n--DEBUG " . $source . " SQL FROM " . self::PDO_DB . "--\n<pre>";
        print_r($sql);

        // If there were placeholders passed in...
        if ($placeholders) {

            // Show the placeholders
            print "</pre>\n--DEBUG " . $source . " PARAMS FROM " . self::PDO_DB . "--\n<pre>";
            print_r($placeholders);

        }

        // If a query object exists...
        if ($query) {

            // Show the query dump
            print "</pre>\n--DEBUG " . $source . " DUMP FROM " . self::PDO_DB . "--\n<pre>";
            print_r($query->debugDumpParams());

        }

        // If records were returned...
        if ($count !== false) {

            // Show the count
            print "</pre>\n--DEBUG " . $source . " ROW COUNT FROM " . self::PDO_DB . "--\n<pre>";
            print_r($count);

        }

        // If this was an INSERT with an ID...
        if ($id) {

            // Show the last inserted ID
            print "</pre>\n--DEBUG LAST INSERTED ID FROM " . self::PDO_DB . "--\n<pre>";
            print_r($id);

        }

        // If records were returned...
        if ($records) {

            // Show the rows returned
            print "</pre>\n--DEBUG " . $source . " RETURNED RESULTS FROM " . self::PDO_DB . "--\n<pre>";
            print_r($records);

        }

        // End the debug output
        print "</pre>\n--DEBUG " . $source . " END FROM " . self::PDO_DB . "--\n<br>\n";
    }
}


class RevSliderDB extends DB{

  public static $wpdb;
  public $database = 'revolution6';
  public $username = 'ps';
  public $password = '1234';
  public $prefix;

  public function __construct()
  {
  // Connect to the database
  $dbConnection = DB::Connect($this->username, $this->password, $this->database, 'localhost');
  // If there was an error, stop and display it
  if ($dbConnection) die($dbConnection);
     
  }
}

new RevSliderDB();

//http://safe.phpclasses.net/browse/view/html/file/200907/name/DB_help.html

 function backupcode(){

// Connect to the database
$error = DB::Connect('username', 'password', 'database', 'hostname');
// If there was an error, stop and display it
if ($error) die($error);
// Execute a SQL query and return whether it was successful or not
// (FYI: You can also use DB::Safe() to return a safe quoted string for SQL)
$sql = "INSERT INTO test_table (name, age, active) VALUES ('Sophia', 20, true)";
$success = DB::Execute($sql);
// Execute a SQL query with placeholders (better because it stops SQL Injection hacks)
$sql = 'INSERT INTO test_table (name, age, active) VALUES (:name, :age, :active)';
$values = array('name' => 'Lucas', 'age' => 45, 'active' => true);
$success = DB::Execute($sql, $values);

// Execute the same SQL statement but only in debug mode
// In debug mode, the record will not be saved
$success = DB::Execute($sql, $values, true);

// Execute a SQL query to return an array containing all rows
$sql = 'SELECT * FROM test_table';
$rows = DB::Query($sql);

// Show the array
print_r($rows);

// Execute a SQL query using placeholders; this will return an array with all rows
$sql = 'SELECT id, name, age FROM test_table WHERE active = :active';
$values = array('active' => true);
$rows = DB::Query($sql, $values);

// Execute the same query in debug mode
$rows = DB::Query($sql, $values, true);

// Let do the same query without using SQL
$columns = array('id', 'name', 'age');
$where = array('active' => true);
$rows = DB::Select('test_table', $columns, $where);

// We can make more complex where clauses in the Select, Update, and Delete methods
$columns = array('id', 'name', 'age');
$where = array(
    'active IS NOT NULL',
    'id > 10',
    'UPPER(name) LIKE %JEN%'
);
$rows = DB::Select('test_table', $columns, $where);

// Let's sort by ID and run it in debug mode
$rows = DB::Select('test_table', $columns, $where, 'id', true);

// Grab one value - get the name of the person in the record with ID 1
$value = DB::SelectValue('test_table', 'name', array('id' => 1));

// Insert a new record
$values = array('name' => 'Riley', 'age' => 30, 'active' => false);
$success = DB::Insert('test_table', $values);

// Try it in debug mode
$success = DB::Insert('test_table', $values, true);

// Update an existing record
$update = array('age' => 35);
$where = array('name' => 'Riley');
$success = DB::Update('test_table', $update, $where);

// Delete records
$where = array('active' => false);
$success = DB::Delete('test_table', $where);

}

