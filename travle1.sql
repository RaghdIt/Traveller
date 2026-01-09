-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 07, 2024 at 08:44 PM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `travle1`
--

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `comment` text NOT NULL,
  `placeID` int(255) NOT NULL,
  `userID` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`comment`, `placeID`, `userID`) VALUES
('hkhkjh', 0, 0),
('xsafscf', 341, 14),
('dfdvdzv', 341, 14),
('waaw', 348, 26),
('i want to visit it', 348, 26);

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE `country` (
  `id` int(13) NOT NULL,
  `country` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`id`, `country`) VALUES
(111, 'Saudi Arabia'),
(222, 'Italy'),
(333, 'France'),
(444, 'London');

-- --------------------------------------------------------

--
-- Table structure for table `like`
--

CREATE TABLE `like` (
  `placeID` int(13) NOT NULL,
  `userID` int(13) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `like`
--

INSERT INTO `like` (`placeID`, `userID`) VALUES
(341, 13),
(341, 14),
(348, 26);

-- --------------------------------------------------------

--
-- Table structure for table `place`
--

CREATE TABLE `place` (
  `id` int(13) NOT NULL,
  `travelID` int(13) NOT NULL,
  `name` varchar(25) NOT NULL,
  `location` varchar(25) NOT NULL,
  `description` text NOT NULL,
  `photoFileName` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `place`
--

INSERT INTO `place` (`id`, `travelID`, `name`, `location`, `description`, `photoFileName`) VALUES
(222, 1, 'GYm', 'mfgjknjkh', '', 'njn'),
(333, 1, 'MAMLAKAH', 'UOLAIA', '', 'BB'),
(334, 3, 'sd', 'sds', 'scd', '3_2024-05-09 (16).png'),
(336, 5, 'frefae', 'fersafae', 'rgert', ''),
(341, 11, 'frefae', 'fersafae', 'dsfaf', ''),
(342, 12, 'frefae', 'fersafae', 'dsfaf', ''),
(343, 12, 'dsd', 'dfsaf', 'dsfaf', ''),
(347, 16, 'Mamlakah toure', 'riyadh', 'perfect', 'uploads/16_download.jpeg'),
(348, 17, 'evval toure', 'paris', 'GOOD', 'uploads/17_download (1).jpeg'),
(349, 18, 'italy', 'italy', 'buetifull', 'uploads/18_download (3).jpeg'),
(350, 19, 'italy', 'italy', 'good', 'uploads/19_download (3).jpeg'),
(351, 20, 'Mamlakah toure', 'riyadh', 'waaw', 'uploads/20_download.jpeg'),
(352, 21, 'evval toure', 'paris', 'good', 'uploads/21_download (1).jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `travle`
--

CREATE TABLE `travle` (
  `id` int(13) NOT NULL,
  `userID` int(13) NOT NULL,
  `month` varchar(25) NOT NULL,
  `year` varchar(25) NOT NULL,
  `countryID` int(13) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `travle`
--

INSERT INTO `travle` (`id`, `userID`, `month`, `year`, `countryID`) VALUES
(1, 2, 'January', '2024', 333),
(2, 2, 'January', '2024', 333),
(3, 2, '2', '2023', 333),
(5, 2, '6', '2025', 333),
(7, 3, '3', '2023', 222),
(9, 10, '2', '2024', 111),
(11, 11, 'January', '2024', 111),
(12, 11, '3', '2023', 222),
(16, 26, 'January', '2024', 111),
(17, 26, '2', '2024', 333),
(18, 26, '5', '2025', 222),
(19, 28, '2', '2025', 222),
(20, 28, '4', '2025', 111),
(21, 28, '5', '2025', 333);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(13) NOT NULL,
  `firstName` varchar(25) NOT NULL,
  `lastName` varchar(25) NOT NULL,
  `emailAddress` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `photoFileName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `firstName`, `lastName`, `emailAddress`, `password`, `photoFileName`) VALUES
(2, 'dsfsf', 'adgadfg', 'gfdgsd@bgff', '$2y$10$EG.Mm5mFuRwPjy.kuydMLeB7.IdIGd2v2Mz0RMWRk8r.43SjzdeG6', 'default.jpg'),
(3, 'dsfsf', 'adgadfg', 'gfdgsd@bgff', '$2y$10$eyeEJbqnIunWgmffiicRJez77teG9TxXUNrhbFqjQb5H7IGval6sS', 'default.jpg'),
(10, 'dsfsf', 'adgadfg', 'gfdgsd@bgff', '$2y$10$YIt8l9BQsPjPXO9ZAzVZne4YFLEBiKZJETcXxg4fm6FMB7Q3/5yOG', '2024-05-12 (1).png'),
(11, 'RRRRRRRRRRR', 'adgadfg', 'gfdgsd@bgff', '$2y$10$s2bHAm3mVmeMFHFFOR4tNuxNBE/.5FVT9MdbmopBAglddZ6Bgq9Z6', '2024-09-18.png'),
(12, 'RRRRRRRRRRR', 'adgadfg', 'gfdgsd@bgff', '$2y$10$teymXQMOlK21JDIjv47VIuJ97mdYWb.6Q25VaVn.Djr1veiNtlQvi', 'default.jpg'),
(13, 'R', 'adgadfg', 'gfdgsd@bgff', '$2y$10$JGCpAclNKPsYuRq2MAT30e9.5PdcnNKhXpwKOaxdi..xzqu.sE5/m', 'default.jpg'),
(14, 'grtehwthwh', 'adgadfg', 'gfdgsd@bgff', '$2y$10$q4WRmJgkMcdz759W67mj1.JkyRk75HEmNKRwovPxRmA93VuUmqIaG', 'default.jpg'),
(21, 'grtehwthwh', 'adgadfg', 'gfdgsd@bgff', '$2y$10$4ZvOQIfr4Q5nLdMWhkoy0.HgkrqhvmQY39vUwBX1uNAugi/pIkdLm', 'pfp.jpg'),
(22, 'raghad', 'adgadfg', 'gfdgsd@bgff', '$2y$10$q0rq2EAIIutgynmeD4nbIeo2n1MQb5i6d3d/cTKL5uAEWFouUKBUa', 'pfp.jpg'),
(24, 'grtehwthwh', 'adgadfg', 'gfdghsd@bgff', '$2y$10$Mqs5hMKEhXbgqmxo3Ef9wOX0loJjxoKMjAKJ78FBVrCgRzua.EL7C', 'image/pfp.jpg'),
(25, 'grtehwthwh', 'adgadfg', 'gfdghjjsd@bgff', '$2y$10$u39WGvdB.u/d/DSCaw6pjOctJcs.bJTnfkAScKxt8g9aZb6nyk6m.', 'pfp.jpg'),
(26, 'raghad', 'adgadfg', 'gfdgFEWFsd@bgff', '$2y$10$9DbB9loe29LjZIVLkPeBCOPMtWZ0QhwThdHdJp25UAL1QntIXzucO', '2024-05-09 (18).png'),
(27, 'raghad', 'hassan', 'raghad@gmail', '$2y$10$LsWRaEC2k/6XawD.zvgcVeUrVRvVZey46GyiK0HqGCoqHAO4GgqcC', 'download (2).jpeg'),
(28, 'nourah', 'nasser', 'nourah@gmail', '$2y$10$sp/P0YFSWq2myTPqXoUcGeCy9eU0j0UahWH/E64RLvNHRH41QOka2', 'download (3).jpeg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `like`
--
ALTER TABLE `like`
  ADD KEY `placeID` (`placeID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `place`
--
ALTER TABLE `place`
  ADD PRIMARY KEY (`id`),
  ADD KEY `travelID` (`travelID`);

--
-- Indexes for table `travle`
--
ALTER TABLE `travle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userID` (`userID`),
  ADD KEY `countryID` (`countryID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `place`
--
ALTER TABLE `place`
  MODIFY `id` int(13) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=353;

--
-- AUTO_INCREMENT for table `travle`
--
ALTER TABLE `travle`
  MODIFY `id` int(13) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(13) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `like`
--
ALTER TABLE `like`
  ADD CONSTRAINT `like_ibfk_1` FOREIGN KEY (`placeID`) REFERENCES `place` (`id`),
  ADD CONSTRAINT `like_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `user` (`id`);

--
-- Constraints for table `place`
--
ALTER TABLE `place`
  ADD CONSTRAINT `place_ibfk_1` FOREIGN KEY (`travelID`) REFERENCES `travle` (`id`);

--
-- Constraints for table `travle`
--
ALTER TABLE `travle`
  ADD CONSTRAINT `travle_ibfk_1` FOREIGN KEY (`countryID`) REFERENCES `country` (`id`),
  ADD CONSTRAINT `travle_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
