<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'PharmacyDatabase.php';
$db = new PharmacyDatabase();

$totalUsers = $db->getUserCount();
$totalMedications = $db->getMedicationCount();
$totalPrescriptions = $db->getPrescriptionCount();
$topMedication = $db->getTopMedication();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pharmacy Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Welcome to the Pharmacy Management System</h1>

    <div>
        <p><strong>Total Users:</strong> <?= $totalUsers ?></p>
        <p><strong>Total Medications:</strong> <?= $totalMedications ?></p>
        <p><strong>Total Prescriptions:</strong> <?= $totalPrescriptions ?></p>
        <p><strong>Top Prescribed Medication:</strong> <?= $topMedication ? $topMedication['medicationName'] . ' (' . $topMedication['total'] . ')' : 'N/A' ?></p>
    </div>

    <hr>
    <h3>Navigation</h3>
    <ul>
        <li><a href="addMedication.php">Add Medication</a></li>
        <li><a href="addPrescription.php">Add Prescription</a></li>
        <li><a href="viewPrescriptions.php">View Prescriptions</a></li>
        <li><a href="viewInventory.php">View Inventory</a></li>
        <li><a href="processSale.php">Process Sale</a></li>
        <li><a href="register.php">Register User</a></li>
       
    </ul>
</body>
</html>
