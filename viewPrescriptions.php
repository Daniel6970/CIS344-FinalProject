<?php
require_once 'PharmacyDatabase.php';
$db = new PharmacyDatabase();


if (isset($_GET['delete'])) {
    $prescriptionId = (int) $_GET['delete'];
    $stmt = $db->connection->prepare("DELETE FROM Prescriptions WHERE prescriptionId = ?");
    $stmt->bind_param("i", $prescriptionId);
    $stmt->execute();
    $stmt->close();
    header("Location: viewPrescriptions.php");
    exit();
}

if (isset($_POST['update'])) {
    $prescriptionId = (int) $_POST['prescription_id'];
    $dosageInstructions = $_POST['dosage_instructions'];
    $quantity = (int) $_POST['quantity'];

    $stmt = $db->connection->prepare("UPDATE Prescriptions SET dosageInstructions = ?, quantity = ? WHERE prescriptionId = ?");
    $stmt->bind_param("sii", $dosageInstructions, $quantity, $prescriptionId);
    $stmt->execute();
    $stmt->close();
    header("Location: viewPrescriptions.php");
    exit();
}

$prescriptions = $db->connection->query(
    "SELECT p.prescriptionId, u.userName, m.medicationName, m.dosage, p.dosageInstructions, p.quantity
     FROM Prescriptions p
     JOIN Users u ON p.userId = u.userId
     JOIN Medications m ON p.medicationId = m.medicationId"
)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Prescriptions</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>All Prescriptions</h1>

    <?php if (count($prescriptions) > 0): ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Prescription ID</th>
                <th>Patient Username</th>
                <th>Medication Name</th>
                <th>Dosage</th>
                <th>Dosage Instructions</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($prescriptions as $row): ?>
                <tr>
                    <form method="POST" action="">
                        <td><?= htmlspecialchars($row['prescriptionId']) ?></td>
                        <td><?= htmlspecialchars($row['userName']) ?></td>
                        <td><?= htmlspecialchars($row['medicationName']) ?></td>
                        <td><?= htmlspecialchars($row['dosage']) ?></td>
                        <td>
                            <input type="text" name="dosage_instructions" value="<?= htmlspecialchars($row['dosageInstructions']) ?>" required>
                        </td>
                        <td>
                            <input type="number" name="quantity" value="<?= htmlspecialchars($row['quantity']) ?>" required>
                        </td>
                        <td>
                            <input type="hidden" name="prescription_id" value="<?= $row['prescriptionId'] ?>">
                            <button type="submit" name="update">Update</button>
                            <a href="viewPrescriptions.php?delete=<?= $row['prescriptionId'] ?>" onclick="return confirm('Are you sure you want to delete this prescription?');">Delete</a>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No prescriptions found.</p>
    <?php endif; ?>

    <br>
    <a href="home.php">← Back to Home</a>
</body>
</html>
