<?php
include 'connect.php'; // Include your database connection

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['userId'];

// Validate input
if (empty($userId)) {
   echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
   exit();
}

// Prepare the DELETE query
$query = "DELETE FROM user WHERE userId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $userId);

if ($stmt->execute()) {
   echo json_encode(['success' => true]);
} else {
   echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
}

$stmt->close();
$conn->close();
?>
