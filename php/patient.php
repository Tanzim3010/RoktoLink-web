<?php
// Turn on error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db_connect.php';

// Kick out unauthenticated users
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Html/index.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$has_profile = false;
$profile_data = [];

// 1. Check if patient profile already exists and fetch the data
$stmt = $conn->prepare("SELECT * FROM patient_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $has_profile = true;
    $profile_data = $result->fetch_assoc(); // Store all profile info in this array
}

// 2. Form processing (Only runs if they submit the form)
if ($_SERVER["REQUEST_METHOD"] == "POST" && !$has_profile) {
    
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

    $image_path = NULL; 
    
    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profilePicture']['tmp_name'];
        $file_name = $_FILES['profilePicture']['name'];
        
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = array("jpg", "jpeg", "png");
        
        if (in_array($file_ext, $allowed_ext)) {
            $new_file_name = "patient_" . $user_id . "_" . time() . "." . $file_ext;
            $destination = "../uploads/" . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $destination)) {
                $image_path = $new_file_name;
            }
        }
    }

    $insert_stmt = $conn->prepare("INSERT INTO patient_profiles (user_id, full_name, profile_image, blood_group, phone, email, dob, hospital, address, blood_units, required_date, urgency, medical_info) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $insert_stmt->bind_param("issssssssisss", $user_id, $full_name, $image_path, $blood_group, $phone, $email, $dob, $hospital, $address, $blood_units, $required_date, $urgency, $medical_info);

    if ($insert_stmt->execute()) {
        header("Location: donors.php"); 
        exit();
    } else {
        echo "Error saving patient profile: " . $insert_stmt->error;
    }
}
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

    <!-- PATIENT DASHBOARD -->
    <div class="dashboard">
        <div class="mode-switcher">
            <button type="button" id="logoutBtn" class="logout-btn">Logout</button>
            <a href="dashboard.php" class="switch-mode-btn">Switch to Donor Mode</a>
        </div>

        <?php if ($has_profile): ?>
            <!-- Header for users who HAVE a profile -->
            <h1>Your Patient Profile 💙</h1>
            <p>Your request for blood is active. Donors can currently see your profile.</p>
            <a href="donors.php" style="display:inline-block; padding:13px 25px; color:#fff; background-color:#2563eb; border-radius:8px; text-decoration:none; font-weight:bold;">
                Search Available Donors
            </a>
        <?php else: ?>
            <!-- Header for users who DO NOT have a profile -->
            <h1>Welcome to Rokto Link 💙</h1>
            <p>Create your patient profile so donors can find you when blood is needed.</p>
        <?php endif; ?>
    </div>

    <!-- MAIN CONTAINER (Forced to display via PHP style tag) -->
    <div class="profile-container" id="profileForm" style="display: flex;">

        <?php if (!$has_profile): ?>
        
        <!-- ============================================== -->
        <!-- VIEW 1: THE REGISTRATION FORM (Only shows if no profile) -->
        <!-- ============================================== -->
        <div class="form-section">
            <h1>Create Patient Profile</h1>
            <form id="createProfileForm" action="patient.php" method="POST" enctype="multipart/form-data">

                <label for="profilePicture">Patient Picture</label>
                <div class="image-preview">
                    <img id="previewImage" src="" alt="Patient Preview">
                    <p id="imageText">No image selected</p>
                </div>
                <input type="file" id="profilePicture" name="profilePicture" accept="image/png, image/jpeg, image/jpg">

                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="fullName" placeholder="Enter patient's full name" required>

                <label for="bloodGroup">Blood Group</label>
                <select id="bloodGroup" name="bloodGroup" required>
                    <option value="">Select Blood Group</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>

                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="01XXXXXXXXX" maxlength="11" pattern="01[0-9]{9}" required>

                <label for="profileEmail">Email</label>
                <input type="email" id="profileEmail" name="profileEmail" placeholder="Enter patient's email" required>

                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" required>

                <label for="hospital">Hospital Name</label>
                <input type="text" id="hospital" name="hospital" placeholder="Enter hospital name" required>

                <label for="address">Hospital Address</label>
                <textarea id="address" name="address" placeholder="Enter hospital address" rows="4" required></textarea>

                <label for="bloodUnits">Required Blood Units</label>
                <input type="number" id="bloodUnits" name="bloodUnits" placeholder="Enter number of blood units" min="1" required>

                <label for="requiredDate">Blood Required Date</label>
                <input type="date" id="requiredDate" name="requiredDate" required>

                <label for="urgency">Urgency Level</label>
                <select id="urgency" name="urgency" required>
                    <option value="">Select Urgency Level</option>
                    <option value="Normal">Normal</option>
                    <option value="Urgent">Urgent</option>
                    <option value="Emergency">Emergency</option>
                </select>

                <label for="medicalInfo">Medical Information</label>
                <textarea id="medicalInfo" name="medicalInfo" placeholder="Enter relevant medical information" rows="4"></textarea>

                <button type="submit" class="submit-button">Create Patient Profile</button>
            </form>
        </div>

        <?php else: ?>

        <!-- ============================================== -->
        <!-- VIEW 2: THE PROFILE CARD (Only shows if profile exists) -->
        <!-- ============================================== -->
        <div class="profile-card" id="profileCard" style="display: block;">
            
            <div class="card-heading">
                <span>PATIENT PROFILE</span>
            </div>

            <?php if (!empty($profile_data['profile_image'])): ?>
                <img src="../uploads/<?php echo htmlspecialchars($profile_data['profile_image']); ?>" alt="Patient Profile" style="width: 120px; height: 120px; margin: 0 auto 15px; display: block; object-fit: cover; border: 4px solid #2563eb; border-radius: 50%;">
            <?php else: ?>
                <div class="default-profile-image" id="defaultProfileImage">👤</div>
            <?php endif; ?>

            <h2 id="cardName"><?php echo htmlspecialchars($profile_data['full_name']); ?></h2>

            <div class="blood-badge" id="cardBlood">
                <?php echo htmlspecialchars($profile_data['blood_group']); ?>
            </div>

            <div class="profile-information">
                <p><strong>📞 Phone:</strong> <span><?php echo htmlspecialchars($profile_data['phone']); ?></span></p>
                <p><strong>✉️ Email:</strong> <span><?php echo htmlspecialchars($profile_data['email']); ?></span></p>
                <p>
                    <strong>🎂 Date of Birth:</strong> 
                    <span>
                        <?php 
                            $dob = date_create($profile_data['dob']);
                            echo date_format($dob, "d M, Y");
                        ?>
                    </span>
                </p>
                <p><strong>🏥 Hospital:</strong> <span><?php echo htmlspecialchars($profile_data['hospital']); ?></span></p>
                <p><strong>📍 Hospital Address:</strong> <span><?php echo htmlspecialchars($profile_data['address']); ?></span></p>
                <p><strong>🩸 Blood Units:</strong> <span><?php echo htmlspecialchars($profile_data['blood_units']); ?></span></p>
                <p>
                    <strong>📅 Required Date:</strong> 
                    <span>
                        <?php 
                            $req_date = date_create($profile_data['required_date']);
                            echo date_format($req_date, "d M, Y");
                        ?>
                    </span>
                </p>
                <p>
                    <strong>🚨 Urgency:</strong> 
                    <span style="font-weight: bold; color: <?php echo ($profile_data['urgency'] == 'Emergency') ? '#dc2626' : (($profile_data['urgency'] == 'Urgent') ? '#ea580c' : '#15803d'); ?>;">
                        <?php echo htmlspecialchars($profile_data['urgency']); ?>
                    </span>
                </p>
                <p><strong>📝 Medical Info:</strong> <span><?php echo htmlspecialchars($profile_data['medical_info']); ?></span></p>
            </div>
        </div>
        
        <?php endif; ?>

    </div>

    <!-- JAVASCRIPT -->
    <script src="../javascript/patient.js"></script>
</body>
</html>