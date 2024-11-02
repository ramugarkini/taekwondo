-- Adminer 4.8.1 MySQL 10.4.32-MariaDB dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `individual_entry_form`;
CREATE TABLE `individual_entry_form` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('sub_junior','cadet','junior','senior') NOT NULL,
  `category` enum('Individual','Pair','Group') NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `weight` int(11) NOT NULL,
  `weight_category` varchar(255) NOT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `state_organization_name` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `parent_guardian_name` varchar(100) DEFAULT NULL,
  `current_belt_grade` varchar(50) DEFAULT NULL,
  `tfi_id_no` varchar(50) DEFAULT NULL,
  `belt_certificate_no` varchar(50) DEFAULT NULL,
  `academic_qualification` varchar(100) DEFAULT NULL,
  `name_of_school` varchar(100) DEFAULT NULL,
  `board_university_name` varchar(100) DEFAULT NULL,
  `signature_parent_guardian_path` varchar(255) DEFAULT NULL,
  `signature_participant_path` varchar(255) DEFAULT NULL,
  `signature_president_secretary_path` varchar(255) DEFAULT NULL,
  `state_association_stamp_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `individual_entry_form` (`id`, `type`, `category`, `gender`, `weight`, `weight_category`, `photo_path`, `name`, `state_organization_name`, `date_of_birth`, `age`, `parent_guardian_name`, `current_belt_grade`, `tfi_id_no`, `belt_certificate_no`, `academic_qualification`, `name_of_school`, `board_university_name`, `signature_parent_guardian_path`, `signature_participant_path`, `signature_president_secretary_path`, `state_association_stamp_path`) VALUES
(1, 'sub_junior', 'Individual', 'Male', 25, '1',  'uploads/photo/1/1.jpg',  'John Doe', 'State Association 1',  '2010-05-10', 14, 'Mr. Doe',  'Yellow', 'TFI12345', 'BC12345',  '10th Grade', 'ABC School', 'XYZ Board',  'uploads/signatures/parent_guardian/1/1.jpg', 'uploads/signatures/participant/1/1.jpg', 'uploads/signatures/president_secretary/1/1.jpg', 'uploads/stamp/1/1.jpg'),
(2, 'cadet',  'Pair', 'Female', 40, '2',  '', 'Jane Smith', 'State Association 2',  '2008-08-15', 16, 'Mrs. Smith', 'Green',  'TFI67890', 'BC67890',  '12th Grade', 'DEF School', 'XYZ Board',  '', '', '', '');

-- 2024-11-02 09:58:17