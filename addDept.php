<?php
session_start();

if (!isset($_SESSION['userId'])) {
   header("Location: index.php");
   exit();
}

include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $departmentName = $_POST['departmentName'];

   if (!empty($departmentName)) {
      $stmt = $conn->prepare("INSERT INTO department (departmentName) VALUES (?)");
      $stmt->bind_param("s", $departmentName);

      if ($stmt->execute()) {
         echo "Department added successfully!";
      } else {
         echo "Error: " . $stmt->error;
      }

      $stmt->close();
   } else {
      echo "Department name cannot be empty.";
   }
}

$conn->close();

header("Location: company.php");
exit();
?>
