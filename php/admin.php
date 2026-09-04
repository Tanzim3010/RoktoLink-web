<?php
session_start();
require_once 'db_connect.php';

// 1. STRICT ADMIN SECURITY CHECK
// If they aren't logged in, or their email isn't the admin email, kick them out
if (!isset($_SESSION['user_id']) || $_SESSION['email'] !== 'admin@roktolink.com') {
    header("Location: ../Html/index.html");
    exit();
}

// 2. HANDLE DELETION REQUESTS
if (isset($_GET['delete_user'])) {
    $del_id = (int)$_GET['delete_user'];
    // Thanks to your ON DELETE CASCADE setup, deleting from 'users' wipes their donor/patient profiles too!
    $conn->query("DELETE FROM users WHERE id = $del_id");
    header("Location: admin.php?msg=deleted");
    exit();
}

// 3. FETCH ALL DATA
$users = $conn->query("SELECT * FROM users ORDER BY id DESC");
$donors = $conn->query("SELECT * FROM donor_profiles ORDER BY id DESC");
$patients = $conn->query("SELECT * FROM patient_profiles ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin page </title>
    <!-- Updated CSS path since this is in the php folder -->
    <link rel="stylesheet" href="../Css/style.css">
    <style>
        /* Basic styling for the admin tables so they look clean */
        body { padding: 20px; background-color: #f8f9fa; }
        #admin_welcome { color: #d62828; border-bottom: 2px solid #d62828; padding-bottom: 10px; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .data-section { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #2b2d42; color: white; }
        tr:hover { background-color: #f1f1f1; }
        .delete-btn { background-color: #e63946; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 13px; }
        .delete-btn:hover { background-color: #c1121f; }
        .alert { background-color: #2a9d8f; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: bold;}
        .logout-btn { background-color: #2b2d42; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>
    
    <div class="admin-header">
        <h1 id="admin_welcome">RoktoLink Admin</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert">User account and all associated profiles were successfully deleted.</div>
    <?php endif; ?>

    <!-- USERS TABLE -->
    <div class="data-section">
        <h2>Registered Accounts (Master List)</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>NID</th>
                <th>Action</th>
            </tr>
            <?php while($u = $users->fetch_assoc()): ?>
            <tr>
                <td><?php echo $u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['name']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><?php echo htmlspecialchars($u['nid']); ?></td>
                <td>
                    <?php if($u['email'] !== 'admin@roktolink.com'): ?>
                        <a href="admin.php?delete_user=<?php echo $u['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure? This deletes their donor and patient profiles entirely!');">Delete User</a>
                    <?php else: ?>
                        <span style="color: #888; font-weight: bold;">System Admin</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- DONORS TABLE -->
    <div class="data-section">
        <h2>Active Donor Profiles</h2>
        <table>
            <tr>
                <th>Profile ID</th>
                <th>User ID</th>
                <th>Name</th>
                <th>Blood Group</th>
                <th>Phone</th>
                <th>Available</th>
            </tr>
            <?php while($d = $donors->fetch_assoc()): ?>
            <tr>
                <td><?php echo $d['id']; ?></td>
                <td><?php echo $d['user_id']; ?></td>
                <td><?php echo htmlspecialchars($d['full_name']); ?></td>
                <td><strong style="color: #d62828;"><?php echo htmlspecialchars($d['blood_group']); ?></strong></td>
                <td><?php echo htmlspecialchars($d['phone']); ?></td>
                <td><?php echo htmlspecialchars($d['availability']); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- PATIENTS TABLE -->
    <div class="data-section">
        <h2>Active Patient Requests</h2>
        <table>
            <tr>
                <th>Profile ID</th>
                <th>User ID</th>
                <th>Patient Name</th>
                <th>Blood Group</th>
                <th>Hospital</th>
                <th>Urgency</th>
            </tr>
            <?php while($p = $patients->fetch_assoc()): ?>
            <tr>
                <td><?php echo $p['id']; ?></td>
                <td><?php echo $p['user_id']; ?></td>
                <td><?php echo htmlspecialchars($p['full_name']); ?></td>
                <td><strong style="color: #d62828;"><?php echo htmlspecialchars($p['blood_group']); ?></strong></td>
                <td><?php echo htmlspecialchars($p['hospital']); ?></td>
                <td style="font-weight: bold; color: <?php echo ($p['urgency']=='Emergency') ? '#d62828' : '#2b2d42'; ?>;">
                    <?php echo htmlspecialchars($p['urgency']); ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

</body>
</html>