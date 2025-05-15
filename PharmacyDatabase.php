<?php
class PharmacyDatabase {
    private $host = "localhost";
    private $port = "3306";
    private $database = "pharmacy_portal_db";
    private $user = "root";
    private $password = ""; 
    public $connection;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->connection = new mysqli($this->host, $this->user, $this->password, $this->database, $this->port);
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public function addPrescription($patientUserName, $medicationId, $dosageInstructions, $quantity) {
        $stmt = $this->connection->prepare(
            "SELECT userId FROM Users WHERE userName = ? AND userType = 'patient'"
        );
        $stmt->bind_param("s", $patientUserName);
        $stmt->execute();
        $stmt->bind_result($patientId);
        $stmt->fetch();
        $stmt->close();
        
        if ($patientId) {
            $stmt = $this->connection->prepare(
                "INSERT INTO Prescriptions (userId, medicationId, dosageInstructions, quantity) VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("iisi", $patientId, $medicationId, $dosageInstructions, $quantity);
            $stmt->execute();
            $stmt->close();
            echo "Prescription added successfully";
        } else {
            echo "Failed to add prescription: patient not found.";
        }
    }

    public function processSale($prescriptionId, $quantitySold, $saleAmount) {
        try {
            $stmt = $this->connection->prepare("SELECT medicationId FROM Prescriptions WHERE prescriptionId = ?");
            $stmt->bind_param("i", $prescriptionId);
            $stmt->execute();
            $stmt->bind_result($medicationId);
            $stmt->fetch();
            $stmt->close();

            if (!$medicationId) {
                throw new Exception("Prescription ID not found.");
            }

            $stmt = $this->connection->prepare(
                "UPDATE Inventory 
                 SET quantityAvailable = quantityAvailable - ?, 
                     lastUpdated = NOW() 
                 WHERE medicationId = ?"
            );
            $stmt->bind_param("ii", $quantitySold, $medicationId);
            $stmt->execute();
            $stmt->close();

            $stmt = $this->connection->prepare(
                "INSERT INTO Sales (prescriptionId, quantitySold, saleAmount) 
                 VALUES (?, ?, ?)"
            );
            $stmt->bind_param("iid", $prescriptionId, $quantitySold, $saleAmount);
            $stmt->execute();
            $stmt->close();

            return "Sale processed successfully.";
        } catch (Exception $e) {
            return "Error processing sale: " . $e->getMessage();
        }
    }

    public function getAllPrescriptions() {
        $result = $this->connection->query(
            "SELECT * FROM Prescriptions 
             JOIN Medications ON Prescriptions.medicationId = Medications.medicationId"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function MedicationInventory() {
        $query = "
            SELECT 
                m.medicationName,
                m.dosage,
                m.manufacturer,
                i.quantityAvailable
            FROM Medications m
            JOIN Inventory i ON m.medicationId = i.medicationId
        ";
        $result = $this->connection->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addUser($userName, $contactInfo, $userType) {
        $stmt = $this->connection->prepare(
            "INSERT INTO Users (userName, contactInfo, userType) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $userName, $contactInfo, $userType);
        $stmt->execute();
        $stmt->close();
        return "User added successfully.";
    }

    public function addMedication($name, $dosage, $manufacturer) {
        $stmt = $this->connection->prepare(
            "INSERT INTO Medications (medicationName, dosage, manufacturer) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $name, $dosage, $manufacturer);
        $stmt->execute();
        $stmt->close();
        return "Medication added successfully.";
    }

    public function getAllMedications() {
        $result = $this->connection->query("SELECT medicationId, medicationName FROM Medications");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserCount() {
        $result = $this->connection->query("SELECT COUNT(*) as total FROM Users");
        return $result->fetch_assoc()['total'];
    }

    public function getMedicationCount() {
        $result = $this->connection->query("SELECT COUNT(*) as total FROM Medications");
        return $result->fetch_assoc()['total'];
    }

    public function getPrescriptionCount() {
        $result = $this->connection->query("SELECT COUNT(*) as total FROM Prescriptions");
        return $result->fetch_assoc()['total'];
    }

    public function getTopMedication() {
        $query = "
            SELECT m.medicationName, COUNT(p.medicationId) as total
            FROM Prescriptions p
            JOIN Medications m ON p.medicationId = m.medicationId
            GROUP BY p.medicationId
            ORDER BY total DESC
            LIMIT 1
        ";
        $result = $this->connection->query($query);
        return $result->fetch_assoc();
    }
}
?>
