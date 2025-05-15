<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'PharmacyDatabase.php';
$db = new PharmacyDatabase();

$message = "";
$error = "";


$medications = $db->getAllMedications();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $patientUsername = $_POST['patient_username'];
    $medicationId = $_POST['medication_id'];
    $dosageInstructions = $_POST['dosage_instructions'];
    $quantity = $_POST['quantity'];

    if (!empty($patientUsername) && !empty($medicationId)) {
        $db->addPrescription($patientUsername, $medicationId, $dosageInstructions, $quantity);
        $message = "Prescription added successfully!";
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Prescription</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Add Prescription</h1>

    <?php if (!empty($message)): ?>
        <p style="color: green;"><?= htmlspecialchars($message) ?></p>
    <?php elseif (!empty($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Patient Username:</label><br>
        <input type="text" name="patient_username" required><br><br>

        <label>Medication:</label><br>
        <select name="medication_id" required>
            <option value="">-- Select Medication --</option>
            <?php foreach ($medications as $med): ?>
                <option value="<?= $med['medicationId'] ?>">
                    <?= htmlspecialchars($med['medicationName']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Dosage Instructions:</label><br>
        <textarea name="dosage_instructions" required></textarea><br><br>

        <label>Quantity:</label><br>
        <input type="number" name="quantity" required><br><br>

        <button type="submit">Save</button>
    </form>

    <br>
    <a href="home.php">Back to Home</a>
</body>
</html>
