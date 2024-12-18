

INSERT INTO `car` (`car_id`, `car_brand`, `car_model`, `car_year`, `car_type`, `car_color`, `car_seats`, `car_transmission_type`, `car_fuel_type`, `car_rental_rate`, `car_excess_per_hour`, `car_availability`) VALUES
(1, 'Toyota', 'Corolla', 2022, 'Sedan', 'Red', 5, 'Automatic', 'Gasoline', 2000, 250, '1'),
(2, 'Honda', 'Civic', 2021, 'Sedan', 'Blue', 5, 'Manual', 'Gasoline', 1800, 350, '1'),
(3, 'BMW', 'X5', 2023, 'SUV', 'Black', 7, 'Automatic', 'Diesel', 4000, 0, '0'),
(4, 'Ford', 'Escape', 2022, 'SUV', 'White', 5, 'Automatic', 'Electric', 3500, 0, '1'),
(5, 'Audi', 'A4', 2021, 'Sedan', 'Silver', 5, 'Automatic', 'Gasoline', 2500, 0, '0'),
(6, 'Chevrolet', 'Tahoe', 2020, 'SUV', 'Gray', 7, 'Manual', 'Diesel', 3000, 0, '1'),
(7, 'Mercedes-Benz', 'C-Class', 2022, 'Sedan', 'White', 5, 'Automatic', 'Gasoline', 2800, 0, '1'),
(8, 'Tesla', 'Model X', 2023, 'SUV', 'Blue', 7, 'Automatic', 'Electric', 5000, 0, '0'),
(9, 'Nissan', 'Altima', 2021, 'Sedan', 'Green', 5, 'Manual', 'Gasoline', 2200, 0, '1'),
(10, 'Hyundai', 'Tucson', 2022, 'SUV', 'Yellow', 5, 'Automatic', 'Hybrid', 2800, 0, '1');


INSERT INTO `car_image` (`IMG_ID`, `CAR_ID`, `IMG_URL`, `IMG_DESCRIPTION`, `IMG_POSITION`, `IS_PRIMARY`, `IMG_UPLOADED_AT`) VALUES
(1, 1, 'car1.png', 'Front view', 1, 1, '2024-12-04 12:43:46'),
(2, 1, 'car2.png', 'Back view', 2, 0, '2024-12-04 12:43:46'),
(3, 1, 'car3.png', 'Side view left', 3, 0, '2024-12-04 12:43:46'),
(4, 1, 'car4.png', 'Side view right', 4, 0, '2024-12-04 12:43:46'),
(5, 1, 'car5.png', 'Interior view', 5, 0, '2024-12-04 12:43:46');



INSERT INTO `rental` (`rental_id`, `car_id`, `user_id`, `assigned_driver_id`, `is_custom_driver`, `custom_driver_name`, `custom_driver_phone`, `CUSTOM_DRIVER_LICENSE_NUMBER`, `rent_pickup_datetime`, `RENT_PICKUP_LOCATION`, `rent_dropoff_datetime`, `RENT_DROPOFF_LOCATION`, `RENTAL_STATUS`, `RENT_TOTAL_PRICE`, `rental_pax`) VALUES
(31, 1, 45, 201, 0, '', '', '', '2024-12-10 08:00:00', 'Cebu City', '2024-12-12 12:00:00', 'Mactan', 'pending', 2500, 4),
(32, 2, 42, 202, 1, 'John Doe', '09123456789', 'D123456789', '2024-12-11 09:30:00', 'Cebu City', '2024-12-13 18:00:00', 'Mandaue City', 'accepted', 2000, 2),
(33, 3, 43, 203, 0, '', '', '', '2024-12-15 07:00:00', 'Cebu City', '2024-12-17 10:00:00', 'Cebu City', 'accepted', 3000, 6),
(34, 4, 47, 204, 1, 'Jane Smith', '09234567890', 'A987654321', '2024-12-14 10:30:00', 'Lapu-Lapu', '2024-12-16 16:00:00', 'Lapu-Lapu', 'rejected', 2800, 4),
(35, 5, 46, 205, 0, '', '', '', '2024-12-10 13:00:00', 'Cebu City', '2024-12-12 15:00:00', 'Cebu City', 'pending', 2500, 3),
(36, 6, 48, 206, 1, 'Alice Johnson', '09345678901', 'B876543210', '2024-12-13 08:30:00', 'Talisay', '2024-12-15 11:45:00', 'Talisay', 'accepted', 2400, 2),
(37, 7, 107, 207, 0, '', '', '', '2024-12-12 06:00:00', 'Cebu City', '2024-12-14 20:00:00', 'Mactan', 'pending', 2600, 5),
(38, 8, 50, 208, 1, 'Bob Martin', '09456789012', 'C765432109', '2024-12-09 09:00:00', 'Lapu-Lapu', '2024-12-11 12:00:00', 'Cebu City', 'accepted', 2700, 4),
(39, 9, 51, 209, 0, '', '', '', '2024-12-14 08:00:00', 'Mandaue City', '2024-12-16 17:00:00', 'Cebu City', 'accepted', 3200, 7),
(40, 10, 52, 210, 1, 'Charlie Brown', '09567890123', 'D654321098', '2024-12-20 10:00:00', 'Cebu City', '2024-12-22 14:00:00', 'Mandaue City', 'pending', 3100, 6),
(41, 1, 45, 201, 0, 'N/A', '0000000000', 'N/A', '2024-12-10 08:00:00', 'Cebu City', '2024-12-12 12:00:00', 'Mactan', 'pending', 2500, 4),
(42, 2, 42, 202, 1, 'John Doe', '09123456789', 'D123456789', '2024-12-11 09:30:00', 'Cebu City', '2024-12-13 18:00:00', 'Mandaue City', 'accepted', 2000, 2),
(43, 3, 43, 203, 0, 'N/A', '0000000000', 'N/A', '2024-12-15 07:00:00', 'Cebu City', '2024-12-17 10:00:00', 'Cebu City', 'accepted', 3000, 6),
(44, 4, 47, 204, 1, 'Jane Smith', '09234567890', 'A987654321', '2024-12-14 10:30:00', 'Lapu-Lapu', '2024-12-16 16:00:00', 'Lapu-Lapu', 'rejected', 2800, 4),
(45, 5, 46, 205, 0, 'N/A', '0000000000', 'N/A', '2024-12-10 13:00:00', 'Cebu City', '2024-12-12 15:00:00', 'Cebu City', 'pending', 2500, 3),
(46, 6, 48, 206, 1, 'Alice Brown', '09345678901', 'C876543210', '2024-12-16 08:00:00', 'Mandaue City', '2024-12-18 10:00:00', 'Mandaue City', 'accepted', 3200, 5);



INSERT INTO `user` (`user_id`, `user_fname`, `user_mname`, `user_lname`, `user_email`, `user_password`, `user_phone`, `user_role`, `user_status`, `user_created_at`, `user_last_login_at`, `user_updated_at`) VALUES
(42, 'John', NULL, 'Doe', 'jeff@gmail.com', '$2y$10$HhFM/KRdeF0rjcUfAxIAfOFRsGmL', NULL, 'user', '1', NULL, NULL, NULL),
(43, '', NULL, '', '', '$2y$10$xiVwcAobbAtNA5CA6jZBg.2mLuyy', NULL, 'user', '1', NULL, NULL, NULL),
(44, 'dsdd', NULL, 'dsd', 'sdsds@ds.ds', '$2y$10$zjJXv3b3puiGKmrd1fR6HuUlxESA', NULL, 'user', '1', NULL, NULL, NULL),
(45, 'sasass', NULL, 'asasasa', 'shinta0x01@wearehackerone.com', '$2y$10$fqKmJVkLgcAXsKijvR0Kve5fJNlH', NULL, 'user', '1', NULL, NULL, NULL),
(46, 'test', NULL, 'fsdfdfdsfdsf', 'gonzxph@gmail.com', '$2y$10$BNiCUFf8lv00AHNAhdbwN.sVAgMZ', NULL, 'user', '1', NULL, NULL, NULL),
(47, 'ds', NULL, 'ssasas', 'test1@test.com', '$2y$10$w8YIsP/qg4xdGtt.2Fk5RegCQQ7L', NULL, 'user', '1', NULL, NULL, NULL),
(48, 'dasdsd', NULL, 'dada', 'x@x.x', '$2y$10$/MCU9IqDFHSHryfYAzLwDePwaxbevj.RZ67vgtJn90l4eQQsoTnpC', NULL, 'user', '1', NULL, NULL, NULL),
(49, 'dsd', NULL, 'dsdsd', 'shinta0x011@wearehackerone.com', '$2y$10$zQjb/M23nfdXH0Vmj1ubqeJCZGPg5PnnQEk0ronJJObdlE6wAeyPO', NULL, 'user', '1', NULL, NULL, NULL),
(50, 'fsf', NULL, 'sfdfdfd', 'shinta0x01@wearehackerone.com1', '$2y$10$RQIpcKworU87PRQ09NnGru.ogQdIlNCaVDZSBNS0DuRJ//nqHbPga', NULL, 'user', '1', NULL, NULL, NULL),
(51, 'asasas', NULL, '', 'shinta0x01@wearehackerone.com111', '$2y$10$JGdM7xol6mEvOHgIujkCL.6tKooMWDthzh2yAfrDaDeqguZhZ4XAq', NULL, 'user', '1', NULL, NULL, NULL),
(52, '', NULL, '', 'sasa@sdsd.dsd', '$2y$10$JJbjOVqd2NsDrOMg.M6VAO00aLbCLVwhD6AZnGgs0K7S7RdbgoWXG', NULL, 'user', '1', NULL, NULL, NULL),
(53, 'dsds', NULL, 'dsdsds', 'dsdsdsdsdd@dad.com', '$2y$10$m8fbttuEUYLn9bKBZoIFRusljFJ69z/UNOalaKU8o4NrwNE8Z7UdO', NULL, 'user', '1', NULL, NULL, NULL),
(54, 'asasas', NULL, 'asasasa', 'shinta0x01+69@wearehackerone.com', '$2y$10$cMY56W2UFJLRjPPD9MEn4eo5W/tNlhl4lxhRck2p30CvGSUks/2Da', NULL, 'user', '1', NULL, NULL, NULL),
(55, 'fdsf', NULL, 'fsfdsf', 'sfdfsd@fsfsd.com', '$2y$10$oYV09r/OlJI3UJRnUPzQ6eM.kA3XSXutHimUhJBC7di.Mm4VL7N02', NULL, 'user', '1', NULL, NULL, NULL);

