<?php
session_start();
include 'connect.php'; // Include your database connection file

if (!isset($_SESSION['userId'])) {
    header("Location: index.php");
    exit();
}

// Retrieve user information from session variables
$firstName = $_SESSION['firstName'];
$lastName = $_SESSION['lastName'];
function fetchEmployees($conn)
{
    $currentMonth = date('Y-m');
    $query = "SELECT employeeId, CONCAT(firstName, ' ', lastName) AS fullName 
              FROM employee 
              WHERE employeeId NOT IN (
                  SELECT employeeId 
                  FROM payroll 
                  WHERE DATE_FORMAT(pay_date, '%Y-%m') = '$currentMonth'
              )";
    $result = mysqli_query($conn, $query);

    $employees = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $employees[] = $row;
    }
    return $employees;
}


// Insert Payroll Details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeId = $_POST['employee'];
    $bonus = $_POST['bonus'];
    $deductions = $_POST['deductions'];

    // Fetch employee details including salary
    $query = "SELECT department.departmentId AS department, position.positionId AS position, position.salaryGrade AS salary
              FROM employee
              INNER JOIN department ON employee.departmentId = department.departmentId
              INNER JOIN position ON employee.positionId = position.positionId
              WHERE employee.employeeId = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $employeeDetails = $result->fetch_assoc();

    if ($employeeDetails) {
        $positionId = $employeeDetails['position'];
        $departmentId = $employeeDetails['department'];
        $basicSalary = $employeeDetails['salary']; // Ensure the correct alias is used for the salary

        $checkPositionQuery = "SELECT COUNT(*) AS count FROM position WHERE positionId = ?";
        $stmt = $conn->prepare($checkPositionQuery);
        $stmt->bind_param("i", $positionId);
        $stmt->execute();
        $result = $stmt->get_result();
        $positionExists = $result->fetch_assoc()['count'] > 0;


        // Calculate net pay based on salary, bonus, and deductions
        $netPay = $basicSalary + $bonus - $deductions;

        // Insert payroll details into the payroll table
        $insertQuery = "INSERT INTO payroll (employeeId, positionId, departmentId, salary, bonus, deductions, net_pay, status, pay_date) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', CURDATE())";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("iiiddds", $employeeId, $positionId, $departmentId, $basicSalary, $bonus, $deductions, $netPay);

        if ($stmt->execute()) {
            echo '<div class="alert alert-success" role="alert">Payroll details added successfully.</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">Error: ' . $stmt->error . '</div>';
        }
    } else {
        echo '<div class="alert alert-danger" role="alert">Invalid employee ID.</div>';
    }
}

// Fetch payroll data
$query = "SELECT 
            employee.firstName,
            employee.lastName, 
            employee.employeeId, 
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
          INNER JOIN position ON payroll.positionId = position.positionId";
$result = mysqli_query($conn, $query);



?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>HR Management System</title>

    <link rel="shortcut icon" href="assets/img/icon.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="main-wrapper">
        <div class="header">
            <div class="header-left">
                <a href="dashboard.php" class="logo">
                    <img src="assets/img/logo.png" alt="Logo" />
                </a>
                <a href="dashboard.php" class="logo logo-small">
                    <img src="assets/img/hrlogo-small.png" alt="Logo" width="30" height="30" />
                </a>
                <a href="javascript:void(0);" id="toggle_btn">
                    <span class="bar-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </a>
            </div>

            <div class="top-nav-search">
                <form>
                    <input type="text" class="form-control" placeholder="" />
                    <button class="btn" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <a class="mobile_btn" id="mobile_btn">
                <i class="fas fa-bars"></i>
            </a>

            <ul class="nav user-menu">
                <li class="nav-item dropdown">
                    <a href="#" class="dropdown-toggle nav-link pr-0" data-toggle="dropdown">
            <i data-feather="bell"></i> <span class="badge badge-pill"></span>
            </a>
                    <div class="dropdown-menu notifications">
                        <div class="topnav-dropdown-header">
                            <span class="notification-title">Notifications</span>
                            <a href="javascript:void(0)" class="clear-noti"> Clear All</a>
                        </div>
                    </div>
                </li>

                <li class="nav-item dropdown has-arrow main-drop">
                    <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                        <span class="user-img">
                            <img src="assets/img/user.jpg" alt="" />
                            <span class="status online"></span>
                        </span>
                        <span><?php echo $firstName . " " . $lastName; ?></span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="profile.php"><i data-feather="user" class="mr-1"></i> Profile</a>
                        <a class="dropdown-item" href="settings.php"><i data-feather="settings" class="mr-1"></i>
                            Settings</a>
                        <a class="dropdown-item" href="logout.php" onclick="return confirmLogout();"><i
                                data-feather="log-out" class="mr-1"></i> Logout</a>
                    </div>
                </li>
            </ul>
            <div class="dropdown mobile-user-menu show">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-ellipsis-v"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="profile.php">My Profile</a>
                    <a class="dropdown-item" href="settings.php">Settings</a>
                    <a class="dropdown-item" href="index.php">Logout</a>
                </div>
            </div>
        </div>

        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li><a href="dashboard.php"><img src="assets/img/home.svg" alt="sidebar_img" />
                                <span>Dashboard</span></a></li>
                        <li><a href="employee.php"><img src="assets/img/employee.svg" alt="sidebar_img" /><span>
                                    Employees</span></a></li>
                        <li><a href="company.php"><img src="assets/img/company.svg" alt="sidebar_img" /> <span>
                                    Company</span></a></li>
                        <li><a href="attendance.php"><img src="assets/img/calendar.svg" alt="sidebar_img" />
                                <span>Attendance</span></a></li>
                        <li class="active"><a href="payroll.php"><img src="assets/img/payroll.svg" alt="sidebar_img" />
                                <span>Payroll</span></a></li>
                        <li><a href="leave.php"><img src="assets/img/leave.svg" alt="sidebar_img" />
                                <span>Leave</span></a></li>
                        <li><a href="profile.php"><img src="assets/img/profile.svg" alt="sidebar_img" />
                                <span>Profile</span></a></li>
                    </ul>
                    <ul class="logout">
                        <li><a href="logout.php" onclick="return confirmLogout();"><img src="assets/img/logout.svg"
                                    alt="sidebar_img"><span>Logout</span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="col-xl-12 col-sm-12 col-12">
                    <div class="breadcrumb-path mb-4">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="dashboard.php"><img src="assets/img/dash.png" class="mr-2"
                                        alt="breadcrumb" />Home</a>
                            </li>
                            <li class="breadcrumb-item active">Payroll</li>
                        </ul>
                        <h3>Payroll</h3>
                    </div>
                </div>

                


                <div class="col-xl-12 col-sm-12 ">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Payroll Data</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Employee Name</th>
                                            <th>Department</th>
                                            <th>Basic Salary</th>
                                            <th>Deductions</th>
                                            <th>Net Pay</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($result) > 0): ?>
                                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['firstName'] . " " . $row['lastName']); ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($row['departmentName']); ?></td>
                                                    <td><?= htmlspecialchars($row['salaryGrade']); ?></td>
                                                    <td><?= htmlspecialchars($row['deductions']); ?></td>
                                                    <td><?= htmlspecialchars($row['net_pay']); ?></td>
                                                    <td>
                                                        <a href="view_payroll_details.php?employeeId=<?= $row['employeeId']; ?>"
                                                            class="btn btn-primary btn-sm" data-toggle="modal"
                                                            data-target="#viewPayrollModal<?= $row['employeeId']; ?>">View</a>
                                                        <!-- Include View Payroll Modal for each employee -->
                                                        <div id="viewPayrollModal<?= $row['employeeId']; ?>" class="modal fade"
                                                            tabindex="-1" role="dialog"
                                                            aria-labelledby="viewPayrollModalLabel<?= $row['employeeId']; ?>"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="viewPayrollModalLabel<?= $row['employeeId']; ?>">
                                                                            Payroll Details</h5>
                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                            aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p><strong>Employee:</strong>
                                                                            <?= htmlspecialchars($row['firstName'] . " " . $row['lastName']); ?>
                                                                        </p>
                                                                        <p><strong>Department:</strong>
                                                                            <?= htmlspecialchars($row['departmentName']); ?></p>
                                                                        <p><strong>Position:</strong>
                                                                            <?= htmlspecialchars($row['positionName']); ?></p>
                                                                        <p><strong>Basic Salary:</strong>
                                                                            <?= htmlspecialchars($row['salaryGrade']); ?></p>
                                                                        <p><strong>Bonus:</strong>
                                                                            <?= htmlspecialchars($row['bonus']); ?></p>
                                                                        <p><strong>Deductions:</strong>
                                                                            <?= htmlspecialchars($row['deductions']); ?></p>
                                                                        <p><strong>Net Pay:</strong>
                                                                            <?= htmlspecialchars($row['net_pay']); ?></p>
                                                                        <p><strong>Pay Date:</strong>
                                                                            <?= htmlspecialchars($row['pay_date']); ?></p>
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Close</button>
                                                                        
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6">No payroll data found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>




                <div id="addPayrollModal" class="modal fade" tabindex="-1" role="dialog"
                    aria-labelledby="addPayrollModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addPayrollModalLabel">Add Payroll</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="payroll.php">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="employee">Employee</label>
                                                <select id="employee" name="employee" class="form-control"
                                                    onchange="fetchEmployeeDetails(this.value)">
                                                    <?php
                                                    $employees = fetchEmployees($conn);
                                                    foreach ($employees as $employee) {
                                                        echo "<option value='" . $employee['employeeId'] . "'>" . $employee['fullName'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="department">Department</label>
                                                <input type="text" id="department" name="department"
                                                    class="form-control" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="position">Position</label>
                                                <input type="text" id="position" name="position" class="form-control"
                                                    readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="salary">Basic Salary</label>
                                                <input type="number" id="salary" name="salary" class="form-control"
                                                    readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="bonus">Bonus</label>
                                                <input type="number" id="bonus" name="bonus" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="deductions">Deductions</label>
                                                <input type="number" id="deductions" name="deductions"
                                                    class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="net_pay">Net Pay</label>
                                                <input type="number" id="net_pay" name="net_pay" class="form-control"
                                                    readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select id="status" name="status" class="form-control">
                                                    <option value="Paid">Paid</option>
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-auto">
                    <a href="#" class="btn btn-primary mr-1" data-toggle="modal" data-target="#addPayrollModal">
                        Add Payroll
                    </a>
                </div>
            </div>
        </div>
    </div>





    </div>
    </div>

    <script>
        $(document).ready(function) () {
            // Initialize datepicker for date fields
            $('.datetimepicker').datetimepicker({
                format: 'YYYY-MM-DD', // adjust the format as needed
            });

            // Fetch employee details when employee is selected
            $('#employee').on('change', function () {
                fetchEmployeeDetails($(this).val());
            });




            // Fetch employee details function
            function fetchEmployeeDetails(employeeId) {
                $.ajax({
                    url: 'payroll.php',
                    type: 'POST',
                    data: { employeeId: employeeId },
                    dataType: 'json',
                    success: function (response) {
                        $('#department').val(response.department);
                        $('#position').val(response.position);
                        $('#salary').val(response.salary);
                    }
                });
            }


    </script>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/select2.min.js"></script>
    <script src="assets/js/multiselect.min.js"></script>
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>

</html>