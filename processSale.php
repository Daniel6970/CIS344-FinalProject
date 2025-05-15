<?php
require_once 'PharmacyDatabase.php';

$db = new PharmacyDatabase();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $prescriptionId = $_POST['prescription_id'];
    $quantitySold = $_POST['quantity_sold'];
    $saleAmount = $_POST['sale_amount'];

    $message = $db->processSale($prescriptionId, $quantitySold, $saleAmount);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Process Sale</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Process Sale</h1>

    <?php if (!empty($message)) echo "<p>$message</p>"; ?>

    <form method="POST">
        <label>Prescription ID:</label>
        <input type="number" name="prescription_id" required><br>

        <label>Quantity Sold:</label>
        <input type="number" name="quantity_sold" required><br>

        <label>Sale Amount:</label>
        <input type="number" step="0.01" name="sale_amount" required><br>

        <button type="submit">Submit Sale</button>
    </form>

    <a href="home.php">Back to Home</a>
</body>
</html>
