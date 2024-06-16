-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 16, 2024 at 11:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_hrms`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendanceId` int(11) NOT NULL,
  `employeeId` int(11) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `status` varchar(255) NOT NULL,
  `checkInTime` time NOT NULL,
  `checkOutTime` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendanceId`, `employeeId`, `userId`, `date`, `status`, `checkInTime`, `checkOutTime`) VALUES
(15, 3, 4, '2024-06-13', 'Present', '20:12:18', '20:12:23'),
(16, 4, 5, '2024-06-13', 'Absent', '00:00:00', '00:00:00'),
(17, 11, 17, '2024-06-13', 'On Going Leave', '00:00:00', '00:00:00'),
(18, 12, 18, '2024-06-13', 'Present', '22:22:56', '22:22:58'),
(19, 13, 20, '2024-06-14', 'Present', '07:31:44', '07:31:47'),
(20, 3, 4, '2024-06-14', 'Present', '09:42:22', '00:00:00'),
(21, 4, 5, '2024-06-14', 'Present', '07:32:26', '07:32:28'),
(22, 12, 18, '2024-06-14', 'Absent', '00:00:00', '00:00:00'),
(23, 11, 17, '2024-06-14', 'On Going Leave', '00:00:00', '00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `departmentId` int(11) NOT NULL,
  `departmentName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`departmentId`, `departmentName`) VALUES
(4, 'IT Department'),
(5, 'Finance Department'),
(6, 'HR Department'),
(8, 'Sales Department'),
(9, 'Sales Department'),
(10, 'Logistic Department');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `employeeId` int(11) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `positionId` int(11) NOT NULL,
  `departmentId` int(11) NOT NULL,
  `dateOfHire` date NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`employeeId`, `firstName`, `lastName`, `email`, `positionId`, `departmentId`, `dateOfHire`, `status`) VALUES
(3, 'Ruiz', 'Sapio', 'ruizsapio@gmail.com', 5, 4, '2024-06-11', ''),
(4, 'Jerie', 'Arocena', 'jarocena@gmail.com', 5, 4, '2024-06-11', ''),
(10, 'Audrey', 'Alinea', 'audrey@gmail.com', 9, 4, '2024-06-14', ''),
(11, 'Charles', 'Leonardo', 'charles@gmail.com', 8, 5, '2024-06-14', ''),
(12, 'Elena', 'Cortez', 'elena@gmail.com', 7, 6, '2024-06-14', ''),
(13, 'Zoltan', 'Gutierrez', 'zgut@gmail.com', 7, 8, '2024-06-15', ''),
(14, 'Amiel ', 'Dioyo', 'amiel@gmail.com', 8, 5, '2024-06-15', '');

-- --------------------------------------------------------

--
-- Table structure for table `employee_leave`
--

CREATE TABLE `employee_leave` (
  `leaveId` int(11) NOT NULL,
  `employeeId` int(11) DEFAULT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `leaveType` varchar(255) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_leave`
--

INSERT INTO `employee_leave` (`leaveId`, `employeeId`, `firstName`, `lastName`, `leaveType`, `startDate`, `endDate`, `status`) VALUES
(4, 3, 'Ruiz', 'Sapio', 'Sick leave', '2024-06-11', '2024-06-15', 'Approved'),
(6, 3, 'Ruiz', 'Sapio', 'Parental leave', '2024-06-13', '2024-06-30', 'Disapproved'),
(7, 3, 'Ruiz\r\n', 'Sapio', 'Family and Medical leave', '2024-06-14', '2024-06-30', 'Approved'),
(8, 10, 'Audrey', 'Alinea', 'Public Holidays', '2024-06-14', '2024-06-30', 'Pending'),
(9, 13, 'Zoltan', 'Gutierrez', 'Vacation leave', '2024-06-15', '2024-06-23', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `payrollId` int(11) NOT NULL,
  `employeeId` int(11) NOT NULL,
  `positionId` int(11) NOT NULL,
  `departmentId` int(11) NOT NULL,
  `salary` decimal(10,2) NOT NULL,
  `bonus` decimal(10,2) NOT NULL,
  `deductions` decimal(10,2) NOT NULL,
  `net_pay` decimal(10,2) GENERATED ALWAYS AS (`salary` + `bonus` - `deductions`) VIRTUAL,
  `pay_date` date DEFAULT curdate(),
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`payrollId`, `employeeId`, `positionId`, `departmentId`, `salary`, `bonus`, `deductions`, `pay_date`, `status`) VALUES
(7, 3, 5, 4, 10000.00, 1000.00, 5000.00, '2024-06-10', ''),
(8, 4, 5, 4, 10000.00, 20000.00, 10000.00, '2024-06-10', ''),
(11, 10, 9, 4, 35000.00, 10000.00, 100.00, '2024-06-14', 'Pending'),
(12, 13, 7, 8, 5000.00, 10000.00, 5000.00, '2024-06-14', '');

-- --------------------------------------------------------

--
-- Table structure for table `position`
--

CREATE TABLE `position` (
  `positionId` int(11) NOT NULL,
  `positionName` varchar(255) NOT NULL,
  `salaryGrade` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `position`
--

INSERT INTO `position` (`positionId`, `positionName`, `salaryGrade`) VALUES
(5, 'Head', 10000.00),
(7, 'Intern', 5000.00),
(8, 'Full-time', 2000.00),
(9, 'Junior Programmer', 35000.00),
(10, 'Part-time', 20000.00);

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `tasksId` int(11) NOT NULL,
  `employeeId` int(11) DEFAULT NULL,
  `task` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userId` int(11) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `roles` varchar(255) NOT NULL,
  `employeeId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userId`, `firstName`, `lastName`, `email`, `password`, `roles`, `employeeId`) VALUES
(4, 'Ruiz\r\n', 'Sapio', 'ruizsapio@gmail.com', '123456', 'Employee', 3),
(5, 'Jerie', 'Arocena', 'jarocena@gmail.com', 'employee', 'Employee', 4),
(17, 'Charles', 'Leonardo', 'charles@gmail.com', 'employee', 'Employee', 11),
(18, 'Elena', 'Cortez', 'elena@gmail.com', 'employee', 'Employee', 12),
(19, 'Von', 'Monfero', 'monferovon@gmail.com', 'admin', 'admin', NULL),
(20, 'Zoltan', 'Gutierrez', 'zgut@gmail.com', 'employee', 'Employee', 13),
(21, 'Russel', 'Monfero', 'russel@gmail.com', 'admin', 'admin', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendanceId`),
  ADD KEY `fk_employee_attendance` (`employeeId`),
  ADD KEY `fk_user_attendance` (`userId`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`departmentId`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`employeeId`),
  ADD KEY `fk_department_id` (`departmentId`),
  ADD KEY `fk_position_id` (`positionId`);

--
-- Indexes for table `employee_leave`
--
ALTER TABLE `employee_leave`
  ADD PRIMARY KEY (`leaveId`),
  ADD KEY `fk_employee_leave` (`employeeId`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`payrollId`),
  ADD KEY `fk_employee_payroll` (`employeeId`),
  ADD KEY `fk_position_payroll` (`positionId`),
  ADD KEY `fk_department_payroll` (`departmentId`);

--
-- Indexes for table `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`positionId`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`tasksId`),
  ADD KEY `fk_employee_task` (`employeeId`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userId`),
  ADD KEY `fk_user_employee` (`employeeId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendanceId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `departmentId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `employeeId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `employee_leave`
--
ALTER TABLE `employee_leave`
  MODIFY `leaveId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `payrollId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `position`
--
ALTER TABLE `position`
  MODIFY `positionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `tasksId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk_employee_attendance` FOREIGN KEY (`employeeId`) REFERENCES `employee` (`employeeId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_attendance` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `fk_department_id` FOREIGN KEY (`departmentId`) REFERENCES `department` (`departmentId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_position_id` FOREIGN KEY (`positionId`) REFERENCES `position` (`positionId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employee_leave`
--
ALTER TABLE `employee_leave`
  ADD CONSTRAINT `fk_employee_leave` FOREIGN KEY (`employeeId`) REFERENCES `employee` (`employeeId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `fk_department_payroll` FOREIGN KEY (`departmentId`) REFERENCES `department` (`departmentId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_employee_payroll` FOREIGN KEY (`employeeId`) REFERENCES `employee` (`employeeId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_position_payroll` FOREIGN KEY (`positionId`) REFERENCES `position` (`positionId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_employee_task` FOREIGN KEY (`employeeId`) REFERENCES `employee` (`employeeId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_employee` FOREIGN KEY (`employeeId`) REFERENCES `employee` (`employeeId`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
