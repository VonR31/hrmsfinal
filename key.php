<?php
if (isset($_POST['company_key'])) {
   $company_key = $_POST['company_key'];
   $correct_key = 'company'; // Replace with your actual company key

   if ($company_key === $correct_key) {
      header('Location: adminsignUp.php');
      exit();
   } else {
      $error_message = "Invalid company key.";
   }
}
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="/assets/css/adminReg.css">
<head>
   <title>Enter Company Key</title>
</head>

<body>
   <div class="container">
      <h2>Enter Company Key</h2>
   <div class="form-container">
   <?php if (isset($error_message)): ?>
      <p style="color:red;"><?php echo $error_message; ?></p>
   <?php endif; ?>
   
   <form method="POST" action="">
      <input type="password" id="company_key" name="company_key" required>
      <div class="container-btn">
         <button type="submit">Submit</button>
      </div>
   </form>
   </div>
   </div>
</body>

</html>