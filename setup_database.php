<?php

class Database
{
    private $db;
    private $db_file;

    public function __construct($db_file)
    {
        $this->db_file = $db_file;
        $this->connect();
    }

    private function connect()
    {
        try {
            $this->db = new PDO('sqlite:' . $this->db_file);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function createTables()
    {
        $this->db->beginTransaction();
        try {
            // Create devices table
            $this->db->exec("CREATE TABLE IF NOT EXISTS devices (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                deviceuid TEXT NOT NULL UNIQUE
            )");

            // Create datalog table
            $this->db->exec("CREATE TABLE IF NOT EXISTS datalog (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                deviceId INTEGER,
                upload REAL,
                download REAL,
                latency REAL,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (deviceId) REFERENCES devices(id)
            )");

            $this->db->commit();
            echo "Tables created successfully.\n";
        } catch (PDOException $e) {
            $this->db->rollBack();
            die("Table creation failed: " . $e->getMessage());
        }
    }

    public function close()
    {
        $this->db = null;
    }
}

$db_file = __DIR__ . '/database/speedtest.db';
$database = new Database($db_file);
$database->createTables();
$database->close();

?>
