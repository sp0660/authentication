<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    function clean($conn, $data) {
        return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
    }

    $army_number    = clean($conn, $_POST['army_number']);
    $rank           = clean($conn, $_POST['rank']);
    $name           = clean($conn, $_POST['name']);
    $unit           = clean($conn, $_POST['unit']);
    $dob            = $_POST['dob'];
    $enrol_date     = $_POST['enrolment_date'];
    $pan_no         = strtoupper(clean($conn, $_POST['pan_no']));
    $aadhar_no      = clean($conn, $_POST['aadhar_no']);
    $contact_no     = clean($conn, $_POST['contact_no']);
    $email          = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password       = $_POST['password'];
    $confirm_pass   = $_POST['confirm_password'];

    if (!preg_match("/^[A-Z]{5}[0-9]{4}[A-Z]$/", $pan_no)) {
        die("❌ Invalid PAN number format.");
    }

    if (!preg_match("/^\d{12}$/", $aadhar_no)) {
        die("❌ Aadhar number must be 12 digits.");
    }

    if (!preg_match("/^\d{10}$/", $contact_no)) {
        die("❌ Contact number must be 10 digits.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("❌ Invalid email format.");
    }

    if (strlen($password) < 6) {
        die("❌ Password must be at least 6 characters.");
    }

    if ($password !== $confirm_pass) {
        die("❌ Passwords do not match.");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn, "INSERT INTO users 
        (army_number, rank, name, unit, dob, enrolment_date, pan_no, aadhar_no, contact_no, email, password) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "sssssssssss", 
        $army_number, $rank, $name, $unit, $dob, $enrol_date,
        $pan_no, $aadhar_no, $contact_no, $email, $hashed_password
    );

    if (mysqli_stmt_execute($stmt)) {
        echo "<p style='color:green;'>✅ Registration successful!</p>";
        echo "<p><a href='login.php'>Click here to login</a></p>";
        exit; // stop execution after success message
    } else {
        echo "<p style='color:red;'>❌ Registration failed: " . mysqli_stmt_error($stmt) . "</p>";
    }

    mysqli_stmt_close($stmt);
}
?>
