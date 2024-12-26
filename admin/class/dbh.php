<?php

class Dbh {
    private $host = "localhost";
    private $user = "root";
    private $password = "";  // Add your password if required
    private $dbname = "dashboard_content_db";
    private $port = 3308;  // Specifying the port explicitly

    protected function connect() {
        // Create a new mysqli connection
        $connection = new mysqli($this->host, $this->user, $this->password, $this->dbname, $this->port);

        // Check for connection errors
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        return $connection;
    }
}
