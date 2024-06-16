<?php

include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $firstName = $_POST['firstName'];
   $lastName = $_POST['lastName'];
   $email = $_POST['email'];
   $password = $_POST['password'];
   $roles = 'admin';
   $employeeId = NULL;



   $stmt = $conn->prepare("INSERT INTO user (firstName, lastName, email, password, roles, employeeId) VALUES (?, ?, ?, ?, ?, ?)");
   $stmt->bind_param("sssssi", $firstName, $lastName, $email, $password, $roles, $employeeId);

   if ($stmt->execute()) {
      echo "<script>alert('New account created successfully'); window.location.href='index.php';</script>";
   } else {
      echo "Error: " . $stmt->error;
   }

   $stmt->close();
   $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
   <title>Create Account</title>
</head>
<link rel="stylesheet" href="./assets/css/adminReg.css">
<body>
   <div class="container">
      <h2>Create Account</h2>
   <div class="form-container">
   <form method="POST" action="">
      
      <input type="text" id="firstName" name="firstName" placeholder="First Name" required><br><br>
     
      <input type="text" id="lastName" name="lastName" placeholder="Last Name" required><br><br>
     
      <input type="email" id="email" name="email" placeholder="Email" required><br><br>
      
      <input type="password" id="password" name="password" placeholder="Password" required><br><br>
      <button type="submit">Create Account</button>
   </form>
   </div>
   </div>
</body>

</html>