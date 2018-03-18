-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 14, 2017 at 01:41 PM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 5.6.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `oursandcastle`
--

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `title` varchar(60) NOT NULL,
  `url` varchar(70) NOT NULL,
  `url_locked` enum('N','Y') NOT NULL DEFAULT 'N',
  `page` enum('N','Y') NOT NULL DEFAULT 'N',
  `meta_description` varchar(160) DEFAULT NULL,
  `content` text,
  `content_html` text,
  `content_excerpt` varchar(500) DEFAULT NULL,
  `published_date` date DEFAULT NULL,
  `template` varchar(50) DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '1',
  `updated_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`id`, `title`, `url`, `url_locked`, `page`, `meta_description`, `content`, `content_html`, `content_excerpt`, `published_date`, `template`, `created_by`, `created_date`, `updated_by`, `updated_date`) VALUES
(1, 'Home - Welcome', 'home', 'Y', 'N', 'Yachats home', 'If you are looking for a spacious and comfortable vacation rental in Yachats, Oregon, with fabulous ocean views, and a great 7 mile long beach for walking, Our Sand Castle is just what you''re looking for!\r\n\r\nThis spectacular beach view property sets back 120 feet from the beach, and has access down a private path to a set of very sturdy stairs.\r\n\r\nThis lovely, contemporary home is finely appointed with maple cabinets, granite counter tops, bamboo flooring, area rugs, washer and dryer, a jetted tub in the master bath large enough for two....and much, much more to make your stay enjoyable\r\nand relaxing. \r\n\r\nOur Sand Castle was designed to maximize the fabulous views by locating the entire guest vacation rental area on the upper floor of this two story dwelling. The downstairs consists of a garage for your use, plus a small apartment and separate\r\ngarage reserved for the owners use only. \r\n\r\nWhen you step just outside on the 6ft x 35ft deck you''ll drink in the sights, savor the refreshing salt air and fresh ocean breezes, and be ready to head to the beach!\r\n', '<p>If you are looking for a spacious and comfortable vacation rental in Yachats, Oregon, with fabulous ocean views, and a great 7 mile long beach for walking, Our Sand Castle is just what you''re looking for!</p>\n<p>This spectacular beach view property sets back 120 feet from the beach, and has access down a private path to a set of very sturdy stairs.</p>\n<p>This lovely, contemporary home is finely appointed with maple cabinets, granite counter tops, bamboo flooring, area rugs, washer and dryer, a jetted tub in the master bath large enough for two....and much, much more to make your stay enjoyable\nand relaxing. </p>\n<p>Our Sand Castle was designed to maximize the fabulous views by locating the entire guest vacation rental area on the upper floor of this two story dwelling. The downstairs consists of a garage for your use, plus a small apartment and separate\ngarage reserved for the owners use only. </p>\n<p>When you step just outside on the 6ft x 35ft deck you''ll drink in the sights, savor the refreshing salt air and fresh ocean breezes, and be ready to head to the beach!</p>', 'If you are looking for a spacious and comfortable vacation rental in Yachats, Oregon, with fabulous ocean views, and a great 7 mile long beach for walking, Our Sand Castle is just what you''re looking for! This spectacular beach view property sets back 120 feet from the beach, and has access down a', '2017-05-14', 'home.html', 1, '2017-05-14 12:08:15', 1, '2017-05-14 12:58:02'),
(2, 'Home - Features - House', 'rates-and-policies', 'Y', 'N', 'Rates and policies', '* Great ocean views!\r\n* Island counter with breakfast bar and 2 bar stools.\r\n* Solid wood dining table seats up to 10.\r\n* TV in bunkbed bedroom', '<ul>\n<li>Great ocean views!</li>\n<li>Island counter with breakfast bar and 2 bar stools.</li>\n<li>Solid wood dining table seats up to 10.</li>\n<li>TV in bunkbed bedroom</li>\n</ul>', 'Great ocean views! Island counter with breakfast bar and 2 bar stools. Solid wood dining table seats up to 10. TV in bunkbed bedroom', '2017-05-14', 'rates.html', 1, '2017-05-14 12:08:51', 1, '2017-05-14 13:02:36'),
(3, 'Home - Features - Comfort', 'home-features-comfort', 'N', 'N', NULL, '* HE Washer and dryer.\r\n* Central heat\r\n* Living room has very comfortable reclinable couches & chairs.\r\n* 2 car garage plus additional outside parking.\r\n* Ceiling fans in living room, master and both queen bedrooms.\r\n* Dish television programming\r\n* 50" LCD HDTV in living room.\r\n* 42" HDTV in master bedroom.\r\n* 32" HDTV in green bedroom.', '<ul>\n<li>HE Washer and dryer.</li>\n<li>Central heat</li>\n<li>Living room has very comfortable reclinable couches &amp; chairs.</li>\n<li>2 car garage plus additional outside parking.</li>\n<li>Ceiling fans in living room, master and both queen bedrooms.</li>\n<li>Dish television programming</li>\n<li>50&quot; LCD HDTV in living room.</li>\n<li>42&quot; HDTV in master bedroom.</li>\n<li>32&quot; HDTV in green bedroom.</li>\n</ul>', 'HE Washer and dryer. Central heat Living room has very comfortable reclinable couches &amp; chairs. 2 car garage plus additional outside parking. Ceiling fans in living room, master and both queen bedrooms. Dish television programming 50&quot; LCD HDTV in living room. 42&quot; HDTV in master', NULL, NULL, 1, '2017-05-14 13:03:04', 1, '2017-05-14 13:03:04'),
(4, 'Home - Features - Provide', 'home-features-provide', 'N', 'N', NULL, '* An assortment of games and books are on hand for your entertainment.\r\n* All linens and initial supply of paper goods provided.\r\n* Fully-equipped kitchen includes stove, microwave, dishwasher, coffee maker, blender, all dishes, pots & pans, linens, etc.\r\n* Gas BBQ on deck (but not in winter)', '<ul>\n<li>An assortment of games and books are on hand for your entertainment.</li>\n<li>All linens and initial supply of paper goods provided.</li>\n<li>Fully-equipped kitchen includes stove, microwave, dishwasher, coffee maker, blender, all dishes, pots &amp; pans, linens, etc.</li>\n<li>Gas BBQ on deck (but not in winter)</li>\n</ul>', 'An assortment of games and books are on hand for your entertainment. All linens and initial supply of paper goods provided. Fully-equipped kitchen includes stove, microwave, dishwasher, coffee maker, blender, all dishes, pots &amp; pans, linens, etc. Gas BBQ on deck (but not in winter)', NULL, NULL, 1, '2017-05-14 13:03:48', 1, '2017-05-14 13:09:39'),
(5, 'Home - Reviews', 'home-reviews', 'N', 'N', NULL, 'The only home we''ll rent again in Yachats\r\n\r\nI just want to give a sincere thank you to Don & Linda. We come out to Yachats every year, and this was the very first time our rental didn''t disappoint. We''ve gone through rental agencies and private parties, but previous rentals couldn''t compare.From the moment we inquired about the rental, to the payment process, to the day we arrived; the level of customer service and immediate responses were impeccable. The best part..... an amazingly clean and well furnished rental awaited my family. There was nothing lacking and all we can say is thank you. To anyone reading this and wondering whether they should stay here: My family had the best time ever in this home. It was very spacious, very comfortable beds, amazing ocean views, and super clean.We highly recommend this rental and hope it is available the next time we come out ti visit. Thanks again for sharing your home, Don & Linda :)\r\n\r\n—srain33 on 11/05/2016', '<p>The only home we''ll rent again in Yachats</p>\n<p>I just want to give a sincere thank you to Don &amp; Linda. We come out to Yachats every year, and this was the very first time our rental didn''t disappoint. We''ve gone through rental agencies and private parties, but previous rentals couldn''t compare.From the moment we inquired about the rental, to the payment process, to the day we arrived; the level of customer service and immediate responses were impeccable. The best part..... an amazingly clean and well furnished rental awaited my family. There was nothing lacking and all we can say is thank you. To anyone reading this and wondering whether they should stay here: My family had the best time ever in this home. It was very spacious, very comfortable beds, amazing ocean views, and super clean.We highly recommend this rental and hope it is available the next time we come out ti visit. Thanks again for sharing your home, Don &amp; Linda :)</p>\n<p>—srain33 on 11/05/2016</p>', 'The only home we''ll rent again in Yachats I just want to give a sincere thank you to Don &amp; Linda. We come out to Yachats every year, and this was the very first time our rental didn''t disappoint. We''ve gone through rental agencies and private parties, but previous rentals couldn''t compare.From', NULL, NULL, 1, '2017-05-14 13:10:22', 1, '2017-05-14 13:10:22'),
(6, 'Rates - Summer / Holiday', 'rates-summer-holiday', 'N', 'N', NULL, '* June 1 - September 5\r\n* $325 per night; 3 night minimum\r\n* Fourth of July Rate: $375 per night; 3 night minimum\r\n\r\n*\\*Holidays (minimum 3 nights) - Memorial Day, Thanksgiving, Christmas, New Years*\r\n', '<ul>\n<li>June 1 - September 5</li>\n<li>$325 per night; 3 night minimum</li>\n<li>Fourth of July Rate: $375 per night; 3 night minimum</li>\n</ul>\n<p><em>*Holidays (minimum 3 nights) - Memorial Day, Thanksgiving, Christmas, New Years</em></p>', 'June 1 - September 5 $325 per night; 3 night minimum Fourth of July Rate: $375 per night; 3 night minimum *Holidays (minimum 3 nights) - Memorial Day, Thanksgiving, Christmas, New Years', NULL, NULL, 1, '2017-05-14 13:14:20', 1, '2017-05-14 13:14:39'),
(7, 'Rates - Winter', 'rates-winter', 'N', 'N', NULL, '* Sept 6 - June 1\r\n* $200 per night; 2 night minimum\r\n\r\n##### Weekly Winter Rate:\r\n* $1,200 (RESERVE 6 nights, GET 7th NIGHT FREE)', '<ul>\n<li>Sept 6 - June 1</li>\n<li>$200 per night; 2 night minimum</li>\n</ul>\n<h5>Weekly Winter Rate:</h5>\n<ul>\n<li>$1,200 (RESERVE 6 nights, GET 7th NIGHT FREE)</li>\n</ul>', 'Sept 6 - June 1 $200 per night; 2 night minimum Weekly Winter Rate: $1,200 (RESERVE 6 nights, GET 7th NIGHT FREE)', NULL, NULL, 1, '2017-05-14 13:16:03', 1, '2017-05-14 13:16:31'),
(8, 'Rates - Details', 'rates-details', 'N', 'N', NULL, '##### Check-in/Check-out\r\n* Check-in is at 4:00 p.m. Check-out is 11:00 a.m.\r\n\r\n##### Advance Deposit\r\n* 50% of the rental fee is required to secure a reservation. The balance is due 30 days prior to check-in date.\r\n\r\n##### Cleaning Fee\r\n* Non-refundable cleaning fee: $120\r\n\r\n##### Damage Deposit\r\n* $300 refundable damage deposit.\r\n\r\n##### Cancellation Policy\r\n* 60 days or more - Full refund of advance deposit minus $25.00 fee.\r\n* 30 to 59 days - 80% refund of advance deposit Minus $25.00 fee.\r\n* 29 days or less - no refund unless the unit can be re-rented. Re-rental income credited towards refund, minus $25.00 fee.', '<h5>Check-in/Check-out</h5>\n<ul>\n<li>Check-in is at 4:00 p.m. Check-out is 11:00 a.m.</li>\n</ul>\n<h5>Advance Deposit</h5>\n<ul>\n<li>50% of the rental fee is required to secure a reservation. The balance is due 30 days prior to check-in date.</li>\n</ul>\n<h5>Cleaning Fee</h5>\n<ul>\n<li>Non-refundable cleaning fee: $120</li>\n</ul>\n<h5>Damage Deposit</h5>\n<ul>\n<li>$300 refundable damage deposit.</li>\n</ul>\n<h5>Cancellation Policy</h5>\n<ul>\n<li>60 days or more - Full refund of advance deposit minus $25.00 fee.</li>\n<li>30 to 59 days - 80% refund of advance deposit Minus $25.00 fee.</li>\n<li>29 days or less - no refund unless the unit can be re-rented. Re-rental income credited towards refund, minus $25.00 fee.</li>\n</ul>', 'Check-in/Check-out Check-in is at 4:00 p.m. Check-out is 11:00 a.m. Advance Deposit 50% of the rental fee is required to secure a reservation. The balance is due 30 days prior to check-in date. Cleaning Fee Non-refundable cleaning fee: $120 Damage Deposit $300 refundable damage deposit.', NULL, NULL, 1, '2017-05-14 13:17:55', 1, '2017-05-14 13:17:55'),
(9, 'Rates - Policies', 'rates-policies', 'N', 'N', NULL, '##### Thank you for observing our house policies:\r\n* No Pets - if there is evidence of pet(s) found on the premise, your damage deposit will be forfeited and you may be asked to leave.\r\n* No Smoking - if evidence of smoking is found on the premise, your damage deposit will be forfeited and you may be asked to leave.', '<h5>Thank you for observing our house policies:</h5>\n<ul>\n<li>No Pets - if there is evidence of pet(s) found on the premise, your damage deposit will be forfeited and you may be asked to leave.</li>\n<li>No Smoking - if evidence of smoking is found on the premise, your damage deposit will be forfeited and you may be asked to leave.</li>\n</ul>', 'Thank you for observing our house policies: No Pets - if there is evidence of pet(s) found on the premise, your damage deposit will be forfeited and you may be asked to leave. No Smoking - if evidence of smoking is found on the premise, your damage deposit will be forfeited and you may be asked to', NULL, NULL, 1, '2017-05-14 13:18:33', 1, '2017-05-14 13:18:33');

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `session_id` char(64) NOT NULL,
  `data` text,
  `user_agent` char(64) DEFAULT NULL,
  `ip_address` varchar(46) DEFAULT NULL,
  `time_updated` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `post_url_idx` (`url`),
  ADD KEY `post_published_idx` (`published_date`),
  ADD KEY `post_page_idx` (`page`);
ALTER TABLE `post` ADD FULLTEXT KEY `post_fulltext_idx` (`title`,`content`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`session_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;