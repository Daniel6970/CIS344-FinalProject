<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'PharmacyDatabase.php';
$db = new PharmacyDatabase();

$inventory = $db->MedicationInventory();
?>


<?php
require_once 'PharmacyDatabase.php';
$db = new PharmacyDatabase();

$inventory = $db->MedicationInventory();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Medication Inventory</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Medication Inventory</h1>

    <?php if (count($inventory) > 0): ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Medication Name</th>
                <th>Dosage</th>
                <th>Manufacturer</th>
                <th>Quantity Available</th>
            </tr>
            <?php foreach ($inventory as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['medicationName']) ?></td>
                    <td><?= htmlspecialchars($item['dosage']) ?></td>
                    <td><?= htmlspecialchars($item['manufacturer']) ?></td>
                    <td><?= htmlspecialchars($item['quantityAvailable']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No inventory data available.</p>
    <?php endif; ?>

    <br>
    <a href="home.php">← Back to Home</a>
</body>
</html>
