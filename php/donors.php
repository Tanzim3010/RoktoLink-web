<?php
// Turn on error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Html/index.html");
    exit();
}

// Fetch all available donors
$sql = "SELECT * FROM donor_profiles WHERE availability = 'Yes' OR availability = 'yes' ORDER BY full_name ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rokto Link - Available Donors</title>
    <link rel="stylesheet" href="../Css/style.css">
    <style>
        .donors-feed {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            justify-content: center;
            max-width: 1200px;
            margin: 0 auto 50px;
            padding: 0 20px;
        }
        .contact-btn {
            display: block; width: 100%; margin-top: 15px; padding: 12px;
            background-color: #d62828; color: #ffffff; text-align: center;
            text-decoration: none; border-radius: 8px; font-weight: bold; transition: 0.3s;
        }
        .contact-btn:hover { background-color: #a4161a; }
    </style>
</head>
<body class="patient-page">

    <div class="dashboard">
        <div class="mode-switcher">
            <button type="button" id="logoutBtn" class="logout-btn">Logout</button>
            <a href="patient.php" class="switch-mode-btn">My Patient Profile</a>
        </div>
        <h1>Available Blood Donors 🩸</h1>
        <p>Browse the list of available donors below. If someone matches the blood group you need, contact them directly.</p>
    </div>

    <div class="donors-feed">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                
                <!-- INLINE STYLES ADDED HERE TO FORCE VISIBILITY -->
                <div class="profile-card" style="display: block !important; margin: 0; width: 100%; max-width: 340px; position: relative;">
                    
                    <div class="card-heading">
                        <span>DONOR</span>
                    </div>

                    <?php if (!empty($row['profile_image'])): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($row['profile_image']); ?>" alt="Donor" style="width: 120px; height: 120px; margin: 0 auto 15px; display: block; object-fit: cover; border: 4px solid #d62828; border-radius: 50%;">
                    <?php else: ?>
                        <div class="default-profile-image" style="display: flex;">👤</div>
                    <?php endif; ?>

                    <h2 style="color: #333; text-align: center; margin-bottom: 10px;">
                        <?php echo htmlspecialchars($row['full_name']); ?>
                    </h2>

                    <div class="blood-badge">
                        <?php echo htmlspecialchars($row['blood_group']); ?>
                    </div>

                    <div class="profile-information">
                        <p><strong>📍 Area:</strong> <?php echo htmlspecialchars($row['address']); ?></p>
                        <p>
                            <strong>🩸 Last Donated:</strong> 
                            <?php 
                                if (!empty($row['last_donation']) && $row['last_donation'] !== '0000-00-00') {
                                    echo date_format(date_create($row['last_donation']), "d M, Y");
                                } else {
                                    echo "Never donated yet";
                                }
                            ?>
                        </p>
                    </div>

                    <a href="tel:<?php echo htmlspecialchars($row['phone']); ?>" class="contact-btn">
                        📞 Call <?php echo htmlspecialchars($row['phone']); ?>
                    </a>

                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; width: 100%; margin-top: 50px;">
                <h2>No Donors Available</h2>
                <p>There are currently no active/available donors in the system.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        const logoutBtn = document.getElementById("logoutBtn");
        if (logoutBtn) {
            logoutBtn.onclick = function () {
                if (confirm("Are you sure you want to logout?")) window.location.href = "logout.php";
            };
        }
    </script>
</body>
</html>