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

// 1. Check if donor profile already exists and fetch the data
$stmt = $conn->prepare("SELECT * FROM donor_profiles WHERE user_id = ?");
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
    $dob = $_POST['dob'];
    $address = trim($_POST['address']);
    $last_donation = !empty($_POST['lastDonation']) ? $_POST['lastDonation'] : NULL;
    $availability = $_POST['availability'];

    $image_path = NULL; 
    
    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profilePicture']['tmp_name'];
        $file_name = $_FILES['profilePicture']['name'];
        
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = array("jpg", "jpeg", "png");
        
        if (in_array($file_ext, $allowed_ext)) {
            $new_file_name = "donor_" . $user_id . "_" . time() . "." . $file_ext;
            $destination = "../uploads/" . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $destination)) {
                $image_path = $new_file_name;
            }
        }
    }

    $insert_stmt = $conn->prepare("INSERT INTO donor_profiles (user_id, full_name, profile_image, blood_group, phone, dob, address, last_donation, availability) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    // issssssss (1 int, 8 strings)
    $insert_stmt->bind_param("issssssss", $user_id, $full_name, $image_path, $blood_group, $phone, $dob, $address, $last_donation, $availability);

    if ($insert_stmt->execute()) {
        header("Location: request.php"); 
        exit();
    } else {
        echo "Error saving donor profile: " . $insert_stmt->error;
    }
}
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

    <!-- DONOR DASHBOARD -->
    <div class="dashboard">
        <div class="mode-switcher">
            <button type="button" id="logoutBtn" class="logout-btn">Logout</button>
            <a href="patient.php" class="switch-mode-btn" style="background-color: #666;">Switch to Patient Mode</a>
        </div>

        <?php if ($has_profile): ?>
            <!-- Header for users who HAVE a profile -->
            <h1>Your Donor Profile ❤️</h1>
            <p>Your profile is active. Patients can currently see your details.</p>
            <a href="request.php" style="display:inline-block; padding:13px 25px; color:#fff; background-color:#d62828; border-radius:8px; text-decoration:none; font-weight:bold;">
                View Blood Requests
            </a>
        <?php else: ?>
            <!-- Header for users who DO NOT have a profile -->
            <h1>Welcome to Rokto Link ❤️</h1>
            <p>Create your donor profile so people can find you when blood is needed.</p>
        <?php endif; ?>
    </div>

    <!-- MAIN CONTAINER (Forced to display via PHP style tag) -->
    <div class="profile-container" id="profileForm" style="display: flex;">

        <?php if (!$has_profile): ?>
        
        <!-- ============================================== -->
        <!-- VIEW 1: THE REGISTRATION FORM (Only shows if no profile) -->
        <!-- ============================================== -->
        <div class="form-section">
            <h1>Create Your Profile</h1>
            <form id="createProfileForm" action="dashboard.php" method="POST" enctype="multipart/form-data">

                <label for="profilePicture">Profile Picture</label>
                <div class="image-preview">
                    <img id="previewImage" src="" alt="Profile Preview">
                    <p id="imageText">No image selected</p>
                </div>
                <input type="file" id="profilePicture" name="profilePicture" accept="image/png, image/jpeg, image/jpg">

                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" required>

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

                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" required>

                <label for="address">Address</label>
                <textarea id="address" name="address" placeholder="Enter your address" rows="4" required></textarea>

                <label for="lastDonation">Last Blood Donation Date</label>
                <input type="date" id="lastDonation" name="lastDonation">

                <label for="availability">Available for Blood Donation?</label>
                <select id="availability" name="availability" required>
                    <option value="">Select</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>

                <button type="submit" class="submit-button">Create Profile</button>
            </form>
        </div>

        <?php else: ?>

        <!-- ============================================== -->
        <!-- VIEW 2: THE PROFILE CARD (Only shows if profile exists) -->
        <!-- ============================================== -->
        <div class="profile-card" id="profileCard" style="display: block;">
            
            <div class="card-heading">
                <span>DONOR PROFILE</span>
            </div>

            <?php if (!empty($profile_data['profile_image'])): ?>
                <img src="../uploads/<?php echo htmlspecialchars($profile_data['profile_image']); ?>" alt="Profile Image" style="width: 120px; height: 120px; margin: 0 auto 15px; display: block; object-fit: cover; border: 4px solid #d62828; border-radius: 50%;">
            <?php else: ?>
                <div class="default-profile-image" id="defaultProfileImage">👤</div>
            <?php endif; ?>

            <h2 id="cardName"><?php echo htmlspecialchars($profile_data['full_name']); ?></h2>

            <div class="blood-badge" id="cardBlood">
                <?php echo htmlspecialchars($profile_data['blood_group']); ?>
            </div>

            <div class="profile-information">
                <p><strong>📞 Phone:</strong> <span><?php echo htmlspecialchars($profile_data['phone']); ?></span></p>
                <p>
                    <strong>🎂 Date of Birth:</strong> 
                    <span>
                        <?php 
                            $dob = date_create($profile_data['dob']);
                            echo date_format($dob, "d M, Y");
                        ?>
                    </span>
                </p>
                <p><strong>📍 Address:</strong> <span><?php echo htmlspecialchars($profile_data['address']); ?></span></p>
                <p>
                    <strong>🩸 Last Donation:</strong> 
                    <span>
                        <?php 
                            if (!empty($profile_data['last_donation']) && $profile_data['last_donation'] !== '0000-00-00') {
                                $date = date_create($profile_data['last_donation']);
                                echo date_format($date, "d M, Y");
                            } else {
                                echo "Never donated yet";
                            }
                        ?>
                    </span>
                </p>
                <p>
                    <strong>✅ Available:</strong> 
                    <span style="font-weight: bold; color: <?php echo ($profile_data['availability'] == 'Yes') ? '#138a36' : '#d62828'; ?>;">
                        <?php echo htmlspecialchars($profile_data['availability']); ?>
                    </span>
                </p>
            </div>
        </div>
        
        <?php endif; ?>

    </div>

    <!-- JAVASCRIPT -->
    <script src="../javascript/script.js"></script>
</body>
</html>