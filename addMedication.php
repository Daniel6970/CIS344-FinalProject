<?php
session_start();
require_once 'PharmacyDatabase.php';

// Optional: Only allow pharmacists
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pharmacist') {
    header("Location: login.php");
    exit();
}

$db = new PharmacyDatabase();
$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['medication_name'];
    $dosage = $_POST['dosage'];
    $manufacturer = $_POST['manufacturer'];

    if (empty($name) || empty($dosage)) {
        $error = "Medication name and dosage are required.";
    } else {
        $message = $db->addMedication($name, $dosage, $manufacturer);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Medication</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .container {
            max-width: 500px;
            margin: 60px auto;
            padding: 25px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
        }
        input, button {
            width: 100%;
            margin-top: 10px;
            padding: 10px;
        }
        .success { color: green; text-align: center; }
        .error { color: red; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Medication</h2>

        <?php if ($message): ?>
            <p class="success"><?= htmlspecialchars($message) ?></p>
        <?php elseif ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Medication Name:</label>
            <input type="text" name="medication_name" required>

            <label>Dosage:</label>
            <input type="text" name="dosage" required>

            <label>Manufacturer:</label>
            <input type="text" name="manufacturer">

            <button type="submit">Add Medication</button>
        </form>

        <div style="text-align:center; margin-top: 15px;">
            <a href="home.php">← Back to Home</a>
        </div>
    </div>
</body>
</html>
