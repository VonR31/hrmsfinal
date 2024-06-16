<?php
include 'connect.php'; // Include your database connection

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);
$employeeId = $data['employee'];

// Validate input
if (empty($employeeId)) {
   echo json_encode(['success' => false, 'message' => 'Invalid employee']);
   exit();
}

// Prepare the DELETE query
$query = "DELETE FROM payroll WHERE employeeId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $employeeId);

if ($stmt->execute()) {
   echo json_encode(['success' => true]);
} else {
   echo json_encode(['success' => false, 'message' => 'Failed to delete payroll']);
}

$stmt->close();
$conn->close();
?>