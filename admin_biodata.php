<?php
require_once 'config/database.php';

// Initialize database silently
initializeDatabase();

// Check if user is logged in and is admin (you can modify this logic)
requireLogin();

$conn = getDBConnection();
$message = '';
$messageType = '';
$action = $_GET['action'] ?? 'list';
$userId = $_GET['user_id'] ?? null;

// Handle admin CRUD operations
switch($action) {
    case 'delete':
        if ($userId) {
            $result = deleteUserBiodata($conn, $userId);
            $message = $result['message'];
            $messageType = $result['type'];
        }
        header('Location: admin_biodata.php');
        exit();
        break;
        
    case 'view':
        $biodata = getBiodataByUserId($conn, $userId);
        break;
}

// Get all biodata records with user information
$allBiodata = getAllBiodata($conn);

// Admin CRUD Functions
function getAllBiodata($conn) {
    $sql = "SELECT b.*, u.username, u.first_name, u.last_name, u.email as user_email 
            FROM biodata b 
            JOIN users u ON b.user_id = u.id 
            ORDER BY b.created_at DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getBiodataByUserId($conn, $userId) {
    $stmt = $conn->prepare("SELECT b.*, u.username, u.first_name, u.last_name, u.email as user_email 
                           FROM biodata b 
                           JOIN users u ON b.user_id = u.id 
                           WHERE b.user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

function deleteUserBiodata($conn, $userId) {
    $stmt = $conn->prepare("DELETE FROM biodata WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        return ['message' => 'Biodata deleted successfully!', 'type' => 'success'];
    } else {
        return ['message' => 'Error deleting biodata: ' . $conn->error, 'type' => 'error'];
    }
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Biodata Management</title>
    <link rel="stylesheet" href="css/biodata.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .biodata-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .biodata-table th,
        .biodata-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .biodata-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .biodata-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .biodata-table tr:hover {
            background-color: #f5f5f5;
        }
        .btn {
            display: inline-block;
            padding: 6px 12px;
            margin: 2px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            font-size: 12px;
        }
        .btn-primary { background: #007bff; }
        .btn-danger { background: #dc3545; }
        .btn-info { background: #17a2b8; }
        .btn:hover { opacity: 0.8; }
        .profile-img-small {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .biodata-detail {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .detail-field {
            margin: 10px 0;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-field strong {
            display: inline-block;
            width: 150px;
            color: #333;
        }
        .back-btn {
            margin: 20px 0;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin: 20px 0;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            flex: 1;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="header">
            <h2>ADMIN BIODATA MANAGEMENT</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</span>
                <a href="biodata_crud.php" class="btn btn-info">My Biodata</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($action == 'list'): ?>
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($allBiodata); ?></div>
                    <div>Total Biodata Records</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count(array_filter($allBiodata, function($b) { return !empty($b['profile_picture']); })); ?></div>
                    <div>With Profile Pictures</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count(array_filter($allBiodata, function($b) { return $b['gender'] == 'Male'; })); ?></div>
                    <div>Male</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count(array_filter($allBiodata, function($b) { return $b['gender'] == 'Female'; })); ?></div>
                    <div>Female</div>
                </div>
            </div>

            <h3>All Biodata Records</h3>
            <?php if (empty($allBiodata)): ?>
                <p>No biodata records found.</p>
            <?php else: ?>
                <table class="biodata-table">
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>User</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Date Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allBiodata as $biodata): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($biodata['profile_picture'])): ?>
                                        <img src="<?php echo htmlspecialchars($biodata['profile_picture']); ?>" 
                                             alt="Profile" class="profile-img-small">
                                    <?php else: ?>
                                        <div style="width: 40px; height: 40px; background: #ddd; border-radius: 50%; display: inline-block;"></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($biodata['username']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($biodata['first_name'] . ' ' . $biodata['last_name']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($biodata['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($biodata['email']); ?></td>
                                <td><?php echo htmlspecialchars($biodata['phone']); ?></td>
                                <td><?php echo htmlspecialchars($biodata['gender']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($biodata['created_at'])); ?></td>
                                <td>
                                    <a href="?action=view&user_id=<?php echo $biodata['user_id']; ?>" 
                                       class="btn btn-primary">View</a>
                                    <a href="?action=delete&user_id=<?php echo $biodata['user_id']; ?>" 
                                       class="btn btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this biodata?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        <?php elseif ($action == 'view' && $biodata): ?>
            <div class="back-btn">
                <a href="?action=list" class="btn btn-info">← Back to List</a>
            </div>

            <div class="biodata-detail">
                <h3>Biodata Details for <?php echo htmlspecialchars($biodata['full_name']); ?></h3>
                
                <div class="detail-field">
                    <strong>User Account:</strong> <?php echo htmlspecialchars($biodata['username']); ?> 
                    (<?php echo htmlspecialchars($biodata['first_name'] . ' ' . $biodata['last_name']); ?>)
                </div>
                
                <?php if (!empty($biodata['profile_picture'])): ?>
                    <div class="detail-field">
                        <strong>Profile Picture:</strong><br>
                        <img src="<?php echo htmlspecialchars($biodata['profile_picture']); ?>" 
                             alt="Profile Picture" style="max-width: 200px; max-height: 200px; border-radius: 10px;">
                    </div>
                <?php endif; ?>
                
                <div class="detail-field">
                    <strong>Full Name:</strong> <?php echo htmlspecialchars($biodata['full_name']); ?>
                </div>
                <div class="detail-field">
                    <strong>Father's Name:</strong> <?php echo htmlspecialchars($biodata['father_name']); ?>
                </div>
                <div class="detail-field">
                    <strong>Mother's Name:</strong> <?php echo htmlspecialchars($biodata['mother_name']); ?>
                </div>
                <div class="detail-field">
                    <strong>Date of Birth:</strong> <?php echo htmlspecialchars($biodata['date_of_birth']); ?>
                </div>
                <div class="detail-field">
                    <strong>Gender:</strong> <?php echo htmlspecialchars($biodata['gender']); ?>
                </div>
                <div class="detail-field">
                    <strong>Address:</strong> <?php echo htmlspecialchars($biodata['address']); ?>
                </div>
                <div class="detail-field">
                    <strong>Phone:</strong> <?php echo htmlspecialchars($biodata['phone']); ?>
                </div>
                <div class="detail-field">
                    <strong>Email:</strong> <?php echo htmlspecialchars($biodata['email']); ?>
                </div>
                <div class="detail-field">
                    <strong>LinkedIn:</strong> 
                    <?php if (!empty($biodata['linkedin'])): ?>
                        <a href="<?php echo htmlspecialchars($biodata['linkedin']); ?>" target="_blank">
                            <?php echo htmlspecialchars($biodata['linkedin']); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="detail-field">
                    <strong>GitHub:</strong> 
                    <?php if (!empty($biodata['github'])): ?>
                        <a href="<?php echo htmlspecialchars($biodata['github']); ?>" target="_blank">
                            <?php echo htmlspecialchars($biodata['github']); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="detail-field">
                    <strong>Education:</strong> <?php echo nl2br(htmlspecialchars($biodata['education'])); ?>
                </div>
                <div class="detail-field">
                    <strong>Skills:</strong> <?php echo nl2br(htmlspecialchars($biodata['skills'])); ?>
                </div>
                <div class="detail-field">
                    <strong>Languages:</strong> <?php echo htmlspecialchars($biodata['languages']); ?>
                </div>
                <div class="detail-field">
                    <strong>Marital Status:</strong> <?php echo htmlspecialchars($biodata['marital_status']); ?>
                </div>
                <div class="detail-field">
                    <strong>Hobbies:</strong> <?php echo htmlspecialchars($biodata['hobbies']); ?>
                </div>
                <div class="detail-field">
                    <strong>Blood Group:</strong> <?php echo htmlspecialchars($biodata['blood_group']); ?>
                </div>
                <div class="detail-field">
                    <strong>Website:</strong> 
                    <?php if (!empty($biodata['website'])): ?>
                        <a href="<?php echo htmlspecialchars($biodata['website']); ?>" target="_blank">
                            <?php echo htmlspecialchars($biodata['website']); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="detail-field">
                    <strong>Created:</strong> <?php echo date('F j, Y g:i A', strtotime($biodata['created_at'])); ?>
                </div>
                <div class="detail-field">
                    <strong>Last Updated:</strong> <?php echo date('F j, Y g:i A', strtotime($biodata['updated_at'])); ?>
                </div>

                <div style="margin-top: 20px;">
                    <a href="?action=delete&user_id=<?php echo $biodata['user_id']; ?>" 
                       class="btn btn-danger"
                       onclick="return confirm('Are you sure you want to delete this biodata?')">Delete Biodata</a>
                </div>
            </div>

        <?php elseif ($action == 'view' && !$biodata): ?>
            <div class="back-btn">
                <a href="?action=list" class="btn btn-info">← Back to List</a>
            </div>
            <p>Biodata not found.</p>

        <?php endif; ?>
    </div>
</body>
</html>
