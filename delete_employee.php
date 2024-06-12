<?php
include 'connect.php';
require ('fpdf/fpdf.php');

// Function to fetch employee data from various tables
function fetchEmployeeData($conn, $employeeId)
{
   $employeeData = [];

   // Fetch from employee table
   $queryEmployee = "SELECT * FROM employee WHERE employeeId = ?";
   $stmt = $conn->prepare($queryEmployee);
   $stmt->bind_param('i', $employeeId);
   $stmt->execute();
   $result = $stmt->get_result();
   $employeeData['employee'] = $result->fetch_assoc();

   // Fetch from attendance table
   $queryAttendance = "SELECT * FROM attendance WHERE employeeId = ?";
   $stmt = $conn->prepare($queryAttendance);
   $stmt->bind_param('i', $employeeId);
   $stmt->execute();
   $result = $stmt->get_result();
   $employeeData['attendance'] = $result->fetch_all(MYSQLI_ASSOC);

   // Fetch from leave table
   $queryLeave = "SELECT * FROM `leave` WHERE employeeId = ?";
   $stmt = $conn->prepare($queryLeave);
   $stmt->bind_param('i', $employeeId);
   $stmt->execute();
   $result = $stmt->get_result();
   $employeeData['leave'] = $result->fetch_all(MYSQLI_ASSOC);

   // Fetch from payroll table
   $queryPayroll = "SELECT * FROM payroll WHERE employeeId = ?";
   $stmt = $conn->prepare($queryPayroll);
   $stmt->bind_param('i', $employeeId);
   $stmt->execute();
   $result = $stmt->get_result();
   $employeeData['payroll'] = $result->fetch_all(MYSQLI_ASSOC);

   // Fetch from tasks table
   $queryTasks = "SELECT * FROM tasks WHERE employeeId = ?";
   $stmt = $conn->prepare($queryTasks);
   $stmt->bind_param('i', $employeeId);
   $stmt->execute();
   $result = $stmt->get_result();
   $employeeData['tasks'] = $result->fetch_all(MYSQLI_ASSOC);

   return $employeeData;
}

// Fetch POST data
$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['userId'];

// Fetch employeeId based on userId from users table
$queryEmployeeId = "SELECT employeeId FROM users WHERE userId = ?";
$stmt = $conn->prepare($queryEmployeeId);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
   echo json_encode(['success' => false, 'message' => 'Employee not found']);
   exit();
}
$employeeId = $result->fetch_assoc()['employeeId'];

// Fetch employee data from all tables
$employeeData = fetchEmployeeData($conn, $employeeId);

// Generate PDF
class PDF extends FPDF
{
   function Header()
   {
      $this->SetFont('Arial', 'B', 12);
      $this->Cell(0, 10, 'Employee Report', 0, 1, 'C');
      $this->Ln(10);
   }

   function ChapterTitle($title)
   {
      $this->SetFont('Arial', 'B', 12);
      $this->Cell(0, 10, $title, 0, 1);
      $this->Ln(4);
   }

   function ChapterBody($body)
   {
      $this->SetFont('Arial', '', 12);
      $this->MultiCell(0, 10, $body);
      $this->Ln();
   }

   function AddEmployeeData($title, $data)
   {
      $this->ChapterTitle($title);
      foreach ($data as $key => $value) {
         $this->ChapterBody("$key: $value");
      }
   }

   function AddEmployeeTable($title, $data)
   {
      $this->ChapterTitle($title);
      $this->SetFont('Arial', '', 12);
      foreach ($data as $row) {
         foreach ($row as $key => $value) {
            $this->Cell(40, 10, $value, 1);
         }
         $this->Ln();
      }
      $this->Ln();
   }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Add employee personal data
$pdf->AddEmployeeData('Employee Personal Data', $employeeData['employee']);

// Add employee attendance data
$pdf->AddEmployeeTable('Attendance Data', $employeeData['attendance']);

// Add employee leave data
$pdf->AddEmployeeTable('Leave Data', $employeeData['leave']);

// Add employee payroll data
$pdf->AddEmployeeTable('Payroll Data', $employeeData['payroll']);

// Add employee tasks data
$pdf->AddEmployeeTable('Tasks Data', $employeeData['tasks']);

$pdf->Output('F', 'employee_report.pdf');

// Delete records from all tables
$tables = ['attendance', 'leave', 'payroll', 'tasks', 'employee', 'users'];
foreach ($tables as $table) {
   $column = ($table == 'users') ? 'userId' : 'employeeId';
   $queryDelete = "DELETE FROM $table WHERE $column = ?";
   $stmt = $conn->prepare($queryDelete);
   $stmt->bind_param('i', $employeeId);
   if (!$stmt->execute()) {
      echo json_encode(['success' => false, 'message' => "Failed to delete from $table"]);
      exit();
   }
}

echo json_encode(['success' => true, 'message' => 'Employee data deleted successfully']);
?>