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

// Fetch all patient requests, sorted by urgency
$sql = "SELECT * FROM patient_profiles ORDER BY 
        CASE urgency 
            WHEN 'Emergency' THEN 1 
            WHEN 'Urgent' THEN 2 
            WHEN 'Normal' THEN 3 
            ELSE 4 
        END, required_date ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rokto Link - Blood Requests</title>
    <link rel="stylesheet" href="../Css/style.css">
    <style>
        .requests-feed {
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
            background-color: #2563eb; color: #ffffff; text-align: center;
            text-decoration: none; border-radius: 8px; font-weight: bold; transition: 0.3s;
        }
        .contact-btn:hover { background-color: #1d4ed8; }
    </style>
</head>
<body>

    <div class="dashboard">
        <div class="mode-switcher">
            <button type="button" id="logoutBtn" class="logout-btn">Logout</button>
            <a href="dashboard.php" class="switch-mode-btn">My Donor Profile</a>
        </div>
        <h1>Active Blood Requests 🩸</h1>
        <p>Browse the patients below. If your blood group matches, please reach out to them directly.</p>
    </div>

    <div class="requests-feed">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                
                <!-- INLINE STYLES ADDED HERE TO FORCE VISIBILITY -->
                <div class="profile-card" style="display: block !important; margin: 0; width: 100%; max-width: 340px; position: relative;">
                    
                    <div class="card-heading" style="background-color: #2563eb;">
                        <span>URGENCY: <?php echo htmlspecialchars(strtoupper($row['urgency'])); ?></span>
                    </div>

                    <?php if (!empty($row['profile_image'])): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($row['profile_image']); ?>" alt="Patient" style="width: 120px; height: 120px; margin: 0 auto 15px; display: block; object-fit: cover; border: 4px solid #2563eb; border-radius: 50%;">
                    <?php else: ?>
                        <div class="default-profile-image" style="border-color: #2563eb; background-color: #dbeafe; display: flex;">👤</div>
                    <?php endif; ?>

                    <h2 style="color: #1e3a8a; text-align: center;">
                        <?php echo htmlspecialchars($row['full_name']); ?>
                    </h2>

                    <div class="blood-badge" style="background-color: #2563eb;">
                        <?php echo htmlspecialchars($row['blood_group']); ?>
                    </div>

                    <div class="profile-information" style="border-left: 4px solid #2563eb; background-color: #eff6ff; padding: 15px; border-radius: 8px;">
                        <p><strong>🏥 Hospital:</strong> <?php echo htmlspecialchars($row['hospital']); ?></p>
                        <p><strong>📍 Location:</strong> <?php echo htmlspecialchars($row['address']); ?></p>
                        <p><strong>🩸 Units Needed:</strong> <?php echo htmlspecialchars($row['blood_units']); ?></p>
                        <p>
                            <strong>📅 Required By:</strong> 
                            <?php 
                                if (!empty($row['required_date']) && $row['required_date'] !== '0000-00-00') {
                                    echo date_format(date_create($row['required_date']), "d M, Y"); 
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
                <h2>No Active Requests</h2>
                <p>There are currently no patients requesting blood.</p>
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