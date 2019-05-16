CREATE DATABASE workhorse;

CREATE USER 'workhorse'@'localhost' IDENTIFIED BY 'workhorsepassword';

GRANT ALL PRIVILEGES ON workhorse.* TO 'workhorse'@'localhost';

USE workhorse;

CREATE TABLE Users (Id INT, Email VARCHAR(100), Password VARCHAR(32));

SET password
FOR 'workhorse'@'localhost' = password('workhorsepassword')