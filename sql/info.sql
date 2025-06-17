-- Create the database
CREATE DATABASE IF NOT EXISTS `team_management_calendar` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `team_management_calendar`;

-- Create the `info` table
CREATE TABLE IF NOT EXISTS `info` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `acountID` INT(11) NOT NULL,
  `eventTitle` TEXT NOT NULL,
  `date` DATE NOT NULL,
  `timeStart` TIME NOT NULL,
  `timeEnd` TIME NOT NULL,
  `person` TEXT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
