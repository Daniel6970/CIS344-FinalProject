<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'PharmacyDatabase.php';

$db = new PharmacyDatabase();
$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['user_name'];
    $contactInfo = $_POST['contact_info'];
    $userType = $_POST['user_type'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check for existing username
        $check = $db->connection->prepare("SELECT userId FROM Users WHERE userName = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Username already exists.";
        } else {
            $stmt = $db->connection->prepare("INSERT INTO Users (userName, contactInfo, userType, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $contactInfo, $userType, $hashedPassword);
            $stmt->execute();
            $stmt->close();
            $message = "User registered successfully.";
        }
        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Pharmacy Portal</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #f4f4f9;
            font-family: Arial, sans-serif;
        }
        .form-container {
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
        label, input, select {
            width: 100%;
            margin-top: 10px;
            margin-bottom: 15px;
            padding: 10px;
        }
        .success { color: green; text-align: center; }
        .error { color: red; text-align: center; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>User Registration</h2>

        <?php if ($message): ?>
            <p class="success"><?= htmlspecialchars($message) ?></p>
        <?php elseif ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="user_name">Username:</label>
            <input type="text" name="user_name" id="user_name" required>

            <label for="contact_info">Contact Info (Email or Phone):</label>
            <input type="text" name="contact_info" id="contact_info" required>

            <label for="user_type">User Type:</label>
            <select name="user_type" id="user_type" required>
                <option value="pharmacist">Pharmacist</option>
                <option value="patient">Patient</option>
            </select>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <button type="submit">Register</button>
        </form>

        <div style="text-align:center;">
            <a href="login.php">← Back to Login</a>
        </div>
    </div>
</body>
</html>