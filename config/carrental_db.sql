-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 04, 2025 at 09:31 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
-- Table structure for table `car`
--

CREATE TABLE `car` (
  `car_id` int(11) NOT NULL,
  `car_brand` varchar(35) NOT NULL,
  `car_model` varchar(35) NOT NULL,
  `car_year` int(4) NOT NULL,
  `car_type` varchar(15) NOT NULL,
  `car_color` varchar(15) NOT NULL,
  `car_seats` int(20) NOT NULL,
  `car_transmission_type` varchar(20) NOT NULL,
  `car_fuel_type` varchar(35) NOT NULL,
  `car_rental_rate` int(10) NOT NULL,
  `car_excess_per_hour` int(5) NOT NULL,
  `car_availability` enum('yes','no') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car`
--

INSERT INTO `car` (`car_id`, `car_brand`, `car_model`, `car_year`, `car_type`, `car_color`, `car_seats`, `car_transmission_type`, `car_fuel_type`, `car_rental_rate`, `car_excess_per_hour`, `car_availability`) VALUES
(1, 'Toyota', 'Vios', 2023, 'Sedan', 'White', 5, 'Automatic', 'Gasoline', 2500, 300, 'yes'),
(2, 'Honda', 'Civic', 2023, 'Sedan', 'Black', 5, 'Automatic', 'Gasoline', 3000, 350, 'yes'),
(3, 'Toyota', 'Fortuner', 2023, 'SUV', 'Silver', 7, 'Automatic', 'Diesel', 4500, 500, 'yes'),
(4, 'Mitsubishi', 'Xpander', 2022, 'MPV', 'Gray', 7, 'Automatic', 'Gasoline', 3500, 400, 'yes'),
(5, 'Nissan', 'Urvan', 2022, 'Van', 'White', 15, 'Manual', 'Diesel', 5000, 600, 'yes'),
(6, 'Suzuki', 'Ertiga', 2023, 'MPV', 'Red', 7, 'Automatic', 'Gasoline', 3000, 350, 'yes'),
(7, 'Toyota', 'Hiace', 2023, 'Van', 'Silver', 12, 'Manual', 'Diesel', 5500, 650, 'yes'),
(8, 'Honda', 'City', 2022, 'Sedan', 'Blue', 5, 'Automatic', 'Gasoline', 2800, 320, 'yes'),
(9, 'Hyundai', 'Starex', 2022, 'Van', 'Black', 10, 'Automatic', 'Diesel', 4800, 550, 'yes'),
(10, 'Toyota', 'Innova', 2023, 'MPV', 'White', 8, 'Automatic', 'Diesel', 4000, 450, 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `car_image`
--

CREATE TABLE `car_image` (
  `IMG_ID` bigint(20) UNSIGNED NOT NULL,
  `CAR_ID` int(11) NOT NULL,
  `IMG_URL` varchar(255) NOT NULL,
  `IMG_DESCRIPTION` text DEFAULT NULL,
  `IMG_POSITION` int(11) DEFAULT NULL,
  `IS_PRIMARY` tinyint(1) DEFAULT 0,
  `IMG_UPLOADED_AT` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `pay_id` int(11) NOT NULL,
  `rental_id` int(11) NOT NULL,
  `rp_id` int(11) DEFAULT NULL,
  `pay_type` varchar(35) NOT NULL,
  `pay_date` date NOT NULL,
  `pay_rental_charge` int(10) NOT NULL,
  `pay_pickup_charge` int(10) NOT NULL,
  `pay_dropoff_charge` int(10) NOT NULL,
  `pay_reservation_fee` int(10) NOT NULL,
  `pay_total_due` int(10) NOT NULL,
  `pay_amount_paid` int(10) NOT NULL,
  `pay_balance_due` int(10) NOT NULL,
  `pay_status` varchar(35) NOT NULL,
  `payment_reference` varchar(255) DEFAULT NULL
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
-- Table structure for table `rental`
--

CREATE TABLE `rental` (
  `rental_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assigned_driver_id` int(11) DEFAULT NULL,
  `is_custom_driver` int(11) DEFAULT NULL,
  `custom_driver_name` varchar(35) DEFAULT NULL,
  `custom_driver_phone` varchar(15) DEFAULT NULL,
  `CUSTOM_DRIVER_LICENSE_NUMBER` varchar(20) DEFAULT NULL,
  `rent_pickup_datetime` datetime NOT NULL,
  `RENT_PICKUP_LOCATION` varchar(50) NOT NULL,
  `rent_dropoff_datetime` datetime NOT NULL,
  `RENT_DROPOFF_LOCATION` varchar(50) NOT NULL,
  `RENTAL_STATUS` enum('rejected','accepted','pending') NOT NULL DEFAULT 'pending',
  `RENT_TOTAL_PRICE` int(10) NOT NULL,
  `rental_pax` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rent_penalty`
--

CREATE TABLE `rent_penalty` (
  `rp_id` int(11) NOT NULL,
  `rental_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_fname` varchar(35) NOT NULL,
  `user_mname` varchar(35) DEFAULT NULL,
  `user_lname` varchar(35) NOT NULL,
  `user_email` varchar(35) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_phone` varchar(15) DEFAULT NULL,
  `user_role` enum('user','admin','agent','driver') NOT NULL DEFAULT 'user',
  `user_status` enum('1','0') NOT NULL DEFAULT '1',
  `user_created_at` timestamp NULL DEFAULT NULL,
  `user_last_login_at` timestamp NULL DEFAULT NULL,
  `user_updated_at` timestamp NULL DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT 'images/profile/user.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_fname`, `user_mname`, `user_lname`, `user_email`, `user_password`, `user_phone`, `user_role`, `user_status`, `user_created_at`, `user_last_login_at`, `user_updated_at`, `profile_image`) VALUES
(1, 'Kent Vincent', NULL, 'De La Concepcion', 'kentivicente@gmail.com', '$2y$12$NCtwITYazdHKyia4bVMKjeopcAmzdYWsYYfvBDIC9k5u.4.bEV4Dm', NULL, 'user', '1', '2024-12-18 19:27:14', '2025-01-02 17:06:50', NULL, 'images/profile/user.png'),
(2, 'John', '', 'Doe', 'jando@gmail.com', '$2y$12$BC5HGz/mxtQenaaaE78Rc.EbDFPIor1.1cqhuTigJlIOefRPhgLq2', '09082885329', 'user', '1', '2025-01-02 04:19:07', '2025-01-04 20:29:11', NULL, 'images/profile/677995f5a5837_Screenshot 2024-12-27 232909.png'),
(3, 'Simon', '', 'Riley', 'max@g.com', '$2y$12$lfk5tRwmh04qG7bqucUb2.G8NaYGAp36h.E2iIwts9D2uGy2ywaTq', '09082885329', 'user', '1', '2025-01-02 20:22:14', '2025-01-04 20:11:03', NULL, 'images/profile/677991b577a65_Screenshot 2024-12-27 004004.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `car`
--
ALTER TABLE `car`
  ADD PRIMARY KEY (`car_id`);

--
-- Indexes for table `car_image`
--
ALTER TABLE `car_image`
  ADD PRIMARY KEY (`IMG_ID`),
  ADD KEY `CAR_ID` (`CAR_ID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`pay_id`),
  ADD KEY `rental_id` (`rental_id`),
  ADD KEY `rp_id` (`rp_id`);

--
-- Indexes for table `pending_payments`
--
ALTER TABLE `pending_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rental`
--
ALTER TABLE `rental`
  ADD PRIMARY KEY (`rental_id`),
  ADD KEY `car_id` (`car_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rent_penalty`
--
ALTER TABLE `rent_penalty`
  ADD PRIMARY KEY (`rp_id`),
  ADD KEY `rental_id` (`rental_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `car`
--
ALTER TABLE `car`
  MODIFY `car_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `car_image`
--
ALTER TABLE `car_image`
  MODIFY `IMG_ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `pay_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pending_payments`
--
ALTER TABLE `pending_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rental`
--
ALTER TABLE `rental`
  MODIFY `rental_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rent_penalty`
--
ALTER TABLE `rent_penalty`
  MODIFY `rp_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `car_image`
--
ALTER TABLE `car_image`
  ADD CONSTRAINT `car_image_ibfk_1` FOREIGN KEY (`CAR_ID`) REFERENCES `car` (`car_id`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`rental_id`) REFERENCES `rental` (`rental_id`),
  ADD CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`rp_id`) REFERENCES `rent_penalty` (`rp_id`);

--
-- Constraints for table `rental`
--
ALTER TABLE `rental`
  ADD CONSTRAINT `car_id` FOREIGN KEY (`car_id`) REFERENCES `car` (`car_id`),
  ADD CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `rent_penalty`
--
ALTER TABLE `rent_penalty`
  ADD CONSTRAINT `rent_penalty_ibfk_1` FOREIGN KEY (`rental_id`) REFERENCES `rental` (`rental_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
