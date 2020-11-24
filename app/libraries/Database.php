<?php

/*
 * PDO Database Class
 * Connect to the MySQL database
 * Creates prepared statements
 * Binds Values
 * Returns rows and results
 */

class Database
{
    private $host = DB_HOST;
    private $user = DB_USER;
    private $password = DB_PASSWORD;
    private $dbName = DB_NAME;

    private $dbHandler;
    private $statement;
    private $error;

    public function __construct()
    {
        // Setup DSN connection string
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbName;
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        // Create a PDO instance
        try {
            $this->dbHandler = new PDO($dsn, $this->user, $this->password, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            echo $this->error;
        }
    }

    // Create base query
    public function query($sql)
    {
        $this->statement = $this->dbHandler->prepare($sql);
    }

    // Bind values
    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
                    break;
            }
        }

        // Bind the values
        $this->statement->bindValue($param, $value, $type);
    }

    // Get result set as array of objects
    public function getAsResultSet()
    {
        $this->execute();
        return $this->statement->fetchAll(PDO::FETCH_OBJ);
    }

    // Execute the prepared statement
    public function execute()
    {
        return $this->statement->execute();
    }


    // Get a single record as an object
    public function getAsSingleRecord()
    {
        $this->execute();
        return $this->statement->fetch(PDO::FETCH_OBJ);
    }

    // Get the database row count
    public function getRowCount()
    {
        return $this->statement->rowCount();
    }

}