<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config/database.php';

// Initialize database silently
initializeDatabase();

$conn = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Parse the request URL
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));
$endpoint = $request[0] ?? '';
$id = $request[1] ?? null;

// Authentication check (simple token-based auth)
function authenticate() {
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? '';
    
    // Simple token validation - you should implement proper JWT or session validation
    if (empty($token)) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        exit();
    }
    
    // For demo purposes, we'll extract user_id from a simple token
    // In production, use proper JWT or session validation
    $userId = str_replace('Bearer ', '', $token);
    return is_numeric($userId) ? (int)$userId : null;
}

// Response helper
function sendResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit();
}

// Error handler
function sendError($message, $status = 400) {
    http_response_code($status);
    echo json_encode(['error' => $message]);
    exit();
}

// Sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Main API routing
switch ($endpoint) {
    case 'biodata':
        handleBiodataAPI($method, $id, $input, $conn);
        break;
    case 'users':
        handleUsersAPI($method, $id, $input, $conn);
        break;
    default:
        sendError('Invalid endpoint', 404);
}

function handleBiodataAPI($method, $id, $input, $conn) {
    $userId = authenticate();
    
    switch ($method) {
        case 'GET':
            if ($id) {
                // Get specific biodata by user ID
                getBiodataAPI($conn, $id);
            } else {
                // Get current user's biodata
                getBiodataAPI($conn, $userId);
            }
            break;
            
        case 'POST':
            // Create new biodata
            createBiodataAPI($conn, $userId, $input);
            break;
            
        case 'PUT':
            // Update biodata
            updateBiodataAPI($conn, $userId, $input);
            break;
            
        case 'DELETE':
            // Delete biodata
            deleteBiodataAPI($conn, $userId);
            break;
            
        default:
            sendError('Method not allowed', 405);
    }
}

function handleUsersAPI($method, $id, $input, $conn) {
    // For admin operations - list all biodata
    $userId = authenticate();
    
    switch ($method) {
        case 'GET':
            // Get all biodata records (admin function)
            getAllBiodataAPI($conn);
            break;
            
        default:
            sendError('Method not allowed', 405);
    }
}

function getBiodataAPI($conn, $userId) {
    $stmt = $conn->prepare("SELECT b.*, u.username, u.first_name, u.last_name, u.email as user_email 
                           FROM biodata b 
                           JOIN users u ON b.user_id = u.id 
                           WHERE b.user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $biodata = $result->fetch_assoc();
        
        // Convert date fields to proper format
        if ($biodata['date_of_birth']) {
            $biodata['date_of_birth'] = date('Y-m-d', strtotime($biodata['date_of_birth']));
        }
        
        sendResponse([
            'success' => true,
            'data' => $biodata
        ]);
    } else {
        sendResponse([
            'success' => false,
            'message' => 'No biodata found'
        ], 404);
    }
}

function getAllBiodataAPI($conn) {
    $sql = "SELECT b.*, u.username, u.first_name, u.last_name, u.email as user_email 
            FROM biodata b 
            JOIN users u ON b.user_id = u.id 
            ORDER BY b.created_at DESC";
    $result = $conn->query($sql);
    $biodata = $result->fetch_all(MYSQLI_ASSOC);
    
    // Format dates
    foreach ($biodata as &$record) {
        if ($record['date_of_birth']) {
            $record['date_of_birth'] = date('Y-m-d', strtotime($record['date_of_birth']));
        }
        $record['created_at'] = date('Y-m-d H:i:s', strtotime($record['created_at']));
        $record['updated_at'] = date('Y-m-d H:i:s', strtotime($record['updated_at']));
    }
    
    sendResponse([
        'success' => true,
        'data' => $biodata,
        'count' => count($biodata)
    ]);
}

function createBiodataAPI($conn, $userId, $input) {
    // Validate required fields
    if (empty($input['full_name'])) {
        sendError('Full name is required');
    }
    
    // Check if biodata already exists
    $stmt = $conn->prepare("SELECT id FROM biodata WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        sendError('Biodata already exists for this user. Use PUT to update.');
    }
    
    // Prepare data
    $fullName = sanitizeInput($input['full_name']);
    $fatherName = sanitizeInput($input['father_name'] ?? '');
    $motherName = sanitizeInput($input['mother_name'] ?? '');
    $dob = $input['date_of_birth'] ?? null;
    $gender = $input['gender'] ?? '';
    $address = sanitizeInput($input['address'] ?? '');
    $phone = sanitizeInput($input['phone'] ?? '');
    $email = sanitizeInput($input['email'] ?? '');
    $linkedin = sanitizeInput($input['linkedin'] ?? '');
    $github = sanitizeInput($input['github'] ?? '');
    $education = sanitizeInput($input['education'] ?? '');
    $skills = sanitizeInput($input['skills'] ?? '');
    $languages = sanitizeInput($input['languages'] ?? '');
    $maritalStatus = sanitizeInput($input['marital_status'] ?? '');
    $hobbies = sanitizeInput($input['hobbies'] ?? '');
    $bloodGroup = $input['blood_group'] ?? '';
    $website = sanitizeInput($input['website'] ?? '');
    $profilePicture = sanitizeInput($input['profile_picture'] ?? '');
    
    $stmt = $conn->prepare("INSERT INTO biodata (user_id, full_name, father_name, mother_name, date_of_birth, gender, address, phone, email, linkedin, github, education, skills, languages, marital_status, hobbies, blood_group, website, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("issssssssssssssssss", $userId, $fullName, $fatherName, $motherName, $dob, $gender, $address, $phone, $email, $linkedin, $github, $education, $skills, $languages, $maritalStatus, $hobbies, $bloodGroup, $website, $profilePicture);
    
    if ($stmt->execute()) {
        $newId = $conn->insert_id;
        sendResponse([
            'success' => true,
            'message' => 'Biodata created successfully',
            'data' => ['id' => $newId]
        ], 201);
    } else {
        sendError('Error creating biodata: ' . $conn->error, 500);
    }
}

function updateBiodataAPI($conn, $userId, $input) {
    // Check if biodata exists
    $stmt = $conn->prepare("SELECT id FROM biodata WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        sendError('No biodata found to update. Use POST to create new biodata.', 404);
    }
    
    // Prepare data
    $fullName = sanitizeInput($input['full_name'] ?? '');
    $fatherName = sanitizeInput($input['father_name'] ?? '');
    $motherName = sanitizeInput($input['mother_name'] ?? '');
    $dob = $input['date_of_birth'] ?? null;
    $gender = $input['gender'] ?? '';
    $address = sanitizeInput($input['address'] ?? '');
    $phone = sanitizeInput($input['phone'] ?? '');
    $email = sanitizeInput($input['email'] ?? '');
    $linkedin = sanitizeInput($input['linkedin'] ?? '');
    $github = sanitizeInput($input['github'] ?? '');
    $education = sanitizeInput($input['education'] ?? '');
    $skills = sanitizeInput($input['skills'] ?? '');
    $languages = sanitizeInput($input['languages'] ?? '');
    $maritalStatus = sanitizeInput($input['marital_status'] ?? '');
    $hobbies = sanitizeInput($input['hobbies'] ?? '');
    $bloodGroup = $input['blood_group'] ?? '';
    $website = sanitizeInput($input['website'] ?? '');
    $profilePicture = sanitizeInput($input['profile_picture'] ?? '');
    
    $stmt = $conn->prepare("UPDATE biodata SET full_name=?, father_name=?, mother_name=?, date_of_birth=?, gender=?, address=?, phone=?, email=?, linkedin=?, github=?, education=?, skills=?, languages=?, marital_status=?, hobbies=?, blood_group=?, website=?, profile_picture=? WHERE user_id=?");
    
    $stmt->bind_param("ssssssssssssssssssi", $fullName, $fatherName, $motherName, $dob, $gender, $address, $phone, $email, $linkedin, $github, $education, $skills, $languages, $maritalStatus, $hobbies, $bloodGroup, $website, $profilePicture, $userId);
    
    if ($stmt->execute()) {
        sendResponse([
            'success' => true,
            'message' => 'Biodata updated successfully'
        ]);
    } else {
        sendError('Error updating biodata: ' . $conn->error, 500);
    }
}

function deleteBiodataAPI($conn, $userId) {
    $stmt = $conn->prepare("DELETE FROM biodata WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            sendResponse([
                'success' => true,
                'message' => 'Biodata deleted successfully'
            ]);
        } else {
            sendError('No biodata found to delete', 404);
        }
    } else {
        sendError('Error deleting biodata: ' . $conn->error, 500);
    }
}

$conn->close();
?>
