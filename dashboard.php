<?php
require 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT name, army_number, rank FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>

   <style>
    /* Full screen, flexbox centering */
    body {
      margin: 0;
      height: 100vh; /* 100% viewport height */
      display: flex;
      justify-content: center; /* center horizontally */
      align-items: center;    /* center vertically */
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      color: #333;
      flex-direction: column; /* stack elements vertically */
      text-align: center;
    }

    h2 {
      margin-bottom: 10px;
      font-weight: normal;
    }

    p {
      margin: 4px 0;
      font-size: 16px;
    }

    a {
      margin-top: 20px;
      text-decoration: none;
      color: #fff;
      background-color: #007bff;
      padding: 10px 15px;
      border-radius: 4px;
      display: inline-block;
      transition: background-color 0.3s ease;
    }

    a:hover {
      background-color: #0056b3;
    }
  </style>
  
</head>
<body>
  <h2>Welcome, <?= htmlspecialchars($user['name']) ?>!</h2>
  <p>Army Number: <?= htmlspecialchars($user['army_number']) ?></p>
  <p>Rank: <?= htmlspecialchars($user['rank']) ?></p>
  <a href="logout.php">Logout</a>
</body>
</html>
