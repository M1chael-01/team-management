CREATE DATABASE IF NOT EXISTS team_management_tasks;
USE team_management_tasks;

CREATE TABLE task (
  id INT(11) NOT NULL AUTO_INCREMENT,
  acountID INT(11) NOT NULL,
  teamID INT(11) NOT NULL,
  title TEXT NOT NULL,
  status TEXT NOT NULL,
  team TEXT NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
