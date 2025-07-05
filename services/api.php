<?php
ob_clean(); // Clean any existing output buffers
ob_end_clean(); // End any active output buffers

require_once __DIR__ . "/../database/database.php";

class API
{

    public $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getDeviceId($device_unique_id)
    {
        $stmt = $this->db->conn->prepare("SELECT id FROM devices WHERE device_unique_id = :device_unique_id");
        $stmt->bindValue(':device_unique_id', $device_unique_id, SQLITE3_TEXT);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row ? $row['id'] : null;
    }

    public function addDevice($device_unique_id)
    {
        $stmt = $this->db->conn->prepare("INSERT INTO devices (device_unique_id) VALUES (:device_unique_id)");
        $stmt->bindValue(':device_unique_id', $device_unique_id, SQLITE3_TEXT);
        $result = $stmt->execute();
        if ($result === false) {
            error_log("SQL Error in addDevice: " . $this->db->conn->lastErrorMsg());
        }
        return $result ? $this->db->conn->lastInsertRowID() : false;
    }

    public function logSpeedTestResult($device_unique_id, $upload, $download, $latency)
    {
        $device_id = $this->getDeviceId($device_unique_id);

        if (!$device_id) {
            $device_id = $this->addDevice($device_unique_id);
            if (!$device_id) {
                return json_encode([
                    'success' => false,
                    'message' => 'Error creating new device: ' . $this->db->conn->lastErrorMsg(),
                ]);
            }
        }

        $stmt = $this->db->conn->prepare("INSERT INTO devicelog (device_id, upload, download, latency, timestamp) VALUES (:device_id, :upload, :download, :latency, :timestamp)");
        
        $stmt->bindValue(':device_id', $device_id, SQLITE3_INTEGER);
        $stmt->bindValue(':upload', $upload, SQLITE3_FLOAT);
        $stmt->bindValue(':download', $download, SQLITE3_FLOAT);
        $stmt->bindValue(':latency', $latency, SQLITE3_FLOAT);
        $stmt->bindValue(':timestamp', date('Y-m-d H:i:s'), SQLITE3_TEXT);
        
        $result = $stmt->execute();
        
        if ($result !== false) {
            return json_encode([
                'success' => true,
                'message' => 'Speed test result logged successfully for device ' . $device_unique_id,
                'device_id' => $device_id
            ]);
        } else {
            return json_encode([
                'success' => false,
                'message' => 'Error logging speed test result: ' . $this->db->conn->lastErrorMsg(),
            ]);
        }
    }

    public function getDevices()
    {
        $result = $this->db->conn->query("SELECT id, device_unique_id, created_at FROM devices ORDER BY created_at DESC");
    
        $data = array();
        $count = 0;
    
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
            $count++;
        }
        return [
            'count' => $count,
            'devices' => $data
        ];
    }
    

    public function getDeviceSpeedTestResults($device_id, $limit = 50)
    {
        $stmt = $this->db->conn->prepare("SELECT id, upload, download, latency, timestamp FROM devicelog WHERE device_id = :device_id ORDER BY timestamp DESC LIMIT :limit");
        $stmt->bindValue(':device_id', $device_id, SQLITE3_INTEGER);
        $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
        
        $result = $stmt->execute();
        
        $data = array();
        if ($result) {
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getAllSpeedTestResults()
    {
        $stmt = $this->db->conn->prepare("SELECT dl.id, d.device_unique_id, dl.upload, dl.download, dl.latency, dl.timestamp FROM devicelog dl JOIN devices d ON dl.device_id = d.id ORDER BY dl.timestamp ASC");
        
        $result = $stmt->execute();
        
        $data = array();
        if ($result) {
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function deleteSpeedTestRecord($id)
    {
        $stmt = $this->db->conn->prepare("DELETE FROM devicelog WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        if ($result !== false) {
            return json_encode([
                'success' => true,
                'message' => 'Record deleted successfully',
            ]);
        } else {
            return json_encode([
                'success' => false,
                'message' => 'Error: ' . $this->db->conn->lastErrorMsg(),
            ]);
        }
    }

    public function deleteDevice($device_id)
    {
        // Start a transaction
        $this->db->conn->exec('BEGIN;');

        try {
            // Delete associated devicelog records first
            $stmtDevicelog = $this->db->conn->prepare("DELETE FROM devicelog WHERE device_id = :device_id");
            $stmtDevicelog->bindValue(':device_id', $device_id, SQLITE3_INTEGER);
            $resultDevicelog = $stmtDevicelog->execute();

            if ($resultDevicelog === false) {
                throw new Exception($this->db->conn->lastErrorMsg());
            }

            // Then delete the device record
            $stmtDevice = $this->db->conn->prepare("DELETE FROM devices WHERE id = :device_id");
            $stmtDevice->bindValue(':device_id', $device_id, SQLITE3_INTEGER);
            $resultDevice = $stmtDevice->execute();

            if ($resultDevice === false) {
                throw new Exception($this->db->conn->lastErrorMsg());
            }

            // Commit transaction
            $this->db->conn->exec('COMMIT;');

            return json_encode([
                'success' => true,
                'message' => 'Device and all associated records deleted successfully',
            ]);
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->conn->exec('ROLLBACK;');
            return json_encode([
                'success' => false,
                'message' => 'Error deleting device: ' . $e->getMessage(),
            ]);
        }
    }

    // Handle POST requests for actions like deleting records
    public function handlePostRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action'])) {
                $action = $_POST['action'];
                switch ($action) {
                    case 'deleteRecord':
                    if (isset($_POST['id'])) {
                        $id = $_POST['id']; // Define $id here
                        echo $this->deleteSpeedTestRecord($id);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Record ID not provided.']);
                    }
                    break;
                case 'addDevice':
                    if (isset($_POST['device_unique_id'])) {
                        $device_unique_id = $_POST['device_unique_id'];
                        $result = $this->addDevice($device_unique_id);
                        if ($result !== false) {
                            error_log("Device added successfully: " . $device_unique_id . " with ID: " . $result);
                            header('Content-Type: application/json');
                            echo json_encode([
                                'success' => true,
                                'message' => 'Device "' . htmlspecialchars($device_unique_id) . '" added successfully!',
                                'device_id' => $result
                            ]);
                            die(); // Terminate script after sending JSON
                        } else {
                            $errorMessage = $this->db->conn->lastErrorMsg();
                            error_log("Error adding device: " . $errorMessage);
                            header('Content-Type: application/json');
                            echo json_encode([
                                'success' => false,
                                'message' => 'Error adding device: ' . $errorMessage,
                            ]);
                            die(); // Terminate script after sending JSON
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Device Unique ID not provided.']);
                    }
                    break;
                case 'deleteDevice':
                    if (isset($_POST['device_id'])) {
                        $device_id = $_POST['device_id'];
                        echo $this->deleteDevice($device_id);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Device ID not provided.']);
                    }
                    break;
                default:
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
                    die(); // Terminate script after sending JSON
                    break;
                }
            }
        }
    }
    
    public function getTotalRecords()
    {
        $result = $this->db->conn->querySingle("SELECT COUNT(*) as count FROM devicelog");
        return $result ? intval($result) : 0;
    }

    public function deleteAllRecords()
    {
        $resultDevicelog = $this->db->conn->exec("DELETE FROM devicelog");
        $resultDevices = $this->db->conn->exec("DELETE FROM devices");
        
        $this->db->conn->exec("DELETE FROM sqlite_sequence WHERE name='devicelog'");
        $this->db->conn->exec("DELETE FROM sqlite_sequence WHERE name='devices'");

        return $resultDevicelog !== false && $resultDevices !== false;
    }
}

// Only handle POST requests if the script is accessed directly
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $api = new API();
    $api->handlePostRequest();
}
