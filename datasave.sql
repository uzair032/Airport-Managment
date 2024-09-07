-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 06, 2024 at 12:50 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `datasave`
--

-- --------------------------------------------------------

--
-- Table structure for table `airline_data`
--

CREATE TABLE `airline_data` (
  `Airline_id` int(11) NOT NULL,
  `Airline_name` varchar(30) NOT NULL,
  `Air_Price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `airline_data`
--

INSERT INTO `airline_data` (`Airline_id`, `Airline_name`, `Air_Price`) VALUES
(1, 'Qatar_airways', 100000),
(2, 'PIA', 36000),
(3, 'Air_canada', 75000),
(4, 'Turkish_airlines', 45000),
(5, 'Emirates', 120000),
(6, 'Saudi_airline', 90000),
(16, 'AirBlue', 250000),
(19, 'Japan_Airlines', 345908);

-- --------------------------------------------------------

--
-- Table structure for table `airport_t`
--

CREATE TABLE `airport_t` (
  `Airport_ID` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `Airport_name` varchar(25) NOT NULL,
  `Dest` varchar(25) NOT NULL,
  `Airline` varchar(50) NOT NULL,
  `InsertedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `checkout`
-- (See below for the actual view)
--
CREATE TABLE `checkout` (
`user_id` int(11)
,`Firstname` varchar(25)
,`Lastname` varchar(25)
,`CNIC` varchar(15)
,`Airport_name` varchar(25)
,`Dest` varchar(25)
,`Airline` varchar(50)
,`Flight_ID` int(11)
,`deperaturedate` datetime
,`arrivaldate` datetime
,`Payment_ID` int(11)
,`Paymentmethod` varchar(25)
,`Ticket_ID` int(11)
,`Total_price` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `city_data`
--

CREATE TABLE `city_data` (
  `City_id` int(11) NOT NULL,
  `Destination` varchar(30) NOT NULL,
  `Dest_Price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `city_data`
--

INSERT INTO `city_data` (`City_id`, `Destination`, `Dest_Price`) VALUES
(1, 'Barcelona', 110431),
(2, 'Dubai', 77259),
(3, 'Rome', 186016),
(4, 'Bali', 287076),
(5, 'Sydney', 332492),
(6, 'Ireland', 433240);

-- --------------------------------------------------------

--
-- Table structure for table `flight_t`
--

CREATE TABLE `flight_t` (
  `Flight_ID` int(11) NOT NULL,
  `Airport_ID` int(11) NOT NULL,
  `deperaturedate` datetime NOT NULL,
  `arrivaldate` datetime NOT NULL,
  `seat` varchar(50) NOT NULL,
  `InsertedAt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_method`
--

CREATE TABLE `payment_method` (
  `Payment_ID` int(11) NOT NULL,
  `Payment_name` varchar(25) NOT NULL,
  `Payment_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_method`
--

INSERT INTO `payment_method` (`Payment_ID`, `Payment_name`, `Payment_price`) VALUES
(61, 'Visa', 20000.00),
(62, 'Master_card', 18000.00),
(63, 'Paypal', 21000.00),
(64, 'Bank_transfer', 10000.00),
(65, 'Credit_card', 13000.00);

-- --------------------------------------------------------

--
-- Table structure for table `payment_t`
--

CREATE TABLE `payment_t` (
  `Payment_ID` int(11) NOT NULL,
  `Flight_ID` int(11) NOT NULL,
  `Airport_ID` int(11) NOT NULL,
  `Paymentmethod` varchar(25) NOT NULL,
  `Cardholder` varchar(35) NOT NULL,
  `Billing_address` varchar(100) NOT NULL,
  `InsertedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seat_data`
--

CREATE TABLE `seat_data` (
  `Seat_ID` int(11) NOT NULL,
  `Seat_category` varchar(25) NOT NULL,
  `Seat_Price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seat_data`
--

INSERT INTO `seat_data` (`Seat_ID`, `Seat_category`, `Seat_Price`) VALUES
(13, 'Economy', 10000.00),
(14, 'Premium_Economy', 15000.00),
(15, 'Business', 30000.00),
(16, 'First_Class', 50000.00);

-- --------------------------------------------------------

--
-- Table structure for table `ticket_t`
--

CREATE TABLE `ticket_t` (
  `Ticket_ID` int(11) NOT NULL,
  `Payment_ID` int(11) NOT NULL,
  `Total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_t`
--

CREATE TABLE `user_t` (
  `user_id` int(11) NOT NULL,
  `firstname` varchar(25) NOT NULL,
  `lastname` varchar(25) NOT NULL,
  `CNIC` varchar(15) NOT NULL,
  `email` varchar(30) NOT NULL,
  `user_type` varchar(9) NOT NULL,
  `Username` varchar(25) NOT NULL,
  `User_password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure for view `checkout`
--
DROP TABLE IF EXISTS `checkout`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `checkout`  AS SELECT `u`.`user_id` AS `user_id`, `u`.`firstname` AS `Firstname`, `u`.`lastname` AS `Lastname`, `u`.`CNIC` AS `CNIC`, `a`.`Airport_name` AS `Airport_name`, `a`.`Dest` AS `Dest`, `a`.`Airline` AS `Airline`, `f`.`Flight_ID` AS `Flight_ID`, `f`.`deperaturedate` AS `deperaturedate`, `f`.`arrivaldate` AS `arrivaldate`, `p`.`Payment_ID` AS `Payment_ID`, `p`.`Paymentmethod` AS `Paymentmethod`, `t`.`Ticket_ID` AS `Ticket_ID`, `t`.`Total_price` AS `Total_price` FROM ((((`user_t` `u` join `airport_t` `a` on(`u`.`user_id` = `a`.`user_id`)) join `flight_t` `f` on(`a`.`Airport_ID` = `f`.`Airport_ID`)) join `payment_t` `p` on(`f`.`Flight_ID` = `p`.`Flight_ID`)) join `ticket_t` `t` on(`p`.`Payment_ID` = `t`.`Payment_ID`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `airline_data`
--
ALTER TABLE `airline_data`
  ADD PRIMARY KEY (`Airline_id`);

--
-- Indexes for table `airport_t`
--
ALTER TABLE `airport_t`
  ADD PRIMARY KEY (`Airport_ID`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `city_data`
--
ALTER TABLE `city_data`
  ADD PRIMARY KEY (`City_id`);

--
-- Indexes for table `flight_t`
--
ALTER TABLE `flight_t`
  ADD PRIMARY KEY (`Flight_ID`),
  ADD KEY `Airport_ID` (`Airport_ID`);

--
-- Indexes for table `payment_method`
--
ALTER TABLE `payment_method`
  ADD PRIMARY KEY (`Payment_ID`);

--
-- Indexes for table `payment_t`
--
ALTER TABLE `payment_t`
  ADD PRIMARY KEY (`Payment_ID`),
  ADD KEY `Flight_ID` (`Flight_ID`),
  ADD KEY `Airport_ID` (`Airport_ID`);

--
-- Indexes for table `seat_data`
--
ALTER TABLE `seat_data`
  ADD PRIMARY KEY (`Seat_ID`);

--
-- Indexes for table `ticket_t`
--
ALTER TABLE `ticket_t`
  ADD PRIMARY KEY (`Ticket_ID`),
  ADD KEY `Payment_ID` (`Payment_ID`);

--
-- Indexes for table `user_t`
--
ALTER TABLE `user_t`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `airline_data`
--
ALTER TABLE `airline_data`
  MODIFY `Airline_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `airport_t`
--
ALTER TABLE `airport_t`
  MODIFY `Airport_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `city_data`
--
ALTER TABLE `city_data`
  MODIFY `City_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payment_method`
--
ALTER TABLE `payment_method`
  MODIFY `Payment_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `seat_data`
--
ALTER TABLE `seat_data`
  MODIFY `Seat_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user_t`
--
ALTER TABLE `user_t`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `airport_t`
--
ALTER TABLE `airport_t`
  ADD CONSTRAINT `airport_t_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_t` (`user_id`);

--
-- Constraints for table `flight_t`
--
ALTER TABLE `flight_t`
  ADD CONSTRAINT `flight_t_ibfk_1` FOREIGN KEY (`Airport_ID`) REFERENCES `airport_t` (`Airport_ID`);

--
-- Constraints for table `payment_t`
--
ALTER TABLE `payment_t`
  ADD CONSTRAINT `payment_t_ibfk_1` FOREIGN KEY (`Flight_ID`) REFERENCES `flight_t` (`Flight_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_t_ibfk_2` FOREIGN KEY (`Airport_ID`) REFERENCES `airport_t` (`Airport_ID`) ON DELETE CASCADE;

--
-- Constraints for table `ticket_t`
--
ALTER TABLE `ticket_t`
  ADD CONSTRAINT `ticket_t_ibfk_1` FOREIGN KEY (`Payment_ID`) REFERENCES `payment_t` (`Payment_ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
