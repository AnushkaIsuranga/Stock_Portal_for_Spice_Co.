-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2024 at 06:07 AM
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
-- Database: `spiceco`
--

-- --------------------------------------------------------

--
-- Table structure for table `order_table`
--

CREATE TABLE `order_table` (
  `Order_ID` int(11) NOT NULL,
  `User_ID` int(10) NOT NULL,
  `Date` date NOT NULL,
  `Quantity` int(50) NOT NULL,
  `Price_Rs` int(50) NOT NULL,
  `Recite` varchar(225) NOT NULL,
  `Add_Line1` varchar(225) NOT NULL,
  `Add_Line2` varchar(225) NOT NULL,
  `needs_verification` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_table`
--

CREATE TABLE `users_table` (
  `User_ID` int(11) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `F_Name` varchar(50) NOT NULL,
  `L_Name` varchar(50) NOT NULL,
  `U_Name` varchar(50) NOT NULL,
  `NIC` varchar(50) NOT NULL,
  `Tel_No` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_table`
--

INSERT INTO `users_table` (`User_ID`, `Email`, `F_Name`, `L_Name`, `U_Name`, `NIC`, `Tel_No`, `Password`) VALUES
(3, '123@abc.com', 'abc', 'abc', 'abc', '123', '123', '$2y$10$s/in1ti3ujXjIIiMIEMGL.02i7qtFgEI9cl9p2BA.qXtGsYj3cJEq'),
(5, '1234@abc.com', 'abc', 'abc', 'abc', '12345', '123', '$2y$10$FBgjfUUCRuPsjt3Q50zEBumZzQqWlobSrLqwxGgb9uTRIzNn2JhZ.'),
(7, '12345@abc.com', 'abc', 'abc', 'abc', '12345', '123', '$2y$10$L4Aj1a56n4N1a57/M3FzGeyOUBPXVbdLTEUBGXKlD0M/bvKr5ZooW'),
(8, '1234556@abc.com', 'abc', 'abc', 'abcde', '1234', '123', '$2y$10$jn/J466eev3Memtxp2WGgOZ91K68JqLoKEYhduD2h1/tT2lrtV/Va');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `order_table`
--
ALTER TABLE `order_table`
  ADD PRIMARY KEY (`Order_ID`);

--
-- Indexes for table `users_table`
--
ALTER TABLE `users_table`
  ADD PRIMARY KEY (`User_ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `order_table`
--
ALTER TABLE `order_table`
  MODIFY `Order_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users_table`
--
ALTER TABLE `users_table`
  MODIFY `User_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
