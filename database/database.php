<?php

class Database
{
    public $database; // SQLite database file
    public $conn;

    public function __construct()
    {
        $this->database = __DIR__ . "/speedtest.db"; // Set the database path relative to the database.php file
        $this->databaseConnection();
    }

    public function databaseConnection()
    {
        try {
            // Create SQLite database connection
            $this->conn = new SQLite3($this->database);
            
            // Enable foreign keys and set busy timeout
            $this->conn->enableExceptions(true);
            $this->conn->busyTimeout(30000); // 30 second timeout
            
            // Create tables if they don't exist
            $this->createTablesIfNotExists();
            
        } catch (Exception $e) {
            print_r("Error Connecting to the Database: " . $e->getMessage());
        }
    }

    private function createTablesIfNotExists()
    {
        // Create devices table
        $sqlDevices = "CREATE TABLE IF NOT EXISTS devices (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            device_unique_id TEXT NOT NULL UNIQUE,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        )";
        $this->conn->exec($sqlDevices);

        // Create devicelog table
        $sqlDeviceLog = "CREATE TABLE IF NOT EXISTS devicelog (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            device_id INTEGER NOT NULL,
            upload REAL,
            download REAL,
            latency REAL,
            timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE CASCADE
        )";
        $this->conn->exec($sqlDeviceLog);
        
        // Create indexes for better performance
        $this->conn->exec("CREATE INDEX IF NOT EXISTS idx_devicelog_timestamp ON devicelog(timestamp)");
        $this->conn->exec("CREATE INDEX IF NOT EXISTS idx_devicelog_device_id ON devicelog(device_id)");

        // Optional: Drop old datalog table if it exists and is no longer needed
        // $this->conn->exec("DROP TABLE IF EXISTS datalog");
    }

    public function closeConnection()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
