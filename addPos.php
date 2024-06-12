<?php
session_start();

if (!isset($_SESSION['userId'])) {
   header("Location: index.php");
   exit();
}

include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $positionName = $_POST['positionName'];
   $salaryGrade = $_POST['salaryGrade'];

   if (!empty($positionName) && !empty($salaryGrade)) {
      $stmt = $conn->prepare("INSERT INTO position (positionName, salaryGrade) VALUES (?, ?)");
      $stmt->bind_param("ss", $positionName, $salaryGrade);

      if ($stmt->execute()) {
         echo "Position added successfully!";
      } else {
         echo "Error: " . $stmt->error;
      }

      $stmt->close();
   } else {
      echo "Position name and salary grade cannot be empty.";
   }
}

$conn->close();

header("Location: company.php");
exit();
?>
