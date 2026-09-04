<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Html/index.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $full_name = trim($_POST['fullName']);
    $blood_group = trim($_POST['bloodGroup']);
    $phone = trim($_POST['phone']);
    $dob = $_POST['dob'];
    $address = trim($_POST['address']);
    $last_donation = !empty($_POST['lastDonation']) ? $_POST['lastDonation'] : NULL;
    $availability = $_POST['availability'];

    // Insert profile data into database
    $stmt = $conn->prepare("INSERT INTO donor_profiles (user_id, full_name, blood_group, phone, dob, address, last_donation, availability) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $user_id, $full_name, $blood_group, $phone, $dob, $address, $last_donation, $availability);

    if ($stmt->execute()) {
        header("Location: request.php");
        exit();
    } else {
        echo "Error saving profile: " . $stmt->error;
    }
}
?>