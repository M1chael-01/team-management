CREATE DATABASE IF NOT EXISTS team_management_users;
USE team_management_users;

CREATE TABLE user (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name TEXT NOT NULL,
  email TEXT NOT NULL,
  password TEXT NOT NULL,
  role TEXT NOT NULL,
  acountID INT(11) NOT NULL,
  last_active TEXT NOT NULL,
  team TEXT NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
