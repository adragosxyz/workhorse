

CREATE DATABASE workhorse;

CREATE USER 'workhorse'@'localhost' IDENTIFIED BY 'workhorsepassword';

GRANT ALL PRIVILEGES ON workhorse.* TO 'workhorse'@'localhost';

USE workhorse;

CREATE TABLE Users (Id INT NOT NULL AUTO_INCREMENT, Email VARCHAR(100), Password VARCHAR(32), PRIMARY KEY (Id));

CREATE TABLE VirtualMachines  (Id INT NOT NULL AUTO_INCREMENT,
                              IdUser INT NOT NULL, 
                              Name VARCHAR(100) NOT NULL,
                              Path VARCHAR(100) NOT NULL,
                              PrivateIP VARCHAR(100) NOT NULL,
                              Subdomain VARCHAR(100) NOT NULL,                              
                              StartDate DATETIME NOT NULL,
                              LastPaidDate DATETIME NOT NULL,
                              Active TINYINT NOT NULL,
                              Price INT NOT NULL,
                              PRIMARY KEY (Id)
                              );

CREATE TABLE AccountBalance (
                            Id INT NOT NULL AUTO_INCREMENT,
                            IdUser INT NOT NULL,
                            Balance INT NOT NULL,
                            PRIMARY KEY(Id)
                            );


CREATE TABLE SSHKeys(Id INT NOT NULL AUTO_INCREMENT,
                     IdUser INT NOT NULL, 
                     SSHKey VARCHAR(1024),
                     PRIMARY KEY (Id)
                      );

-- de adaugat
-- Transactions, SSHKeys
-- 

/*
CREATE EVENT e_hourly
    ON SCHEDULE
      EVERY 1 HOUR
    COMMENT 'Substracts the price of machine from the user's balance if the virtual machine is active
    DO

    UPDATE AccountBalance AS ab INNER JOIN VirtualMachines as vm ON vm.IdUser=ab.IdUser SET ab.Balance = (ab.Balance-vm.Price);
    UPDATE VirtualMachines AS vm INNER JOIN AccountBalance ON vm.IdUser = ab.IdUser AS ab SET vm.Active = IF(ab.Balance > 0, 1, 0); 
*/


SET password
FOR 'workhorse'@'localhost' = password('workhorsepassword');