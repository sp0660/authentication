<?php
require 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {
                // Generate OTP and save it
                $otp = rand(100000, 999999);
                $otp_expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

                $update = mysqli_prepare($conn, "UPDATE users SET otp = ?, otp_expiry = ? WHERE id = ?");
                mysqli_stmt_bind_param($update, "ssi", $otp, $otp_expiry, $user['id']);
                mysqli_stmt_execute($update);

                $_SESSION['otp_user_id'] = $user['id'];

                $message = "✅ Password OK. Your OTP is: <strong>$otp</strong><br>";
                $message .= "<a href='otp_verify.php'>Click here to verify OTP</a>";
            } else {
                $message = "❌ Incorrect password.";
            }
        } else {
            $message = "❌ User not found.";
        }
    } else {
        $message = "❌ Failed to prepare statement.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <h2>Login</h2>
    <?php if (!empty($message)): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>
    <form method="POST" action="login.php">
        <label>Email:</label>
        <input type="email" name="email" required><br><br>

        <label>Password:</label>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
