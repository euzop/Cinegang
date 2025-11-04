-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 18, 2024 at 05:47 PM
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
-- Database: `cinegang`
--

-- --------------------------------------------------------

--
-- Table structure for table `anime`
--

CREATE TABLE `anime` (
  `aid` int(100) NOT NULL,
  `anime_name` varchar(150) NOT NULL,
  `anime_year` int(4) NOT NULL,
  `anime_genre` varchar(30) NOT NULL,
  `anime_poster_link` varchar(200) NOT NULL,
  `anime_cover_link` varchar(200) NOT NULL,
  `anime_description` varchar(600) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anime`
--

INSERT INTO `anime` (`aid`, `anime_name`, `anime_year`, `anime_genre`, `anime_poster_link`, `anime_cover_link`, `anime_description`) VALUES
(30001, 'Sousou No Frieren', 2023, 'Adventure', 'https://imgur.com/d60Aroz', 'https://imgur.com/p1Rednz', '\"Sousou no Frieren\" is a fantasy manga about an elf mage who outlives her companions. Frieren, the protagonist, reflects on her life and relationships as she continues living long after her adventuring party has passed away. The series explores themes of mortality, companionship, and finding purpose in an immortal existence.'),
(30002, 'Hunter X Hunter', 2011, 'Dark Fantasy', 'https://imgur.com/Fb4jvJB', 'https://imgur.com/dcYBcSh', '\"Hunter x Hunter\" is an acclaimed shonen anime/manga about Gon, a young boy who embarks on a journey to become a licensed \'Hunter\' - an elite warrior tasked with dangerous missions. Filled with captivating characters, intricate power systems, and thrilling adventures, the series explores themes of ambition, friendship, and the complexities of morality.'),
(30003, 'Naruto: Shippuuden', 2007, 'Shonen', 'https://imgur.com/EKOBElo', 'https://imgur.com/WtQjoi6', '\"Naruto Shippuden\" continues the epic tale of the titular ninja, Naruto Uzumaki, as he matures and takes on greater responsibilities in the ninja world. The series features intense battles, character development, and an expansive narrative exploring themes of ninja warfare, found family, and the search for peace. Naruto\'s growth into a powerful shinobi and leader is the core focus of this beloved sequel series.'),
(30004, 'Bleach', 2004, 'Action', 'https://imgur.com/dpbUNaX', 'https://imgur.com/d1wILVU', '\"Bleach\" follows Ichigo Kurosaki, a teenager who gains the powers of a Soul Reaper and is thrust into a supernatural conflict between the living and the dead. With its unique blend of Japanese folklore, intense action sequences, and character-driven storytelling, Bleach explores themes of death, purpose, and the duality of human nature as Ichigo protects the living world from powerful spiritual threats.'),
(30005, 'ONE PIECE', 1999, 'Adventure', 'https://imgur.com/wsAQkh5', 'https://imgur.com/9cuw9o5', '\"One Piece\" chronicles the pirate Monkey D. Luffy\'s quest to find the legendary treasure \'One Piece\' and become the King of the Pirates. With its sprawling world, colorful cast of characters, and thrilling action, the series delves into themes of adventure, dreams, friendship, and the freedom to forge one\'s own destiny, making it a beloved shonen anime/manga epic.');

-- --------------------------------------------------------

--
-- Table structure for table `cartoon`
--

CREATE TABLE `cartoon` (
  `cid` int(100) NOT NULL,
  `cartoon_name` varchar(150) NOT NULL,
  `cartoon_year` int(4) NOT NULL,
  `cartoon_genre` varchar(30) NOT NULL,
  `cartoon_poster_link` varchar(200) NOT NULL,
  `cartoon_cover_link` varchar(200) NOT NULL,
  `cartoon_description` varchar(600) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cartoon`
--

INSERT INTO `cartoon` (`cid`, `cartoon_name`, `cartoon_year`, `cartoon_genre`, `cartoon_poster_link`, `cartoon_cover_link`, `cartoon_description`) VALUES
(40001, 'The Amazing World of Gumball', 2011, 'Surreal comedy', 'https://imgur.com/Gstt0Vz', 'https://imgur.com/iaxIX6P', 'Gumball Watterson, a 12-year-old cat who attends middle school in the city of Elmore, and his former pet goldfish, Darwin, find themselves involved in shenanigans around the city.');

-- --------------------------------------------------------

--
-- Table structure for table `movie`
--

CREATE TABLE `movie` (
  `mid` int(100) NOT NULL,
  `movie_name` varchar(150) NOT NULL,
  `movie_genre` varchar(30) NOT NULL,
  `movie_poster_link` varchar(200) NOT NULL,
  `movie_cover_link` varchar(200) NOT NULL,
  `movie_description` varchar(600) DEFAULT NULL,
  `movie_year` int(4) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movie`
--

INSERT INTO `movie` (`mid`, `movie_name`, `movie_genre`, `movie_poster_link`, `movie_cover_link`, `movie_description`, `movie_year`) VALUES
(10001, 'Forrest Gump', 'Comedy Drama', 'https://imgur.com/vb3of69', 'https://imgur.com/j8PV1xg', 'Slow-witted Forrest Gump (Tom Hanks) has never thought of himself as disadvantaged, and thanks to his supportive mother (Sally Field), he leads anything but a restricted life. Whether dominating on the gridiron as a college football star, fighting in Vietnam or captaining a shrimp boat, Forrest inspires people with his childlike optimism.', 1994),
(10002, 'Chungking Express', 'Romance', 'https://imgur.com/I6DtB6w', 'https://imgur.com/Teb5iOk', 'Every day, Cop 223 (Takeshi Kaneshiro) buys a can of pineapple with an expiration date of May 1, symbolizing the day he\'ll get over his lost love. He\'s also got his eye on a mysterious woman in a blond wig (Brigitte Lin), oblivious of the fact she\'s a drug dealer.', 1994);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `rev_id` int(11) NOT NULL,
  `media_id` int(20) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `user` varchar(100) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `star` float DEFAULT NULL,
  `review_desc` varchar(600) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`rev_id`, `media_id`, `uid`, `user`, `datetime`, `star`, `review_desc`) VALUES
(11001, 30001, 3, 'euzop', '2024-07-14 01:14:33', 5, 'it\'s good, frieren is cool!'),
(11002, 20001, 3, 'euzop', '2024-07-14 06:08:19', 4, 'good series!'),
(11003, 40001, 3, 'euzop', '2024-07-14 06:09:03', 4, 'very funny cartoon'),
(11026, 30003, 7, NULL, '2024-07-18 04:45:31', 5, 'gg'),
(11027, 10001, 7, NULL, '2024-07-18 04:46:08', 5, 'retard dude is cool'),
(11028, 10002, 3, NULL, '2024-07-18 04:57:32', 5, 'shes hot');

-- --------------------------------------------------------

--
-- Table structure for table `series`
--

CREATE TABLE `series` (
  `sid` int(100) NOT NULL,
  `series_name` varchar(150) NOT NULL,
  `series_year` int(4) NOT NULL,
  `series_genre` varchar(30) NOT NULL,
  `series_poster_link` varchar(200) NOT NULL,
  `series_cover_link` varchar(200) NOT NULL,
  `series_description` varchar(600) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `series`
--

INSERT INTO `series` (`sid`, `series_name`, `series_year`, `series_genre`, `series_poster_link`, `series_cover_link`, `series_description`) VALUES
(20001, 'How I Met Your Mother', 2005, 'Comedy', 'https://imgur.com/UU7fo9w', 'https://imgur.com/Zs2Qpgt', 'Ted has fallen in love. It all started when his best friend, Marshall, drops the bombshell that he plans to propose to longtime girlfriend Lily, a kindergarten teacher. Suddenly, Ted realizes that he had better get a move on if he hopes to find true love.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` int(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(25) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `name`, `email`, `password`, `nickname`, `profile_pic`, `bio`, `mobile`) VALUES
(1, 'admin', 'admin@cinegang.com', 'admin@123', NULL, NULL, NULL, NULL),
(2, 'user', 'user@cinegang.com', 'user@123', NULL, NULL, NULL, NULL),
(3, 'Euzop', '202210333@fit.edu.ph', 'euzop2000', 'Monke', 'https://imgur.com/YDj6pGV', 'Hello I\'m euzop', '09927995674'),
(4, 'carl justin', 'carllourd123@gmail.com', 'qwerty', NULL, NULL, NULL, NULL),
(5, 'json', '202210312@fit.edu.ph', 'Polpik_011', '.Json', 'https://imgur.com/zL1eSzy', 'Bumabayo ng bata', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `watchlist`
--

CREATE TABLE `watchlist` (
  `wid` int(11) NOT NULL,
  `media_id` int(20) NOT NULL,
  `user` varchar(50) NOT NULL,
  `uid` int(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anime`
--
ALTER TABLE `anime`
  ADD PRIMARY KEY (`aid`);

--
-- Indexes for table `cartoon`
--
ALTER TABLE `cartoon`
  ADD PRIMARY KEY (`cid`);

--
-- Indexes for table `movie`
--
ALTER TABLE `movie`
  ADD PRIMARY KEY (`mid`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`rev_id`);

--
-- Indexes for table `series`
--
ALTER TABLE `series`
  ADD PRIMARY KEY (`sid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `watchlist`
--
ALTER TABLE `watchlist`
  ADD PRIMARY KEY (`wid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `movie`
--
ALTER TABLE `movie`
  MODIFY `mid` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10006;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `rev_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11029;

--
-- AUTO_INCREMENT for table `series`
--
ALTER TABLE `series`
  MODIFY `sid` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20002;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `watchlist`
--
ALTER TABLE `watchlist`
  MODIFY `wid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22001;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
