<?php
class PharmacyDatabase {
    private $host = "localhost";
    private $port = "3306";
    private $database = "pharmacy_portal_db";
    private $user = "root";
    private $password = "YourPassword";
    private $connection;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->connection = new mysqli($this->host, $this->user, $this->password, $this->database, $this->port);
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
        echo "Successfully connected to the database";
    }

    public function addPrescription($patientUserName, $medicationId, $dosageInstructions, $quantity)  {
        $stmt = $this->connection->prepare(
            "SELECT userId FROM Users WHERE userName = ? AND userType = 'patient'"
        );
        $stmt->bind_param("s", $patientUserName);
        $stmt->execute();
        $stmt->bind_result($patientId);
        $stmt->fetch();
        $stmt->close();
        
        if ($patientId){
            $stmt = $this->connection->prepare(
                "INSERT INTO prescriptions (userId, medicationId, dosageInstructions, quantity) VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("iisi", $patientId, $medicationId, $dosageInstructions, $quantity);
            $stmt->execute();
            $stmt->close();
            echo "Prescription added successfully";
        }else{
            echo "failed to add prescription";
        }
    }

    public function getAllPrescriptions() {
        $result = $this->connection->query("SELECT * FROM  prescriptions join medications on prescriptions.medicationId= medications.medicationId");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function MedicationInventory() {
        $result = $this->connection->query("SELECT * FROM MedicationInventoryView");
        
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC); 
        } else {
            return []; 
        }
    }

    public function addUser($userName, $contactInfo, $userType) {
        
        $stmt = $this->connection->prepare("SELECT userId FROM Users WHERE userName = ?");
        $stmt->bind_param("s", $userName);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
          
            echo "User already exists!";
        } else {
          
            $stmt = $this->connection->prepare("INSERT INTO Users (userName, contactInfo, userType) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $userName, $contactInfo, $userType);
            $stmt->execute();
            $stmt->close();
            echo "User added successfully!";
        }

        $stmt->close();
    }

   
    public function getUserDetails($userId) {
        $stmt = $this->connection->prepare("SELECT * FROM Users WHERE userId = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $userDetails = $result->fetch_assoc();
        $stmt->close();
        return $userDetails;
    }

}
?>
