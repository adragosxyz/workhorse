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

-- #PENTRUTESTE
INSERT INTO Users(Email, Password) VALUES('test@test.com', 'test');
INSERT INTO Users(Email, Password) VALUES('popescu@test.com', 'test');                         
INSERT INTO Users(Email, Password) VALUES('ion@test.com', 'test');
INSERT INTO AccountBalance(IdUser, Balance) VALUES (1, 10000);
INSERT INTO AccountBalance(IdUser, Balance) VALUES (2, 250);                                             
INSERT INTO AccountBalance(IdUser, Balance) VALUES (3, 10);
                                                                                                           
INSERT INTO VirtualMachines(
IdUser,
Name,
Path,
PrivateIP,
Subdomain,
StartDate,
LastPaidDate,
Active,
Price)
VALUES (1, 'VM1', '/vms/VM1', '192.168.33.34', 'test1', NOW(), NOW(), 1, 100);

INSERT INTO VirtualMachines(
IdUser,
Name,
Path,
PrivateIP,
Subdomain,
StartDate,
LastPaidDate,
Active,
Price)
VALUES (1, 'VM2', '/vms/VM2', '192.168.33.35', 'test2', NOW(), NOW(), 1, 200);

INSERT INTO VirtualMachines(
IdUser,
Name,
Path,
PrivateIP,
Subdomain,
StartDate,
LastPaidDate,
Active,
Price)
VALUES (2, 'VM3', '/vms/VM3', '192.168.33.36', 'test3', NOW(), NOW(), 1, 100);

INSERT INTO VirtualMachines(
IdUser,
Name,
Path,
PrivateIP,
Subdomain,
StartDate,
LastPaidDate,
Active,
Price)
VALUES (3, 'VM4', '/vms/VM4', '192.168.33.37', 'test4', NOW(), NOW(), 1, 100);
-- #PENTRUTESTE                               
                                                                                                           

-- de adaugat
-- Transactions, SSHKeys
-- 


CREATE EVENT e_hourly
    ON SCHEDULE
      EVERY 1 HOUR
    COMMENT 'Substracts the price of machine from the users balance  if the virtual machine is active'
    DO
      UPDATE AccountBalance AS AB -- ADUNAM TOATE VM-URILE PE CARE LE ARE USER-UL
INNER JOIN (
  SELECT IdUser, SUM(Price) as SumPrice 
  FROM VirtualMachines 
  WHERE Active=1 AND TIMESTAMPDIFF(HOUR, LastPaidDate, NOW()) > 0
  GROUP BY IdUser
) AS VM ON AB.IdUser=VM.IdUser
SET AB.Balance=AB.Balance-VM.SumPrice;
  
UPDATE VirtualMachines as VM -- DACA NU MAI ARE BANI SA PLATEASCA IN CONTINUARE INCHIDEM VM-UL
INNER JOIN AccountBalance as AB
ON VM.IdUser=AB.IdUser
SET VM.Active=0
WHERE TIMESTAMPDIFF(HOUR, LastPaidDate, NOW()) > 0 AND (AB.Balance - VM.Price < 0);
  
UPDATE VirtualMachines  -- SCHIMBAM DATA ULTIMA PLATI
SET LastPaidDate = NOW()
WHERE TIMESTAMPDIFF(HOUR, LastPaidDate, NOW()) > 0 AND Active = 1;
                    

UPDATE AccountBalance -- NU LASAM USER-UL CU BALANTA NEGATIVA
SET Balance = 0
WHERE Balance < 0;
    UPDATE AccountBalance AS ab INNER JOIN VirtualMachines as vm ON vm.IdUser=ab.IdUser SET ab.Balance = (ab.Balance-vm.Price);
    UPDATE VirtualMachines AS vm INNER JOIN AccountBalance ON vm.IdUser = ab.IdUser AS ab SET vm.Active = IF(ab.Balance > 0, 1, 0); 



SET password
FOR 'workhorse'@'localhost' = password('workhorsepassword');