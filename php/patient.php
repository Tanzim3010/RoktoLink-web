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
$stmt = $conn->prepare("SELECT * FROM patient_profiles WHERE user_id = ?");
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
    $email = trim($_POST['profileEmail']);
    $dob = $_POST['dob'];
    $hospital = trim($_POST['hospital']);
    $address = trim($_POST['address']);
    $blood_units = (int)$_POST['bloodUnits'];
    $required_date = $_POST['requiredDate'];
    $urgency = $_POST['urgency'];
    $medical_info = trim($_POST['medicalInfo']);

    // Default to existing image if updating
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
            $new_file_name = "patient_" . $user_id . "_" . time() . "." . $file_ext;
            $destination = $upload_dir . $new_file_name;
            if (move_uploaded_file($file_tmp, $destination)) {
                $image_path = $new_file_name;
            }
        }
    }

    if ($has_profile) {
        // UPDATE EXISTING PROFILE
        $update_stmt = $conn->prepare("UPDATE patient_profiles SET full_name=?, profile_image=?, blood_group=?, phone=?, email=?, dob=?, hospital=?, address=?, blood_units=?, required_date=?, urgency=?, medical_info=? WHERE user_id=?");
        $update_stmt->bind_param("ssssssssisssi", $full_name, $image_path, $blood_group, $phone, $email, $dob, $hospital, $address, $blood_units, $required_date, $urgency, $medical_info, $user_id);
        $update_stmt->execute();
    } else {
        // INSERT NEW PROFILE
        $insert_stmt = $conn->prepare("INSERT INTO patient_profiles (user_id, full_name, profile_image, blood_group, phone, email, dob, hospital, address, blood_units, required_date, urgency, medical_info) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("issssssssisss", $user_id, $full_name, $image_path, $blood_group, $phone, $email, $dob, $hospital, $address, $blood_units, $required_date, $urgency, $medical_info);
        $insert_stmt->execute();
    }
    
    header("Location: patient.php"); 
    exit();
}

$show_form = !$has_profile ? 'block' : 'none';
$show_card = $has_profile ? 'block' : 'none';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rokto Link - Patient Dashboard</title>
    <link rel="stylesheet" href="../Css/style.css">
</head>
<body class="patient-page">

    <div class="dashboard">
        <div class="mode-switcher">
            <button type="button" id="logoutBtn" class="logout-btn">Logout</button>
            <a href="dashboard.php" class="switch-mode-btn">Switch to Donor Mode</a>
        </div>

        <?php if ($has_profile): ?>
            <h1>Your Patient Profile 💙</h1>
            <p>Your request for blood is active. Donors can currently see your profile.</p>
            <a href="donors.php" style="display:inline-block; padding:13px 25px; color:#fff; background-color:#2563eb; border-radius:8px; text-decoration:none; font-weight:bold;">Search Available Donors</a>
        <?php else: ?>
            <h1>Welcome to Rokto Link 💙</h1>
            <p>Create your patient profile so donors can find you when blood is needed.</p>
        <?php endif; ?>
    </div>

    <div class="profile-container" id="profileForm" style="display: flex;">

        <!-- FORM SECTION -->
        <div class="form-section" id="formSection" style="display: <?php echo $show_form; ?>;">
            <h1><?php echo $has_profile ? 'Edit Patient Profile' : 'Create Patient Profile'; ?></h1>
            <form id="createProfileForm" action="patient.php" method="POST" enctype="multipart/form-data">

                <label for="profilePicture">Patient Picture</label>
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

                <label for="profileEmail">Email</label>
                <input type="email" id="profileEmail" name="profileEmail" value="<?php echo htmlspecialchars($profile_data['email'] ?? ''); ?>" required>

                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($profile_data['dob'] ?? ''); ?>" required>

                <label for="hospital">Hospital Name</label>
                <input type="text" id="hospital" name="hospital" value="<?php echo htmlspecialchars($profile_data['hospital'] ?? ''); ?>" required>

                <label for="address">Hospital Address</label>
                <textarea id="address" name="address" rows="4" required><?php echo htmlspecialchars($profile_data['address'] ?? ''); ?></textarea>

                <label for="bloodUnits">Required Blood Units</label>
                <input type="number" id="bloodUnits" name="bloodUnits" min="1" value="<?php echo htmlspecialchars($profile_data['blood_units'] ?? ''); ?>" required>

                <label for="requiredDate">Blood Required Date</label>
                <input type="date" id="requiredDate" name="requiredDate" value="<?php echo htmlspecialchars($profile_data['required_date'] ?? ''); ?>" required>

                <label for="urgency">Urgency Level</label>
                <select id="urgency" name="urgency" required>
                    <option value="Normal" <?php echo ($has_profile && $profile_data['urgency'] == 'Normal') ? 'selected' : ''; ?>>Normal</option>
                    <option value="Urgent" <?php echo ($has_profile && $profile_data['urgency'] == 'Urgent') ? 'selected' : ''; ?>>Urgent</option>
                    <option value="Emergency" <?php echo ($has_profile && $profile_data['urgency'] == 'Emergency') ? 'selected' : ''; ?>>Emergency</option>
                </select>

                <label for="medicalInfo">Medical Information</label>
                <textarea id="medicalInfo" name="medicalInfo" rows="4"><?php echo htmlspecialchars($profile_data['medical_info'] ?? ''); ?></textarea>

                <button type="submit" class="submit-button"><?php echo $has_profile ? 'Save Changes' : 'Create Patient Profile'; ?></button>
                
                <?php if($has_profile): ?>
                    <button type="button" id="cancelEditBtn" style="width: 100%; margin-top: 10px; padding: 13px; background-color: #64748b; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">Cancel Edit</button>
                <?php endif; ?>
            </form>
        </div>

        <!-- PROFILE CARD SECTION -->
        <?php if ($has_profile): ?>
        <div class="profile-card" id="cardSection" style="display: <?php echo $show_card; ?>;">
            <div class="card-heading"><span>PATIENT PROFILE</span></div>

            <?php if (!empty($profile_data['profile_image'])): ?>
                <img src="../uploads/<?php echo htmlspecialchars($profile_data['profile_image']); ?>" style="width: 120px; height: 120px; margin: 0 auto 15px; display: block; object-fit: cover; border: 4px solid #2563eb; border-radius: 50%;">
            <?php else: ?>
                <div class="default-profile-image" style="display: flex;">👤</div>
            <?php endif; ?>

            <h2><?php echo htmlspecialchars($profile_data['full_name']); ?></h2>
            <div class="blood-badge"><?php echo htmlspecialchars($profile_data['blood_group']); ?></div>

            <div class="profile-information">
                <p><strong>📞 Phone:</strong> <span><?php echo htmlspecialchars($profile_data['phone']); ?></span></p>
                <p><strong>✉️ Email:</strong> <span><?php echo htmlspecialchars($profile_data['email']); ?></span></p>
                <p><strong>🎂 Date of Birth:</strong> <span><?php echo date_format(date_create($profile_data['dob']), "d M, Y"); ?></span></p>
                <p><strong>🏥 Hospital:</strong> <span><?php echo htmlspecialchars($profile_data['hospital']); ?></span></p>
                <p><strong>📍 Hospital Address:</strong> <span><?php echo htmlspecialchars($profile_data['address']); ?></span></p>
                <p><strong>🩸 Blood Units:</strong> <span><?php echo htmlspecialchars($profile_data['blood_units']); ?></span></p>
                <p><strong>📅 Required Date:</strong> <span><?php echo date_format(date_create($profile_data['required_date']), "d M, Y"); ?></span></p>
                <p><strong>🚨 Urgency:</strong> <span style="font-weight: bold; color: <?php echo ($profile_data['urgency'] == 'Emergency') ? '#dc2626' : (($profile_data['urgency'] == 'Urgent') ? '#ea580c' : '#15803d'); ?>;"><?php echo htmlspecialchars($profile_data['urgency']); ?></span></p>
                <p><strong>📝 Medical Info:</strong> <span><?php echo htmlspecialchars($profile_data['medical_info']); ?></span></p>
            </div>

            <button type="button" id="editProfileBtn" style="width: 100%; margin-top: 15px; padding: 11px; color: #2563eb; background-color: #ffffff; border: 2px solid #2563eb; border-radius: 7px; font-weight: bold; cursor: pointer;">Edit Profile</button>
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