<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Html/index.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$has_profile = false;
$profile_data = [];

// 1. Fetch profile if it exists
$stmt = $conn->prepare("SELECT * FROM donor_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $has_profile = true;
    $profile_data = $result->fetch_assoc();
}

// 2. Form processing (Handles BOTH Create and Update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $full_name = trim($_POST['fullName']);
    $blood_group = trim($_POST['bloodGroup']);
    $phone = trim($_POST['phone']);
    $dob = $_POST['dob'];
    $address = trim($_POST['address']);
    $last_donation = !empty($_POST['lastDonation']) ? $_POST['lastDonation'] : NULL;
    $availability = $_POST['availability'];

    // Default to existing image if updating, or NULL if new
    $image_path = $has_profile ? $profile_data['profile_image'] : NULL; 
    
    // --- PHOTO UPLOAD LOGIC ---
    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
        
        // FIX: Automatically create the uploads folder if it is missing!
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_tmp = $_FILES['profilePicture']['tmp_name'];
        $file_name = $_FILES['profilePicture']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = array("jpg", "jpeg", "png");
        
        if (in_array($file_ext, $allowed_ext)) {
            $new_file_name = "donor_" . $user_id . "_" . time() . "." . $file_ext;
            $destination = $upload_dir . $new_file_name;
            if (move_uploaded_file($file_tmp, $destination)) {
                $image_path = $new_file_name;
            }
        }
    }

    if ($has_profile) {
        // UPDATE EXISTING PROFILE
        $update_stmt = $conn->prepare("UPDATE donor_profiles SET full_name=?, profile_image=?, blood_group=?, phone=?, dob=?, address=?, last_donation=?, availability=? WHERE user_id=?");
        $update_stmt->bind_param("ssssssssi", $full_name, $image_path, $blood_group, $phone, $dob, $address, $last_donation, $availability, $user_id);
        $update_stmt->execute();
    } else {
        // INSERT NEW PROFILE
        $insert_stmt = $conn->prepare("INSERT INTO donor_profiles (user_id, full_name, profile_image, blood_group, phone, dob, address, last_donation, availability) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("issssssss", $user_id, $full_name, $image_path, $blood_group, $phone, $dob, $address, $last_donation, $availability);
        $insert_stmt->execute();
    }
    
    // Refresh page to show updated data
    header("Location: dashboard.php"); 
    exit();
}

// Determine what to show on page load
$show_form = !$has_profile ? 'block' : 'none';
$show_card = $has_profile ? 'block' : 'none';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rokto Link - Donor Dashboard</title>
    <link rel="stylesheet" href="../Css/style.css">
</head>
<body>

    <div class="dashboard">
        <div class="mode-switcher">
            <button type="button" id="logoutBtn" class="logout-btn">Logout</button>
            <a href="patient.php" class="switch-mode-btn" style="background-color: #666;">Switch to Patient Mode</a>
        </div>

        <?php if ($has_profile): ?>
            <h1>Your Donor Profile ❤️</h1>
            <p>Your profile is active. Patients can currently see your details.</p>
            <a href="request.php" style="display:inline-block; padding:13px 25px; color:#fff; background-color:#d62828; border-radius:8px; text-decoration:none; font-weight:bold;">View Blood Requests</a>
        <?php else: ?>
            <h1>Welcome to Rokto Link ❤️</h1>
            <p>Create your donor profile so people can find you when blood is needed.</p>
        <?php endif; ?>
    </div>

    <div class="profile-container" id="profileForm" style="display: flex;">

        <!-- FORM SECTION -->
        <div class="form-section" id="formSection" style="display: <?php echo $show_form; ?>;">
            <h1><?php echo $has_profile ? 'Edit Your Profile' : 'Create Your Profile'; ?></h1>
            <form id="createProfileForm" action="dashboard.php" method="POST" enctype="multipart/form-data">

                <label for="profilePicture">Profile Picture</label>
                <input type="file" id="profilePicture" name="profilePicture" accept="image/png, image/jpeg, image/jpg">

                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($profile_data['full_name'] ?? ''); ?>" required>

                <label for="bloodGroup">Blood Group</label>
                <select id="bloodGroup" name="bloodGroup" required>
                    <option value="">Select Blood Group</option>
                    <?php 
                        $bgs = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                        foreach($bgs as $bg) {
                            $selected = ($has_profile && $profile_data['blood_group'] == $bg) ? 'selected' : '';
                            echo "<option value='$bg' $selected>$bg</option>";
                        }
                    ?>
                </select>

                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($profile_data['phone'] ?? ''); ?>" maxlength="11" pattern="01[0-9]{9}" required>

                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($profile_data['dob'] ?? ''); ?>" required>

                <label for="address">Address</label>
                <textarea id="address" name="address" rows="4" required><?php echo htmlspecialchars($profile_data['address'] ?? ''); ?></textarea>

                <label for="lastDonation">Last Blood Donation Date</label>
                <input type="date" id="lastDonation" name="lastDonation" value="<?php echo htmlspecialchars($profile_data['last_donation'] ?? ''); ?>">

                <label for="availability">Available for Blood Donation?</label>
                <select id="availability" name="availability" required>
                    <option value="Yes" <?php echo ($has_profile && $profile_data['availability'] == 'Yes') ? 'selected' : ''; ?>>Yes</option>
                    <option value="No" <?php echo ($has_profile && $profile_data['availability'] == 'No') ? 'selected' : ''; ?>>No</option>
                </select>

                <button type="submit" class="submit-button"><?php echo $has_profile ? 'Save Changes' : 'Create Profile'; ?></button>
                
                <?php if($has_profile): ?>
                    <button type="button" id="cancelEditBtn" style="width: 100%; margin-top: 10px; padding: 13px; background-color: #666; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">Cancel Edit</button>
                <?php endif; ?>
            </form>
        </div>

        <!-- PROFILE CARD SECTION -->
        <?php if ($has_profile): ?>
        <div class="profile-card" id="cardSection" style="display: <?php echo $show_card; ?>;">
            <div class="card-heading"><span>DONOR PROFILE</span></div>
            
            <?php if (!empty($profile_data['profile_image'])): ?>
                <img src="../uploads/<?php echo htmlspecialchars($profile_data['profile_image']); ?>" style="width: 120px; height: 120px; margin: 0 auto 15px; display: block; object-fit: cover; border: 4px solid #d62828; border-radius: 50%;">
            <?php else: ?>
                <div class="default-profile-image" style="display: flex;">👤</div>
            <?php endif; ?>

            <h2><?php echo htmlspecialchars($profile_data['full_name']); ?></h2>
            <div class="blood-badge"><?php echo htmlspecialchars($profile_data['blood_group']); ?></div>

            <div class="profile-information">
                <p><strong>📞 Phone:</strong> <span><?php echo htmlspecialchars($profile_data['phone']); ?></span></p>
                <p><strong>🎂 Date of Birth:</strong> <span><?php echo date_format(date_create($profile_data['dob']), "d M, Y"); ?></span></p>
                <p><strong>📍 Address:</strong> <span><?php echo htmlspecialchars($profile_data['address']); ?></span></p>
                <p><strong>🩸 Last Donation:</strong> <span><?php echo (!empty($profile_data['last_donation']) && $profile_data['last_donation'] !== '0000-00-00') ? date_format(date_create($profile_data['last_donation']), "d M, Y") : "Never donated yet"; ?></span></p>
                <p><strong>✅ Available:</strong> <span style="font-weight: bold; color: <?php echo ($profile_data['availability'] == 'Yes') ? '#138a36' : '#d62828'; ?>;"><?php echo htmlspecialchars($profile_data['availability']); ?></span></p>
            </div>
            
            <button type="button" id="editProfileBtn" style="width: 100%; margin-top: 15px; padding: 11px; color: #d62828; background-color: #ffffff; border: 2px solid #d62828; border-radius: 7px; font-weight: bold; cursor: pointer;">Edit Profile</button>
        </div>
        <?php endif; ?>

    </div>

    <script>
        // Logout Script
        const logoutBtn = document.getElementById("logoutBtn");
        if (logoutBtn) logoutBtn.onclick = () => { if (confirm("Are you sure you want to logout?")) window.location.href = "logout.php"; };

        // Toggle Edit Form Script
        const editBtn = document.getElementById('editProfileBtn');
        const cancelBtn = document.getElementById('cancelEditBtn');
        const formSec = document.getElementById('formSection');
        const cardSec = document.getElementById('cardSection');

        if (editBtn) {
            editBtn.addEventListener('click', () => {
                cardSec.style.display = 'none';
                formSec.style.display = 'block';
            });
        }
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                formSec.style.display = 'none';
                cardSec.style.display = 'block';
            });
        }
    </script>
</body>
</html>