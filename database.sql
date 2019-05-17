CREATE DATABASE workhorse;

CREATE USER 'workhorse'@'localhost' IDENTIFIED BY 'workhorsepassword';

GRANT ALL PRIVILEGES ON workhorse.* TO 'workhorse'@'localhost';

USE workhorse;

CREATE TABLE Users (Id INT NOT NULL AUTO_INCREMENT, Email VARCHAR(100), Password VARCHAR(32), PRIMARY KEY (Id));

CREATE TABLE VirtualMachines  (Id INT NOT NULL AUTO_INCREMENT,
                              IdUser INT NOT NULL, 
                              Name VARCHAR(100) NOT NULL,
                              Path VARCHAR(100) NOT NULL,
                              Subdomain VARCHAR(100) NOT NULL,                              
                              StartDate DATE NOT NULL,
                              Active TINYINT NOT NULL,
                              PRIMARY KEY (Id)
                              );

CREATE TABLE AccountBalance (
                            Id INT NOT NULL AUTO_INCREMENT,
                            IdUser INT NOT NULL,
                            Balance INT NOT NULL,
                            PRIMARY KEY(Id)
                            )

SET password
FOR 'workhorse'@'localhost' = password('workhorsepassword')