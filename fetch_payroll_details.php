<?php
include 'connect.php';

if (isset($_POST['employeeId'])) {
   $employeeId = $_POST['employeeId'];

   $query = "SELECT 
            e.firstName, 
            e.lastName, 
            d.departmentName, 
            p.positionName, 
            p.salaryGrade, 
            py.bonus, 
            py.deductions, 
            py.net_pay, 
            py.status, 
            py.pay_date 
          FROM 
            employee e 
          INNER JOIN department d ON e.departmentId = d.departmentId 
          INNER JOIN position p ON e.positionId = p.positionId 
          INNER JOIN payroll py ON e.employeeId = py.employeeId 
          WHERE 
            e.employeeId = '$employeeId'";

   $result = mysqli_query($conn, $query);

   if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
      $data = array(
         'fullName' => $row['firstName'] . ' ' . $row['lastName'],
         'department' => $row['departmentName'],
         'position' => $row['positionName'],
         'salary' => $row['salaryGrade'],
         'bonus' => $row['bonus'],
         'deductions' => $row['deductions'],
         'net_pay' => $row['net_pay'],
         'status' => $row['status'],
         'pay_date' => $row['pay_date']
      );
      echo json_encode($data);
   } else {
      echo json_encode(array('error' => 'No data found'));
   }
}
?>
