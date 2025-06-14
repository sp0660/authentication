<?php
require 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['otp_user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = $_POST['otp'];
    $uid = $_SESSION['otp_user_id'];

    $result = mysqli_query($conn, "SELECT otp, otp_expiry FROM users WHERE id = $uid");
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        if ($entered_otp === $row['otp']) {
            if (strtotime($row['otp_expiry']) >= time()) {
                $_SESSION['user_id'] = $uid;
                unset($_SESSION['otp_user_id']);
                header("Location: dashboard.php");
                exit;
            } else {
                $message = "❌ OTP expired.";
            }
        } else {
            $message = "❌ Invalid OTP.";
        }
    } else {
        $message = "❌ User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OTP Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
        }
        form {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"] {
            padding: 10px;
            width: 100%;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .message {
            margin-top: 15px;
            color: red;
        }
    </style>
</head>
<body>

<form method="POST">
    <label>Enter OTP:</label>
    <input type="text" name="otp" pattern="\d{6}" required>
    <button type="submit">Verify OTP</button>

    <?php if (!empty($message)): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>
</form>

</body>
</html>
