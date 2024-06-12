<?php
session_start();

if (!isset($_SESSION['userId']) || $_SESSION['role'] != 'Employee') {
   header("Location: index.php");
   exit();
}

$firstName = htmlspecialchars($_SESSION['firstName']);
$lastName = htmlspecialchars($_SESSION['lastName']);
$userId = intval($_SESSION['userId']);

// Include your database connection
include 'connect.php';

// Fetch payroll data for the logged-in employee
$query = "SELECT 
            employee.firstName,
            employee.lastName,
            department.departmentName, 
            position.positionName, 
            position.salaryGrade, 
            payroll.salary, 
            payroll.bonus, 
            payroll.deductions, 
            payroll.net_pay, 
            payroll.pay_date 
          FROM payroll 
          INNER JOIN employee ON payroll.employeeId = employee.employeeId
          INNER JOIN department ON payroll.departmentId = department.departmentId
          INNER JOIN position ON payroll.positionId = position.positionId
          WHERE payroll.employeeId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Employee Dashboard</title>
   <!-- Bootstrap CSS -->
   <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
   <!-- Font Awesome -->
   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
   <!-- Custom CSS -->
   <style>
      body {
         background-color: #e3f2fd;
         font-family: 'Montserrat', sans-serif;
      }

      .navbar {
         background-color: navy;
         box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }

      .navbar-brand {
         font-weight: bold;
         color: #ffffff;
      }

      .navbar-nav .nav-link {
         color: #ffffff;
      }

      .navbar-nav .nav-link.active {
         font-weight: bold;
      }

      .page-title {
         font-size: 24px;
         font-weight: bold;
         margin-top: 20px;
         color: #343a40;
      }

      .table-responsive {
         border-radius: 25px;
      }
   </style>
</head>

<body>
   <nav class="navbar navbar-expand-lg navbar-dark ">
      <div class="container-fluid">
         <a class="navbar-brand" href="employeeDashboard.php">HRMS</a>
         <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
         </button>
         <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
               <li class="nav-item">
                  <a class="nav-link" href="employeeDashboard.php">Dashboard</a>
               </li>
               <li class="nav-item">
                  <a class="nav-link" href="employeeProfile.php">Profile</a>
               </li>
               <li class="nav-item">
                  <a class="nav-link" href="employeeAttendance.php">Attendance</a>
               </li>
               <li class="nav-item">
                  <a class="nav-link" href="employeeLeave.php">Leave</a>
               </li>
               <li class="nav-item">
                  <a class="nav-link active" href="employeePayroll.php">Payroll</a>
               </li>
               <li class="nav-item">
                  <a class="nav-link" href="logout.php">Logout</a>
               </li>
            </ul>
         </div>
      </div>
   </nav>

   <div class="container mt-4">
      <div class="row">
         <div class="col-md-12">
            <h2 class="page-title text-center">Welcome, <?php echo $firstName . ' ' . $lastName; ?>!</h2>
         </div>
      </div>
   </div>

   <div class="container mt-4">
      <div class="col-xl-12">
         <h3 class="text-center">Your Payroll Details</h3>
      </div>
         <div class="table-responsive">
            <div class="card">
            <table class="table table-striped">
               <thead>
                  <tr>
                     <th>Department</th>
                     <th>Position</th>
                     <th>Salary</th>
                     <th>Bonus</th>
                     <th>Deductions</th>
                     <th>Net Pay</th>
                     <th>Pay Date</th>
                  </tr>
               </thead>
               <tbody>
                  <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                           <td><?php echo htmlspecialchars($row['departmentName']); ?></td>
                           <td><?php echo htmlspecialchars($row['positionName']); ?></td>
                           <td><?php echo htmlspecialchars($row['salary']); ?></td>
                           <td><?php echo htmlspecialchars($row['bonus']); ?></td>
                           <td><?php echo htmlspecialchars($row['deductions']); ?></td>
                           <td><?php echo htmlspecialchars($row['net_pay']); ?></td>
                           <td><?php echo htmlspecialchars($row['pay_date']); ?></td>
                        </tr>
                  <?php endwhile; ?>
               </tbody>
            </table>
           </div> 
         </div>
      </div>
   

   <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
   <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
   <!-- Font Awesome JS -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
</body>

</html>