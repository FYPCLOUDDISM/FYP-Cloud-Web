-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 09, 2024 at 04:47 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cloud_fyp`
--

-- --------------------------------------------------------

--
-- Table structure for table `antivirus`
--

CREATE TABLE `antivirus` (
  `avId` int(20) NOT NULL,
  `avName` varchar(60) NOT NULL,
  `avLink` varchar(60) NOT NULL,
  `picture` varchar(60) NOT NULL,
  `description` varchar(300) NOT NULL,
  `avCreator` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `antivirus`
--

INSERT INTO `antivirus` (`avId`, `avName`, `avLink`, `picture`, `description`, `avCreator`) VALUES
(1, 'BitDefender', 'https://www.bitdefender.com', 'bitdefender.png', 'Bitdefender is a global cybersecurity leader protecting over 500 million systems in more than 150 countries. They offer a wide range of security products for home and business use, including antivirus, internet security, and total security suites.', 'Bitdefender SRL'),
(2, 'McAfee', 'https://www.mcafee.com/', 'mcafee.png', 'McAfee is a well-known provider of cybersecurity solutions, offering antivirus, firewall, and identity protection software. Their products are designed to safeguard computers, mobile devices, and online activities from various threats, including viruses, malware, and phishing attacks.', 'McAfee, LLC'),
(3, 'Malwarebytes', 'https://www.malwarebytes.com/', 'malwarebytes.png', 'Malwarebytes is an anti-malware software that provides real-time protection against malware, ransomware, and other advanced online threats. It uses advanced technology to detect and remove malicious software from computers and mobile devices.', 'Malwarebytes Corporation'),
(4, 'norton', 'https://sg.norton.com/', 'norton.png', 'Norton, a trusted cybersecurity leader, offers comprehensive antivirus solutions safeguarding against viruses, malware, and more. Features include firewall, VPN, and identity theft protection', 'NortonLifeLock Inc.'),
(5, 'G DATA', 'https://www.gdatasoftware.com/', 'gdata.png', 'G Data is a German cybersecurity company known for its antivirus solutions for both home and business users. Their products utilize a combination of signature-based detection and behavioral analysis to protect against a wide range of malware threats.\r\n', 'G Data Software AG');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `quesId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `question` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`quesId`, `userId`, `question`) VALUES
(1, 5, 'is mcafee really that jialat?'),
(2, 1, 'which antivirus is the best out of the 5?');

-- --------------------------------------------------------

--
-- Table structure for table `replies`
--

CREATE TABLE `replies` (
  `replyId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `quesId` int(11) NOT NULL,
  `reply` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `replies`
--

INSERT INTO `replies` (`replyId`, `userId`, `quesId`, `reply`) VALUES
(1, 3, 1, 'yep mcafee is so bad. it always changes ur browser to yahoo and says it is \"secure\". even free antivirus is better\r\n'),
(2, 1, 1, 'extremely ass, so bad'),
(3, 2, 3, 'testing 123456');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `reviewId` int(11) NOT NULL,
  `avId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `review` text NOT NULL,
  `rating` int(11) NOT NULL,
  `datePosted` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`reviewId`, `avId`, `userId`, `review`, `rating`, `datePosted`) VALUES
(1, 1, 1, 'Decent Antivirus Software.', 3, '2024-04-21'),
(2, 2, 1, 'worst antivirus software ever, could give 0 stars if i could do so. keep changing my yahoo to google stupid trash', 1, '2024-04-21'),
(3, 2, 2, 'dogshit antivirus software, even a free antivirus is better than this. TRASH SHIT', 1, '2024-04-21'),
(4, 4, 5, 'not bad better than mcafee', 4, '2024-04-22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userId` int(11) NOT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(40) NOT NULL,
  `name` varchar(80) NOT NULL,
  `dob` varchar(80) NOT NULL,
  `email` varchar(80) NOT NULL,
  `2fa_code` varchar(10) DEFAULT NULL,
  `2fa_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `username`, `password`, `name`, `dob`, `email`, `2fa_code`, `2fa_expiry`) VALUES
(1, 'fyp', '200d79589b9a3d1684a8260fdd3ca7483e0934e5', 'fyp', '2024-04-15', 'fypclouddism24@gmail.com', NULL, NULL),
(2, 'justin', '0ce7911e6479995d6c346d6f03eb723b5135309e', 'Justin', '1997-05-18', ' r95600968@gmail.com', '008707', '2024-07-02 21:28:59'),
(3, 'hoylune', 'ff8379a9ced08780c595c30155e4622cb4de23ff', 'Poh Hoy Lune', '1999-05-30', '22011078@myrp.edu.sg', NULL, NULL),
(5, 'hongkai', 'b09a8c72fcb3b54106a8159af13b202da360ce6f', 'Tan Hong Kai', '2004-04-04', 'steltors04@gmail.com', '996525', '2024-07-02 21:28:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `antivirus`
--
ALTER TABLE `antivirus`
  ADD PRIMARY KEY (`avId`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`quesId`);

--
-- Indexes for table `replies`
--
ALTER TABLE `replies`
  ADD PRIMARY KEY (`replyId`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`reviewId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `antivirus`
--
ALTER TABLE `antivirus`
  MODIFY `avId` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `quesId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `replies`
--
ALTER TABLE `replies`
  MODIFY `replyId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `reviewId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
