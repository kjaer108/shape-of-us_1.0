<?php
/* *************************************************************************
    This file establishes the database connection and contains database
    functions that are used throughout the system.

    It is included from the init.php file.
   ************************************************************************* */

// Setup control variables
$use_test_db = FALSE;                               // Set to TRUE to use the test database


// Define global constants
define("LOG_DB", "zandora_net_db_log");             // Name of the log database


/* *************************************************************************
    Establish database connection
   ************************************************************************* */

// Make sure we can only use the LIVE database when we are running from LIVE
if (in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    $cur_db = "zandora_net_db".($use_test_db ? "_test" : "");
} else {
    $cur_db = "zandora_net_db";
}

// It's possible to force the test_db on live through ?testdb in url
if (isset($_GET['testdb'])) { $cur_db = "zandora_net_db_test"; }

// Setup connection to database
$host = $config['DB']['host'];
$user = $config['DB']['username'];
$pass = $config['DB']['password'];
$charset = 'utf8mb4';

// Connect to database
// https://stackoverflow.com/questions/4380813/how-to-get-rid-of-mysql-error-prepared-statement-needs-to-be-re-prepared
$dsn = "mysql:host=$host;dbname=$cur_db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => true                       // Was true, but changed to false to allow for LIMIT in pdo_get_rows
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    trigger_error('Could not connect to database: ' . $e->getMessage(), E_USER_ERROR);
}

unset($use_test_db);
unset($cur_db);
unset($host);
unset($user);
unset($pass);
unset($charset);
unset($dsn);
unset($options);


/* *************************************************************************
    Database helper functions
   ************************************************************************* */

/*
The helper functions themselves is quite lightweight and should have negligible
performance impact. The main factors influencing performance are going to be:

1. Database Performance:
========================
- The efficiency of the SQL queries being executed.
- The load and performance of your database server.
- The indexing of your database tables.
- Network latency between your web server and your database server (if they
  are not the same machine).

2. PHP Execution Time:
======================
 - The overall load and available resources on your web server.
 - The complexity and efficiency of the PHP code calling this function.

3. PDO Overhead:
================
There is some overhead associated with using PDO and prepared statements, but
it's generally minimal and is usually outweighed by the benefits such as SQL
injection protection and improved code maintainability.

Recommendations for Performance Optimization:

- Optimize SQL Queries: Ensure that your SQL queries are optimized, use indexes
  where appropriate, and avoid SELECT * queries; instead, specify the columns
  you need.

- Use Persistent Connections: If your application makes a lot of database
  queries, consider using persistent connections to reduce the overhead of
  establishing a new connection every time.

- Use a Connection Pooling: If possible, use a connection pooling
  mechanism to manage and share database connections among multiple PHP
  requests.

- Optimize PHP Code: Optimize the PHP code that is using this function, by
  reducing the amount of unnecessary computations and function calls.

- Use Caching: Consider caching the results of database queries if they do not
  change often, to reduce the load on the database server.

- Use Adequate Hardware and Software: Ensure that your web and database servers
  have adequate resources (CPU, RAM, Disk I/O) and are configured correctly,
  and consider using a PHP accelerator like OPcache.

- Batch Operations: If you are dealing with a large amount of data, try to use
  batch operations to reduce the number of individual queries sent to the
  database.

Remember, optimizing application performance usually involves addressing
bottlenecks in many different areas, and it's often necessary to profile
the application to identify where the most significant gains can be made. */

/*
    Example usage:
        $sql = "SELECT * FROM `shop` WHERE `ID` = :id";

        $params = [':id' => 1];

        $params = [
            ':age' => ['value' => 21, 'type' => PDO::PARAM_INT]
        ];

        $params = [
            ':username' => 'john_doe',
            ':password' => 'some_password',
            ':age' => ['value' => 30, 'type' => PDO::PARAM_INT]
        ];

        $shop = pdo_get_row($pdo, $sql, $params);
*/
function bindParameters($stmt, $params) {
    foreach ($params as $param => $data) {
        if (is_array($data)) {
            $value = $data['value'];
            $type = $data['type'] ?? null; // Use provided type or default to null
            if ($type) {
                $stmt->bindValue($param, $value, $type);
            } else {
                $stmt->bindValue($param, $value); // Let PDO guess the type
            }
        } else {
            $stmt->bindValue($param, $data); // Let PDO guess the type
        }
    }
}

function pdo_get_row($pdo, $sql, $params = []) {
    try {

        // Prepare statement
        $stmt = $pdo->prepare($sql);

        // Bind parameters, if any
        if (!empty($params)) bindParameters($stmt, $params);

        // Execute statement
        $stmt->execute();

        // Fetch the first row of the result set as an associative array
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the row, or an empty array if no row was returned
        return $row ?: null;

    } catch (PDOException $e) {
        throw new Exception("Database error GET_ROW: " . $e->getMessage());
    } finally {
        // Close the cursor and set the statement to null
        if (isset($stmt) && $stmt instanceof PDOStatement) {
            $stmt->closeCursor();
            $stmt = null;
        }
    }
}

function pdo_get_rows($pdo, $sql, $params = []) {
    try {
        // Prepare statement
        $stmt = $pdo->prepare($sql);

        // Bind parameters, if any
        if (!empty($params)) bindParameters($stmt, $params);

        // Execute statement
        $stmt->execute();

        // Return the PDOStatement object to the caller
        return $stmt;

    } catch (PDOException $e) {
        // If there is an error, clean up and throw an exception
        if (isset($stmt) && $stmt instanceof PDOStatement) {
            $stmt->closeCursor();
            $stmt = null;
        }
        throw new Exception("Database error GET_ROWS: " . $e->getMessage());
    }
}

/*
Example Usage:

    try {
        $sql = "SELECT * FROM segments";
        $params = []; // use appropriate parameters as per your query

        pdo_get_rows($pdo, $sql, $params, function($row) {
            // Process each row as required
            $segments[] = ["id" => $row['seg_id'], "name" => $row['seg_name'], "shortname" => $row['seg_shortname']];
        });
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
*/
function pdo_process_rows($pdo, $sql, $params = [], callable $processRow) {
    $returnValue = null; // Variable to capture return value from the callback
    $breakLoop = false; // Flag to indicate loop break without a return value

    try {
        // Prepare statement
        $stmt = $pdo->prepare($sql);

        // Bind parameters, if any
        if (!empty($params)) bindParameters($stmt, $params);

        // Execute statement
        $stmt->execute();

        // Fetch rows and process them using the callback function
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result = $processRow($row);

            if ($result !== null) { // Check if the callback returned a value
                if ($result === false) {
                    $breakLoop = true; // Indicate to break the loop without returning a value
                    break; // Exit the while loop
                } else {
                    $returnValue = $result; // Capture the return value
                    break; // Exit the while loop
                }
            }
        }

    } catch (PDOException $e) {
        throw new Exception("Database error PROCESS_ROWS: " . $e->getMessage());
    } finally {
        // Close the cursor and clean up.
        if (isset($stmt) && $stmt instanceof PDOStatement) {
            $stmt->closeCursor();
            $stmt = null;
        }
    }

    if ($breakLoop) {
        return null;
    }
    return $returnValue;
}
/*
 function pdo_process_rows($pdo, $sql, $params = [], callable $processRow) {
    try {
        // Prepare statement
        $stmt = $pdo->prepare($sql);

        // Bind parameters, if any
        if (!empty($params)) bindParameters($stmt, $params);

        // Execute statement
        $stmt->execute();

        // Fetch rows and process them using the callback function
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($processRow($row) === false) break;
        }

    } catch (PDOException $e) {
        throw new Exception("Database error PROCESS_ROWS: " . $e->getMessage());
    } finally {
        // Close the cursor and clean up.
        if (isset($stmt) && $stmt instanceof PDOStatement) {
            $stmt->closeCursor();
            $stmt = null;
        }
    }
}
 */


/*
Example usage:

    try {
        $sql = "INSERT INTO some_table (column1, column2) VALUES (:column1, :column2)";
        $params = [':column1' => 'value1', ':column2' => ['value' => 1234, 'type' => PDO::PARAM_INT]];
        $insertId = pdo_execute($pdo, $sql, $params);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

 */
function pdo_execute($pdo, $sql, $params = []) {
    try {

        // Prepare the statement
        $stmt = $pdo->prepare($sql);

        // Bind parameters, if any
        if (!empty($params)) bindParameters($stmt, $params);

        // Execute the statement
        $stmt->execute();

        // Check if the query was an INSERT operation
        if (stripos($sql, 'INSERT') === 0) {
            // Return the ID of the last inserted row
            return $pdo->lastInsertId();
        }

    } catch (PDOException $e) {
        throw new Exception("Database error EXEC: " . $e->getMessage());
    } finally {
        // Ensure to close the cursor and set the statement to null when done
        if (isset($stmt) && $stmt instanceof PDOStatement) {
            $stmt->closeCursor();
            $stmt = null;
        }
    }

    // For non-INSERT operations, return null
    return null;
}


/*
Example usage:

    try {
        $sql = ""SELECT some_column FROM some_table WHERE some_column = :some_parameter";
        $params = [':some_parameter' => 'some_value'];
        $col = pdo_get_col($pdo, $sql, $params);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

 */
function pdo_get_col($pdo, $sql, $params = []) {
    try {

        // Prepare the statement
        $stmt = $pdo->prepare($sql);

        // Bind parameters, if any
        if (!empty($params)) bindParameters($stmt, $params);

        // Execute the statement
        $stmt->execute();

        // Fetch the column value
        $col = $stmt->fetchColumn();

        // Return the column value
        return $col !== false ? $col : null;

    } catch (PDOException $e) {
        throw new Exception("Database error GET_COL: " . $e->getMessage());
    } finally {
        // Ensure to close the cursor and set the statement to null when done
        if (isset($stmt) && $stmt instanceof PDOStatement) {
            $stmt->closeCursor();
            $stmt = null;
        }
    }
}

/*
Example Usage:

try {
    $sql = "SELECT * FROM some_table WHERE some_column = :value";
    $params = [':value' => 'some value'];
    $results = pdo_get_array($pdo, $sql, $params);

    // Process $results array as needed.
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
*/

function pdo_get_array($pdo, $sql, $params = []) {
    // try {
    // Prepare the statement
    $stmt = $pdo->prepare($sql);

    // Bind parameters, if any
    if (!empty($params)) bindParameters($stmt, $params);

    // Execute the statement
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //   } catch (PDOException $e) {
    //      throw new Exception("Database error: " . $e->getMessage());
    //   } finally {
    // Ensuring that the statement is closed and the connection is returned to the pool.
    //$stmt->closeCursor();
    //       $stmt = null;
    //  }

    return $results ?: []; // return empty array if $results is null
}


/*
Example usage:

    try {
        $sql = "SELECT * FROM some_table WHERE column1 = :column1";
        $params = [':column1' => 'value1'];

        $rowCount = pdo_row_count($pdo, $sql, $params);
        echo "Row count: $rowCount";

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

*/
function pdo_row_count($pdo, $sql, $params = []) {
    try {

        $stmt = $pdo->prepare($sql);

        // Bind parameters, if any
        if (!empty($params)) bindParameters($stmt, $params);

        $stmt->execute();

        // Return the number of rows
        return $stmt->rowCount();

    } catch (PDOException $e) {
        throw new Exception("Database error: " . $e->getMessage());
    } finally {
        // Set the statement to null when done
        $stmt->closeCursor();
        $stmt = null;
    }
}


/*
Example usage:

    try {
        $sql = "SELECT * FROM some_table WHERE column1 = :column1";
        $params = [':column1' => 'value1'];

        if (pdo_row_exists($pdo, $sql, $params)) {
            echo "Row exists!";
        } else {
            echo "Row does not exist!";
        }

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

 */
function pdo_row_exists($pdo, $sql, $params = []) {
    return pdo_row_count($pdo, $sql, $params) > 0;
}

function pdo_get_enum_list($pdo, $table, $field) {
    try {
        $sql = "SELECT `column_type` FROM information_schema.columns WHERE table_name = :table AND column_name = :field";
        $params = [':table' => $table, ':field' => $field];
        $row = pdo_get_row($pdo, $sql, $params);

        if ($row && isset($row['column_type'])) {
            $enumList = explode(",", str_replace("'", "", substr($row['column_type'], 5, (strlen($row['column_type'])-6))));
            return $enumList;
        } else {
            return [];
        }
    } catch (PDOException $e) {
        throw new Exception("Database error: " . $e->getMessage());
    }
}