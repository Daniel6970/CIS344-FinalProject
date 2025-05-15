<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'PharmacyDatabase.php';

$db = new PharmacyDatabase();
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['user_name'];
    $password = $_POST['password'];

    $stmt = $db->connection->prepare("SELECT * FROM Users WHERE userName = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && !empty($user['password']) && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['userId'];
        $_SESSION['username'] = $user['userName'];
        $_SESSION['role'] = $user['userType'];

        if ($user['userType'] === 'pharmacist') {
            header("Location: home.php");
        } else {
            header("Location: viewPrescriptions.php");
        }
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Pharmacy Portal</title>
    <link rel="stylesheet" href="styles.css"> 
    <style>
        body {
            background-color: #f4f4f9;
            font-family: Arial, sans-serif;
        }
        .login-container {
            max-width: 420px;
            margin: 80px auto;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Pharmacy Login</h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="user_name">Username:</label>
            <input type="text" name="user_name" id="user_name" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Login</button>
        </form>

        <div style="text-align:center;">
            <a href="home.php">← Back to Home</a>
        </div>
    </div>
</body>
</html>
