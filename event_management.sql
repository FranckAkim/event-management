-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2026 at 11:07 AM
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
-- Database: `event_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `BookingID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `VenueID` int(11) NOT NULL,
  `EventID` int(11) NOT NULL,
  `Status` varchar(20) NOT NULL DEFAULT 'PENDING',
  `RequestedAt` datetime DEFAULT NULL,
  `DepositAmount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Notes` text DEFAULT NULL
) ;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`BookingID`, `UserID`, `VenueID`, `EventID`, `Status`, `RequestedAt`, `DepositAmount`, `Notes`) VALUES
(1, 4, 1, 1, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(2, 5, 1, 1, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(3, 6, 1, 1, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(4, 4, 5, 2, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(5, 7, 5, 2, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(6, 8, 3, 3, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(7, 9, 3, 3, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(8, 4, 4, 4, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(9, 5, 4, 4, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(10, 6, 1, 5, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(11, 7, 1, 5, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(12, 8, 1, 5, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(13, 9, 5, 6, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(14, 10, 5, 6, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(15, 4, 2, 7, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(16, 5, 2, 7, 'CANCELLED', '2026-04-15 03:30:11', 0.00, ''),
(17, 6, 3, 8, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(18, 7, 3, 8, 'CANCELLED', '2026-04-15 03:30:11', 0.00, ''),
(19, 4, 1, 9, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(20, 5, 1, 9, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(21, 6, 1, 10, 'CANCELLED', '2026-04-15 03:30:11', 0.00, ''),
(22, 8, 6, 11, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(23, 9, 6, 11, 'CANCELLED', '2026-04-15 03:30:11', 0.00, ''),
(24, 4, 2, 12, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(25, 5, 2, 12, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(26, 6, 2, 12, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(27, 7, 2, 12, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(28, 8, 2, 12, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(29, 9, 2, 12, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(30, 10, 2, 12, 'APPROVED', '2026-04-15 03:30:11', 0.00, ''),
(31, 10, 1, 9, 'CANCELLED', '2026-04-15 03:30:11', 0.00, 'Changed plans'),
(32, 7, 5, 6, 'CANCELLED', '2026-04-15 03:30:11', 0.00, 'Late registration');

--
-- Triggers `booking`
--
DELIMITER $$
CREATE TRIGGER `trg_booking_before_insert` BEFORE INSERT ON `booking` FOR EACH ROW BEGIN
    IF NEW.RequestedAt IS NULL THEN
        SET NEW.RequestedAt = NOW();
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_booking_check_capacity` BEFORE INSERT ON `booking` FOR EACH ROW BEGIN
    DECLARE current_bookings INT;
    DECLARE event_capacity   INT;

    SELECT COUNT(*) INTO current_bookings
    FROM booking
    WHERE EventID = NEW.EventID
      AND Status IN ('PENDING', 'APPROVED');

    SELECT CapacityLimit INTO event_capacity
    FROM event
    WHERE EventID = NEW.EventID;

    IF current_bookings >= event_capacity THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Event is fully booked. Capacity limit reached.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `EventID` int(11) NOT NULL,
  `Title` varchar(200) NOT NULL,
  `Description` text DEFAULT NULL,
  `OrganizerID` int(11) NOT NULL,
  `CapacityLimit` int(11) NOT NULL DEFAULT 100,
  `Status` varchar(20) NOT NULL DEFAULT 'DRAFT',
  `IsPrivate` tinyint(1) NOT NULL DEFAULT 0
) ;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`EventID`, `Title`, `Description`, `OrganizerID`, `CapacityLimit`, `Status`, `IsPrivate`) VALUES
(1, 'Spring Wedding Reception', 'Elegant spring wedding with dinner and dancing.', 2, 200, 'CONFIRMED', 0),
(2, 'Charity Fundraiser Dinner', 'Black-tie charity dinner with live auction.', 3, 250, 'CONFIRMED', 0),
(3, 'Graduation Ceremony', 'University graduation celebration.', 2, 300, 'CONFIRMED', 0),
(4, 'Morning Board Meeting', 'Quarterly board meeting with breakfast.', 2, 40, 'CONFIRMED', 0),
(5, 'Luncheon Awards Ceremony', 'Annual staff awards and recognition lunch.', 3, 120, 'CONFIRMED', 0),
(6, 'Evening Gala Dinner', 'Formal gala dinner for VIP guests.', 2, 200, 'CONFIRMED', 0),
(7, 'Baby Shower Brunch', 'Garden baby shower with brunch and games.', 3, 60, 'CONFIRMED', 0),
(8, 'Retirement Party', 'Retirement celebration for a long-serving staff member.', 2, 90, 'CONFIRMED', 0),
(9, 'Tech Conference Day 1', 'Annual technology conference — day one.', 2, 250, 'CONFIRMED', 0),
(10, 'Tech Conference Day 2', 'Annual technology conference — day two.', 3, 250, 'CONFIRMED', 0),
(11, 'Wine Tasting Evening', 'Curated wine tasting with a professional sommelier.', 3, 70, 'CONFIRMED', 0),
(12, 'Intimate Wedding Celebration', 'Small, elegant wedding ceremony and reception.', 2, 20, 'CONFIRMED', 0),
(13, 'Birthday Gala 50th', 'Milestone 50th birthday with live music and dancing.', 3, 150, 'CONFIRMED', 0),
(14, 'Proposed Christmas Party', 'Tentative Christmas celebration pending approval.', 2, 200, 'DRAFT', 0),
(15, 'Possible Offsite Retreat', 'Draft team offsite, venue and date to be confirmed.', 3, 80, 'DRAFT', 0);

-- --------------------------------------------------------

--
-- Table structure for table `event_resource`
--

CREATE TABLE `event_resource` (
  `EventID` int(11) NOT NULL,
  `ResourceID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL DEFAULT 1
) ;

--
-- Dumping data for table `event_resource`
--

INSERT INTO `event_resource` (`EventID`, `ResourceID`, `Quantity`) VALUES
(1, 3, 20),
(1, 4, 10),
(1, 6, 8),
(2, 3, 30),
(2, 4, 20),
(3, 2, 6),
(3, 6, 10),
(4, 1, 1),
(5, 2, 4),
(5, 3, 15),
(5, 4, 10),
(6, 6, 6),
(6, 7, 1),
(7, 3, 8),
(7, 5, 1),
(8, 2, 2),
(8, 3, 10),
(9, 1, 6),
(9, 2, 10),
(10, 1, 4),
(10, 2, 8),
(11, 2, 2),
(12, 3, 10),
(12, 5, 1),
(12, 6, 4),
(13, 2, 4),
(13, 6, 8),
(13, 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `event_schedule`
--

CREATE TABLE `event_schedule` (
  `EventID` int(11) NOT NULL,
  `VenueID` int(11) NOT NULL,
  `SlotID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_schedule`
--

INSERT INTO `event_schedule` (`EventID`, `VenueID`, `SlotID`) VALUES
(1, 1, 1),
(5, 1, 5),
(9, 1, 9),
(10, 1, 10),
(13, 1, 13),
(7, 2, 7),
(12, 2, 12),
(3, 3, 3),
(8, 3, 8),
(14, 3, 14),
(4, 4, 4),
(2, 5, 2),
(6, 5, 6),
(15, 5, 15),
(11, 6, 11);

-- --------------------------------------------------------

--
-- Table structure for table `resource`
--

CREATE TABLE `resource` (
  `ResourceID` int(11) NOT NULL,
  `Name` varchar(120) NOT NULL,
  `Type` varchar(60) NOT NULL DEFAULT 'Other'
) ;

--
-- Dumping data for table `resource`
--

INSERT INTO `resource` (`ResourceID`, `Name`, `Type`) VALUES
(1, 'Projector & Screen', 'AV'),
(2, 'PA System & Microphones', 'AV'),
(3, 'Round Tables & Chairs', 'Furniture'),
(4, 'Buffet Serving Stations', 'Catering'),
(5, 'Photo Booth', 'Other'),
(6, 'Stage Lighting', 'AV'),
(7, 'Dance Floor', 'Furniture');

-- --------------------------------------------------------

--
-- Table structure for table `timeslot`
--

CREATE TABLE `timeslot` (
  `SlotID` int(11) NOT NULL,
  `EventDate` date NOT NULL,
  `StartTime` time NOT NULL,
  `EndTime` time NOT NULL
) ;

--
-- Dumping data for table `timeslot`
--

INSERT INTO `timeslot` (`SlotID`, `EventDate`, `StartTime`, `EndTime`) VALUES
(1, '2026-03-10', '10:00:00', '14:00:00'),
(2, '2026-03-15', '14:00:00', '18:00:00'),
(3, '2026-03-20', '09:00:00', '12:00:00'),
(4, '2026-04-15', '09:00:00', '12:00:00'),
(5, '2026-04-15', '13:00:00', '17:00:00'),
(6, '2026-04-15', '18:00:00', '21:00:00'),
(7, '2026-04-16', '10:00:00', '14:00:00'),
(8, '2026-04-16', '15:00:00', '19:00:00'),
(9, '2026-04-17', '09:00:00', '13:00:00'),
(10, '2026-04-17', '14:00:00', '18:00:00'),
(11, '2026-04-18', '11:00:00', '15:00:00'),
(12, '2026-04-20', '10:00:00', '14:00:00'),
(13, '2026-04-22', '09:00:00', '13:00:00'),
(14, '2026-04-29', '10:00:00', '14:00:00'),
(15, '2026-05-06', '11:00:00', '15:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `Name` varchar(120) NOT NULL,
  `Email` varchar(120) NOT NULL,
  `RoleName` varchar(20) NOT NULL DEFAULT 'requester',
  `password` varchar(255) NOT NULL
) ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `Name`, `Email`, `RoleName`, `password`) VALUES
(1, 'Admin User', 'admin@festival.edu', 'admin', 'admin123'),
(2, 'John Organizer', 'john@festival.edu', 'organiser', 'org123'),
(3, 'Sarah Planner', 'sarah@festival.edu', 'organiser', 'org123'),
(4, 'Alice Johnson', 'alice@festival.edu', 'requester', 'att123'),
(5, 'Bob Smith', 'bob@festival.edu', 'requester', 'att123'),
(6, 'Carol White', 'carol@festival.edu', 'requester', 'att123'),
(7, 'David Brown', 'david@festival.edu', 'requester', 'att123'),
(8, 'Emma Davis', 'emma@festival.edu', 'requester', 'att123'),
(9, 'Frank Miller', 'frank@festival.edu', 'requester', 'att123'),
(10, 'Grace Wilson', 'grace@festival.edu', 'requester', 'att123');

-- --------------------------------------------------------

--
-- Table structure for table `venue`
--

CREATE TABLE `venue` (
  `VenueID` int(11) NOT NULL,
  `Name` varchar(120) NOT NULL,
  `MaxCapacity` int(11) NOT NULL DEFAULT 300,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venue`
--

INSERT INTO `venue` (`VenueID`, `Name`, `MaxCapacity`, `IsActive`) VALUES
(1, 'Grand Ballroom', 300, 1),
(2, 'Garden Pavilion', 150, 1),
(3, 'Riverside Hall', 200, 1),
(4, 'Executive Suite', 50, 1),
(5, 'Outdoor Meadow', 300, 1),
(6, 'Rooftop Terrace', 120, 1);

--
-- Triggers `venue`
--
DELIMITER $$
CREATE TRIGGER `trg_venue_deactivated` AFTER UPDATE ON `venue` FOR EACH ROW BEGIN
    IF OLD.IsActive = TRUE AND NEW.IsActive = FALSE THEN
        UPDATE event e
        JOIN event_schedule es ON e.EventID = es.EventID
        SET e.Status = 'CANCELLED'
        WHERE es.VenueID = NEW.VenueID
          AND e.Status   = 'CONFIRMED';
    END IF;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`BookingID`),
  ADD UNIQUE KEY `uq_booking_user_event` (`UserID`,`EventID`),
  ADD KEY `fk_booking_venue` (`VenueID`),
  ADD KEY `fk_booking_event` (`EventID`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`EventID`),
  ADD KEY `fk_event_organizer` (`OrganizerID`);

--
-- Indexes for table `event_resource`
--
ALTER TABLE `event_resource`
  ADD PRIMARY KEY (`EventID`,`ResourceID`),
  ADD KEY `fk_er_resource` (`ResourceID`);

--
-- Indexes for table `event_schedule`
--
ALTER TABLE `event_schedule`
  ADD PRIMARY KEY (`EventID`),
  ADD UNIQUE KEY `uq_es_slot` (`SlotID`),
  ADD UNIQUE KEY `uq_es_venue_slot` (`VenueID`,`SlotID`);

--
-- Indexes for table `resource`
--
ALTER TABLE `resource`
  ADD PRIMARY KEY (`ResourceID`);

--
-- Indexes for table `timeslot`
--
ALTER TABLE `timeslot`
  ADD PRIMARY KEY (`SlotID`),
  ADD UNIQUE KEY `uq_timeslot` (`EventDate`,`StartTime`,`EndTime`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `uq_user_email` (`Email`);

--
-- Indexes for table `venue`
--
ALTER TABLE `venue`
  ADD PRIMARY KEY (`VenueID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `BookingID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `EventID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resource`
--
ALTER TABLE `resource`
  MODIFY `ResourceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `timeslot`
--
ALTER TABLE `timeslot`
  MODIFY `SlotID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `venue`
--
ALTER TABLE `venue`
  MODIFY `VenueID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `fk_booking_event` FOREIGN KEY (`EventID`) REFERENCES `event` (`EventID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_booking_venue` FOREIGN KEY (`VenueID`) REFERENCES `venue` (`VenueID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `fk_event_organizer` FOREIGN KEY (`OrganizerID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `event_resource`
--
ALTER TABLE `event_resource`
  ADD CONSTRAINT `fk_er_event` FOREIGN KEY (`EventID`) REFERENCES `event` (`EventID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_er_resource` FOREIGN KEY (`ResourceID`) REFERENCES `resource` (`ResourceID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `event_schedule`
--
ALTER TABLE `event_schedule`
  ADD CONSTRAINT `fk_es_event` FOREIGN KEY (`EventID`) REFERENCES `event` (`EventID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_es_slot` FOREIGN KEY (`SlotID`) REFERENCES `timeslot` (`SlotID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_es_venue` FOREIGN KEY (`VenueID`) REFERENCES `venue` (`VenueID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
