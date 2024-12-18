-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 18, 2024 at 06:17 AM
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
-- Database: `carrental_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `ADDRESS_ID` int(11) NOT NULL,
  `USER_ID` int(11) DEFAULT NULL,
  `ADDRESS_TYPE` varchar(50) DEFAULT NULL,
  `ADDRESS_REGION` varchar(50) DEFAULT NULL,
  `ADDRESS_PROVINCE` varchar(50) DEFAULT NULL,
  `ADDRESS_CITY` varchar(50) DEFAULT NULL,
  `ADDRESS_BARANGAY` varchar(50) DEFAULT NULL,
  `ADDRESS_STREET` varchar(100) DEFAULT NULL,
  `ADDRESS_ZIPCODE` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `agent_status`
--

CREATE TABLE `agent_status` (
  `LOGIN_ID` int(11) NOT NULL,
  `LOGIN_DATE` timestamp NOT NULL DEFAULT current_timestamp(),
  `USER_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `car`
--

CREATE TABLE `car` (
  `CAR_ID` int(11) NOT NULL,
  `CAR_DESCRIPTION` text DEFAULT NULL,
  `CAR_BRAND` varchar(50) DEFAULT NULL,
  `CAR_MODEL` varchar(50) DEFAULT NULL,
  `CAR_YEAR` year(4) DEFAULT NULL,
  `CAR_TYPE` varchar(50) DEFAULT NULL,
  `CAR_COLOR` varchar(30) DEFAULT NULL,
  `CAR_LICENSE_PLATE` varchar(20) DEFAULT NULL,
  `CAR_VIN` varchar(50) DEFAULT NULL,
  `CAR_SEATS` int(11) DEFAULT NULL,
  `CAR_TRANSMISSION_TYPE` varchar(20) DEFAULT NULL,
  `CAR_FUEL_TYPE` varchar(30) DEFAULT NULL,
  `CAR_RENTAL_RATE` decimal(10,2) DEFAULT NULL,
  `CAR_AVAILABILITY` tinyint(1) DEFAULT 1,
  `CAR_CREATED_AT` timestamp NOT NULL DEFAULT current_timestamp(),
  `CAR_UPDATED_AT` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `CAR_EXCESS_PER_HOUR` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car`
--

INSERT INTO `car` (`CAR_ID`, `CAR_DESCRIPTION`, `CAR_BRAND`, `CAR_MODEL`, `CAR_YEAR`, `CAR_TYPE`, `CAR_COLOR`, `CAR_LICENSE_PLATE`, `CAR_VIN`, `CAR_SEATS`, `CAR_TRANSMISSION_TYPE`, `CAR_FUEL_TYPE`, `CAR_RENTAL_RATE`, `CAR_AVAILABILITY`, `CAR_CREATED_AT`, `CAR_UPDATED_AT`, `CAR_EXCESS_PER_HOUR`) VALUES
(1, NULL, 'Toyota', 'Corolla', '2022', 'Sedan', 'Red', NULL, NULL, 5, 'Automatic', 'Gasoline', 2000.00, 1, '2024-12-17 09:19:24', '2024-12-17 09:19:24', 250.00),
(2, NULL, 'Honda', 'Civic', '2021', 'Sedan', 'Blue', NULL, NULL, 5, 'Manual', 'Gasoline', 1800.00, 1, '2024-12-17 09:19:24', '2024-12-17 09:19:24', 350.00),
(3, NULL, 'BMW', 'X5', '2023', 'SUV', 'Black', NULL, NULL, 7, 'Automatic', 'Diesel', 4000.00, 0, '2024-12-17 09:19:24', '2024-12-17 09:19:24', 0.00),
(4, NULL, 'Ford', 'Escape', '2022', 'SUV', 'White', NULL, NULL, 5, 'Automatic', 'Electric', 3500.00, 1, '2024-12-17 09:19:24', '2024-12-17 09:19:24', 0.00),
(5, NULL, 'Audi', 'A4', '2021', 'Sedan', 'Silver', NULL, NULL, 5, 'Automatic', 'Gasoline', 2500.00, 0, '2024-12-17 09:19:24', '2024-12-17 09:19:24', 0.00),
(6, NULL, 'Chevrolet', 'Tahoe', '2020', 'SUV', 'Gray', NULL, NULL, 7, 'Manual', 'Diesel', 3000.00, 1, '2024-12-17 09:19:24', '2024-12-17 09:19:24', 0.00),
(7, NULL, 'Mercedes-Benz', 'C-Class', '2022', 'Sedan', 'White', NULL, NULL, 5, 'Automatic', 'Gasoline', 2800.00, 1, '2024-12-17 09:19:24', '2024-12-17 09:19:24', 0.00),
(8, NULL, 'Tesla', 'Model X', '2023', 'SUV', 'Blue', NULL, NULL, 7, 'Automatic', 'Electric', 5000.00, 0, '2024-12-17 09:19:24', '2024-12-17 09:19:24', 0.00),
(9, NULL, 'Nissan', 'Altima', '2021', 'Sedan', 'Green', NULL, NULL, 5, 'Manual', 'Gasoline', 2200.00, 1, '2024-12-17 09:19:24', '2024-12-17 09:19:24', 0.00),
(10, NULL, 'Hyundai', 'Tucson', '2022', 'SUV', 'Yellow', NULL, NULL, 5, 'Automatic', 'Hybrid', 2800.00, 1, '2024-12-17 09:19:24', '2024-12-17 09:19:24', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `car_image`
--

CREATE TABLE `car_image` (
  `IMG_ID` int(11) NOT NULL,
  `CAR_ID` int(11) DEFAULT NULL,
  `IMG_URL` varchar(255) DEFAULT NULL,
  `IMG_DESCRIPTION` text DEFAULT NULL,
  `IMG_POSITION` int(11) DEFAULT NULL,
  `IS_PRIMARY` tinyint(1) DEFAULT 0,
  `IMG_UPLOADED_AT` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `driver`
--

CREATE TABLE `driver` (
  `DRIVER_ID` int(11) NOT NULL,
  `DRIVER_NAME` varchar(100) NOT NULL,
  `DRIVER_PHONE` varchar(15) DEFAULT NULL,
  `DRIVER_LICENSE_NUMBER` varchar(50) DEFAULT NULL,
  `DRIVER_AVAILABILITY` tinyint(1) DEFAULT 1,
  `DRIVER_CREATED_AT` timestamp NOT NULL DEFAULT current_timestamp(),
  `DRIVER_UPDATED_AT` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_history`
--

CREATE TABLE `login_history` (
  `LOGIN_ID` int(11) NOT NULL,
  `LOGIN_DATE` timestamp NOT NULL DEFAULT current_timestamp(),
  `USER_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `PAY_ID` int(11) NOT NULL,
  `RENTAL_ID` int(11) DEFAULT NULL,
  `RP_ID` int(11) DEFAULT NULL,
  `PAY_TYPE` varchar(50) DEFAULT NULL,
  `PAY_METHOD` varchar(50) DEFAULT NULL,
  `PAY_DATE` timestamp NOT NULL DEFAULT current_timestamp(),
  `PAY_RENTAL_CHARGE` decimal(10,2) DEFAULT NULL,
  `PAY_PICKUP_CHARGE` decimal(10,2) DEFAULT NULL,
  `PAY_DROPOFF_CHARGE` decimal(10,2) DEFAULT NULL,
  `PAY_RESERVATION_FEE` decimal(10,2) DEFAULT NULL,
  `PAY_TOTAL_DUE` decimal(10,2) DEFAULT NULL,
  `PAY_AMOUNT_PAID` decimal(10,2) DEFAULT NULL,
  `PAY_BALANCE_DUE` decimal(10,2) DEFAULT NULL,
  `PAY_STATUS` varchar(20) DEFAULT NULL,
  `PAYMENT_REFERENCE` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pending_payments`
--

CREATE TABLE `pending_payments` (
  `id` int(11) NOT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `car_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `pickup_date` datetime DEFAULT NULL,
  `pickup_address` text DEFAULT NULL,
  `return_date` datetime DEFAULT NULL,
  `return_address` text DEFAULT NULL,
  `payment_option` varchar(50) DEFAULT NULL,
  `vehicle_rate` decimal(10,2) DEFAULT NULL,
  `delivery_fee` decimal(10,2) DEFAULT NULL,
  `return_fee` decimal(10,2) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receipt`
--

CREATE TABLE `receipt` (
  `RECEIPT_ID` int(11) NOT NULL,
  `PAY_ID` int(11) DEFAULT NULL,
  `RECEIPT_DATE` timestamp NOT NULL DEFAULT current_timestamp(),
  `RECEIPT_AMOUNT_PAID` decimal(10,2) DEFAULT NULL,
  `RECEIPT_BALANCE_DUE` decimal(10,2) DEFAULT NULL,
  `PAY_STATUS` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rental`
--

CREATE TABLE `rental` (
  `RENTAL_ID` int(11) NOT NULL,
  `CAR_ID` int(11) DEFAULT NULL,
  `USER_ID` int(11) DEFAULT NULL,
  `ASSIGNED_DRIVER_ID` int(11) DEFAULT NULL,
  `IS_CUSTOM_DRIVER` tinyint(1) DEFAULT 0,
  `CUSTOM_DRIVER_NAME` varchar(100) DEFAULT NULL,
  `CUSTOM_DRIVER_PHONE` varchar(15) DEFAULT NULL,
  `CUSTOM_DRIVER_LICENSE_NUMBER` varchar(50) DEFAULT NULL,
  `RENT_PICKUP_DATETIME` datetime DEFAULT NULL,
  `RENT_PICKUP_LOCATION` varchar(255) DEFAULT NULL,
  `RENT_DROPOFF_DATETIME` datetime DEFAULT NULL,
  `RENT_DROPOFF_LOCATION` varchar(255) DEFAULT NULL,
  `RENTAL_STATUS` varchar(20) DEFAULT NULL,
  `RENT_TOTAL_PRICE` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rent_penalty`
--

CREATE TABLE `rent_penalty` (
  `RP_ID` int(11) NOT NULL,
  `RP_1` decimal(10,2) DEFAULT NULL,
  `RP_2` decimal(10,2) DEFAULT NULL,
  `RP_3` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `USER_ID` int(11) NOT NULL,
  `USER_FNAME` varchar(50) NOT NULL,
  `USER_MNAME` varchar(50) DEFAULT NULL,
  `USER_LNAME` varchar(50) NOT NULL,
  `USER_EMAIL` varchar(100) NOT NULL,
  `USER_PASSWORD` varchar(255) NOT NULL,
  `USER_PHONE` varchar(15) DEFAULT NULL,
  `USER_ROLE` varchar(20) DEFAULT 'CUSTOMER',
  `USER_STATUS` varchar(20) DEFAULT 'ACTIVE',
  `USER_CREATED_AT` timestamp NOT NULL DEFAULT current_timestamp(),
  `USER_IS_ONLINE` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`USER_ID`, `USER_FNAME`, `USER_MNAME`, `USER_LNAME`, `USER_EMAIL`, `USER_PASSWORD`, `USER_PHONE`, `USER_ROLE`, `USER_STATUS`, `USER_CREATED_AT`, `USER_IS_ONLINE`) VALUES
(2, 'vince', NULL, 'delaconcepcion', 'vince@gmail.com', '$2y$12$tY9lFTk/kpozIkemv32fFOY48Tdei7E0mvTS9FcBWjQca9NtWh7eO', NULL, 'CUSTOMER', 'ACTIVE', '2024-12-17 09:39:18', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_update_history`
--

CREATE TABLE `user_update_history` (
  `UPDATE_ID` int(11) NOT NULL,
  `UPDATE_DATE` timestamp NOT NULL DEFAULT current_timestamp(),
  `USER_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`ADDRESS_ID`),
  ADD KEY `USER_ID` (`USER_ID`);

--
-- Indexes for table `agent_status`
--
ALTER TABLE `agent_status`
  ADD PRIMARY KEY (`LOGIN_ID`),
  ADD KEY `USER_ID` (`USER_ID`);

--
-- Indexes for table `car`
--
ALTER TABLE `car`
  ADD PRIMARY KEY (`CAR_ID`);

--
-- Indexes for table `car_image`
--
ALTER TABLE `car_image`
  ADD PRIMARY KEY (`IMG_ID`),
  ADD KEY `CAR_ID` (`CAR_ID`);

--
-- Indexes for table `driver`
--
ALTER TABLE `driver`
  ADD PRIMARY KEY (`DRIVER_ID`);

--
-- Indexes for table `login_history`
--
ALTER TABLE `login_history`
  ADD PRIMARY KEY (`LOGIN_ID`),
  ADD KEY `USER_ID` (`USER_ID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`PAY_ID`),
  ADD KEY `RENTAL_ID` (`RENTAL_ID`),
  ADD KEY `RP_ID` (`RP_ID`);

--
-- Indexes for table `receipt`
--
ALTER TABLE `receipt`
  ADD PRIMARY KEY (`RECEIPT_ID`),
  ADD KEY `PAY_ID` (`PAY_ID`);

--
-- Indexes for table `rental`
--
ALTER TABLE `rental`
  ADD PRIMARY KEY (`RENTAL_ID`),
  ADD KEY `CAR_ID` (`CAR_ID`),
  ADD KEY `USER_ID` (`USER_ID`),
  ADD KEY `ASSIGNED_DRIVER_ID` (`ASSIGNED_DRIVER_ID`);

--
-- Indexes for table `rent_penalty`
--
ALTER TABLE `rent_penalty`
  ADD PRIMARY KEY (`RP_ID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`USER_ID`),
  ADD UNIQUE KEY `USER_EMAIL` (`USER_EMAIL`);

--
-- Indexes for table `user_update_history`
--
ALTER TABLE `user_update_history`
  ADD PRIMARY KEY (`UPDATE_ID`),
  ADD KEY `USER_ID` (`USER_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `ADDRESS_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `agent_status`
--
ALTER TABLE `agent_status`
  MODIFY `LOGIN_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `car`
--
ALTER TABLE `car`
  MODIFY `CAR_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `car_image`
--
ALTER TABLE `car_image`
  MODIFY `IMG_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `driver`
--
ALTER TABLE `driver`
  MODIFY `DRIVER_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_history`
--
ALTER TABLE `login_history`
  MODIFY `LOGIN_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `PAY_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receipt`
--
ALTER TABLE `receipt`
  MODIFY `RECEIPT_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rental`
--
ALTER TABLE `rental`
  MODIFY `RENTAL_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rent_penalty`
--
ALTER TABLE `rent_penalty`
  MODIFY `RP_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `USER_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_update_history`
--
ALTER TABLE `user_update_history`
  MODIFY `UPDATE_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `address_ibfk_1` FOREIGN KEY (`USER_ID`) REFERENCES `user` (`USER_ID`);

--
-- Constraints for table `agent_status`
--
ALTER TABLE `agent_status`
  ADD CONSTRAINT `agent_status_ibfk_1` FOREIGN KEY (`USER_ID`) REFERENCES `user` (`USER_ID`);

--
-- Constraints for table `car_image`
--
ALTER TABLE `car_image`
  ADD CONSTRAINT `car_image_ibfk_1` FOREIGN KEY (`CAR_ID`) REFERENCES `car` (`CAR_ID`);

--
-- Constraints for table `login_history`
--
ALTER TABLE `login_history`
  ADD CONSTRAINT `login_history_ibfk_1` FOREIGN KEY (`USER_ID`) REFERENCES `user` (`USER_ID`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`RENTAL_ID`) REFERENCES `rental` (`RENTAL_ID`),
  ADD CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`RP_ID`) REFERENCES `rent_penalty` (`RP_ID`);

--
-- Constraints for table `receipt`
--
ALTER TABLE `receipt`
  ADD CONSTRAINT `receipt_ibfk_1` FOREIGN KEY (`PAY_ID`) REFERENCES `payment` (`PAY_ID`);

--
-- Constraints for table `rental`
--
ALTER TABLE `rental`
  ADD CONSTRAINT `rental_ibfk_1` FOREIGN KEY (`CAR_ID`) REFERENCES `car` (`CAR_ID`),
  ADD CONSTRAINT `rental_ibfk_2` FOREIGN KEY (`USER_ID`) REFERENCES `user` (`USER_ID`),
  ADD CONSTRAINT `rental_ibfk_3` FOREIGN KEY (`ASSIGNED_DRIVER_ID`) REFERENCES `driver` (`DRIVER_ID`);

--
-- Constraints for table `user_update_history`
--
ALTER TABLE `user_update_history`
  ADD CONSTRAINT `user_update_history_ibfk_1` FOREIGN KEY (`USER_ID`) REFERENCES `user` (`USER_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
