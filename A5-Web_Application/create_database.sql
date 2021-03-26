drop database airlineDB;
create database airlineDB;

CREATE TABLE Airline (
    AirlineCode char(2) NOT NULL, -- Assumes the 2 letter carrier codes are used (All airlines I found had both 2 and 3 letter codes)
    AirlineName varchar(255) NOT NULL,
    PRIMARY KEY (AirlineCode)
);

CREATE TABLE AirplaneType (
    AirplaneTypeName varchar(255) NOT NULL,
    MaxSeats int NOT NULL,
    Company varchar(255) NOT NULL,
    PRIMARY KEY (AirplaneTypeName)
);

CREATE TABLE Airplane (
    AirplaneId varchar(255) NOT NULL,
    YearBuilt year,
    AirlineCode char(2) NOT NULL,
    AirplaneTypeName varchar(255) NOT NULL,
    PRIMARY KEY (AirplaneId),
    FOREIGN KEY (AirlineCode) REFERENCES Airline (AirlineCode) ON DELETE CASCADE,
    FOREIGN KEY (AirplaneTypeName) REFERENCES AirplaneType (AirplaneTypeName) ON DELETE CASCADE
);

CREATE TABLE Flight (
    ThreeDigitNumber char(3) NOT NULL, -- check (ThreeDigitNumber between 100 and 999), 
    AirlineCode char(2) NOT NULL,
    AirplaneId varchar(255) NOT NULL,
    PRIMARY KEY (ThreeDigitNumber, AirlineCode),
    FOREIGN KEY (AirlineCode) REFERENCES Airline (AirlineCode) ON DELETE CASCADE,
    FOREIGN KEY (AirplaneId) REFERENCES Airplane (AirplaneId) ON DELETE CASCADE
);

DELIMITER $$
    CREATE TRIGGER FlightThreeDigitNumberCheck 
    BEFORE INSERT ON Flight
    FOR EACH ROW 
    BEGIN 
        IF (NEW.ThreeDigitNumber REGEXP '[0-9]{3}' ) = 0 THEN 
        SIGNAL SQLSTATE '12345'
            SET MESSAGE_TEXT = 'Only three number strings are accepted';
    END IF; 
    END$$
DELIMITER ;

-- For Days Offered (Flight)
CREATE TABLE DayOffered (
    AirlineCode char(2) NOT NULL,
    ThreeDigitNumber char(3) NOT NULL, 
    DayValue varchar(8), -- The maximum number of letters a day can have (in English) is 8 - Thursday
    PRIMARY KEY (AirlineCode, ThreeDigitNumber, DayValue),
    FOREIGN KEY (AirlineCode, ThreeDigitNumber) REFERENCES Flight (AirlineCode, ThreeDigitNumber) ON DELETE CASCADE
);

CREATE TABLE Airport (
    AirportCode char(3) NOT NULL, -- Assumes a 3 letter code
    AirportName varchar(255) NOT NULL,
    City varchar(255) NOT NULL,
    Province varchar(255) NOT NULL,
    PRIMARY KEY (AirportCode)
);

-- For Airplane Type - Supported By - Airport
CREATE TABLE SupportedBy (
    AirportCode varchar(3) NOT NULL,
    AirplaneTypeName varchar(255) NOT NULL,
    PRIMARY KEY (AirportCode, AirplaneTypeName),
    FOREIGN KEY (AirportCode) REFERENCES Airport (AirportCode) ON DELETE CASCADE,
    FOREIGN KEY (AirplaneTypeName) REFERENCES AirplaneType (AirplaneTypeName) ON DELETE CASCADE
);

-- For Flight - Arrives at - Airport
CREATE TABLE ArrivesAt (
    AirlineCode char(2) NOT NULL,
    ThreeDigitNumber char(3) NOT NULL, 
    AirportCode varchar(3) NOT NULL,
    ScheduledArrivalTime time NOT NULL,
    ActualArrivalTime time,
    PRIMARY KEY (AirlineCode, ThreeDigitNumber, AirportCode),
    FOREIGN KEY (AirlineCode, ThreeDigitNumber) REFERENCES Flight (AirlineCode, ThreeDigitNumber) ON DELETE CASCADE,
    FOREIGN KEY (AirportCode) REFERENCES Airport (AirportCode) ON DELETE CASCADE
);

-- For Flight - Departs From - Airport
CREATE TABLE DepartsFrom (
    AirlineCode char(2) NOT NULL,
    ThreeDigitNumber char(3) NOT NULL, 
    AirportCode varchar(3) NOT NULL,
    ScheduledDepartureTime time NOT NULL,
    ActualDepartureTime time,
    PRIMARY KEY (AirlineCode, ThreeDigitNumber, AirportCode),
    FOREIGN KEY (AirlineCode, ThreeDigitNumber) REFERENCES Flight (AirlineCode, ThreeDigitNumber) ON DELETE CASCADE,
    FOREIGN KEY (AirportCode) REFERENCES Airport (AirportCode) ON DELETE CASCADE
);

-- Insert Airline
INSERT INTO Airline VALUES ('AA', 'American Airlines Inc.');
INSERT INTO Airline VALUES ('AC', 'Air Canada');
INSERT INTO Airline VALUES ('AZ', 'Alitalia-Compagnia Aerea Italiana S.P.');
INSERT INTO Airline VALUES ('AF', 'Air France');
INSERT INTO Airline VALUES ('AM', 'AeroMexico');
INSERT INTO Airline VALUES ('PX', 'Air Nippon Co. Ltd.');
INSERT INTO Airline VALUES ('OZ', 'Asiana Airlines Inc.');

-- Insert Airplane Type
INSERT INTO AirplaneType VALUES ('737 NG', 215, 'Boeing');
INSERT INTO AirplaneType VALUES ('737 Classic', 149, 'Boeing');
INSERT INTO AirplaneType VALUES ('001', 368, 'Boeing');
INSERT INTO AirplaneType VALUES ('767', 216, 'Boeing');
INSERT INTO AirplaneType VALUES ('787', 242, 'Boeing');
INSERT INTO AirplaneType VALUES ('757', 243, 'Boeing');
INSERT INTO AirplaneType VALUES ('717', 100, 'Boeing');
INSERT INTO AirplaneType VALUES ('747', 400, 'Boeing');
INSERT INTO AirplaneType VALUES ('A320', 186, 'Airbus');
INSERT INTO AirplaneType VALUES ('A330-200', 247, 'Airbus');
INSERT INTO AirplaneType VALUES ('A330-300', 277, 'Airbus');
INSERT INTO AirplaneType VALUES ('A321', 230, 'Airbus');
INSERT INTO AirplaneType VALUES ('A319', 156, 'Airbus');
INSERT INTO AirplaneType VALUES ('CRJ700', 76, 'Bombardier');
INSERT INTO AirplaneType VALUES ('CRJ900', 100, 'Bombardier');
INSERT INTO AirplaneType VALUES ('BRJ-X', 90, 'Bombardier');

-- Insert Airplane
INSERT INTO Airplane VALUES ('AA1', 1969, 'AA', 'A330-200');
INSERT INTO Airplane VALUES ('AA2', 1995, 'AA', 'BRJ-X');
INSERT INTO Airplane VALUES ('AA3', 1990, 'AA', '747');
INSERT INTO Airplane VALUES ('AA4', 1987, 'AA', 'A330-300');
INSERT INTO Airplane VALUES ('AA5', 2006, 'AA', 'CRJ900');

INSERT INTO Airplane VALUES ('AC1', 1969, 'AC', '737 NG');
INSERT INTO Airplane VALUES ('AC2', 1999, 'AC', 'BRJ-X');
INSERT INTO Airplane VALUES ('AC3', 1990, 'AC', '757');
INSERT INTO Airplane VALUES ('AC4', 1987, 'AC', '717');
INSERT INTO Airplane VALUES ('AC5', 2006, 'AC', 'CRJ900');

INSERT INTO Airplane VALUES ('AZ1', 1969, 'AZ', '737 Classic');
INSERT INTO Airplane VALUES ('AZ2', 1995, 'AZ', 'A319');
INSERT INTO Airplane VALUES ('AZ3', 1990, 'AZ', '747');
INSERT INTO Airplane VALUES ('AZ4', 1987, 'AZ', 'A321');

INSERT INTO Airplane VALUES ('AF1', 1969, 'AF', '737 NG');
INSERT INTO Airplane VALUES ('AF2', 2008, 'AF', 'BRJ-X');
INSERT INTO Airplane VALUES ('AF3', 1990, 'AF', '757');
INSERT INTO Airplane VALUES ('AF4', 1987, 'AF', '767');
INSERT INTO Airplane VALUES ('AF5', 2006, 'AF', '787');

INSERT INTO Airplane VALUES ('AM1', 2011, 'AM', '737 Classic');
INSERT INTO Airplane VALUES ('AM2', 1995, 'AM', 'A319');
INSERT INTO Airplane VALUES ('AM3', 1990, 'AM', '747');

INSERT INTO Airplane VALUES ('PX1', 1969, 'PX', 'A330-200');
INSERT INTO Airplane VALUES ('PX2', 2019, 'PX', 'BRJ-X');
INSERT INTO Airplane VALUES ('PX3', 1990, 'PX', '757');
INSERT INTO Airplane VALUES ('PX4', 1987, 'PX', '717');
INSERT INTO Airplane VALUES ('PX5', 2006, 'PX', 'CRJ900');

INSERT INTO Airplane VALUES ('OZ1', 1969, 'OZ', '737 Classic');
INSERT INTO Airplane VALUES ('OZ2', 1995, 'OZ', 'A319');
INSERT INTO Airplane VALUES ('OZ3', 2021, 'OZ', '757');
INSERT INTO Airplane VALUES ('OZ4', 2020, 'OZ', '767');
INSERT INTO Airplane VALUES ('OZ5', 1998, 'OZ', '787');
INSERT INTO Airplane VALUES ('OZ6', 2000, 'OZ', '747');

-- Insert Flight
INSERT INTO Flight VALUES ('123', "AA", "AA3");
INSERT INTO Flight VALUES ('100', "AA", "AA3");
INSERT INTO Flight VALUES ('299', "AA", "AA3");
INSERT INTO Flight VALUES ('123', "AC", "AC1");
INSERT INTO Flight VALUES ('100', "AZ", "AZ2");
INSERT INTO Flight VALUES ('299', "AF", "AF5");
INSERT INTO Flight VALUES ('999', "AM", "AM3");
INSERT INTO Flight VALUES ('301', "PX", "PX5");
INSERT INTO Flight VALUES ('001', "OZ", "OZ6");

-- Insert Airport
INSERT INTO Airport VALUES ("YYZ", "Lester B. Pearson International Airport", "Toronto", "Ontario");
INSERT INTO Airport VALUES ("HKG", "Chek Lap Kok International Airport", "Hong Kong", "Hong Kong");
INSERT INTO Airport VALUES ("CAM", "Vancouver International Airport", "Vancouver", "British Columbia");


-- Insert DayOffered
INSERT INTO DayOffered VALUES ("AA", '123', "Monday");
INSERT INTO DayOffered VALUES ("AA", '123', "Tuesday");
INSERT INTO DayOffered VALUES ("AA", '123', "Sunday");
INSERT INTO DayOffered VALUES ("AA", '100', "Thursday");
INSERT INTO DayOffered VALUES ("AA", '299', "Friday");
INSERT INTO DayOffered VALUES ("AC", '123', "Monday");
INSERT INTO DayOffered VALUES ("AZ", '100', "Friday");
INSERT INTO DayOffered VALUES ("AF", '299', "Sunday");
INSERT INTO DayOffered VALUES ("AM", '999', "Sunday");
INSERT INTO DayOffered VALUES ("PX", '301', "Saturday");
INSERT INTO DayOffered VALUES ("OZ", '001', "Monday");

-- Insert Arrives At
INSERT INTO ArrivesAt VALUES("AA", '123', "YYZ", "00:00:00", "00:05:00");
INSERT INTO ArrivesAt VALUES("AA", '100', "HKG", "10:00:00", "10:05:00");
INSERT INTO ArrivesAt VALUES("AA", '299', "CAM", "11:00:00", "09:05:00");
INSERT INTO ArrivesAt VALUES("AC", '123', "YYZ", "12:00:00", "12:05:00");
INSERT INTO ArrivesAt VALUES("AZ", '100', "YYZ", "08:00:00", "09:05:00");
INSERT INTO ArrivesAt VALUES("AF", '299', "YYZ", "05:00:00", "04:05:00");
INSERT INTO ArrivesAt VALUES("AM", '999', "HKG", "01:00:00", "01:05:00");
INSERT INTO ArrivesAt VALUES("PX", '301', "YYZ", "09:00:00", "09:05:00");

-- Insert Departs From
INSERT INTO DepartsFrom VALUES("AA", '123', "HKG", "23:00:00", "23:05:00");
INSERT INTO DepartsFrom VALUES("AA", '100', "CAM", "19:00:00", "19:05:00");
INSERT INTO DepartsFrom VALUES("AA", '299', "YYZ", "18:00:00", "19:05:00");
INSERT INTO DepartsFrom VALUES("AC", '123', "HKG", "08:00:00", "8:05:00");
INSERT INTO DepartsFrom VALUES("AZ", '100', "HKG", "18:00:00", "19:05:00");
INSERT INTO DepartsFrom VALUES("AF", '299', "HKG", "02:00:00", "02:05:00");
INSERT INTO DepartsFrom VALUES("AM", '999', "YYZ", "23:00:00", "23:05:00");
INSERT INTO DepartsFrom VALUES("PX", '301', "HKG", "02:00:00", "02:05:00");

-- Insert SupportedBy
INSERT INTO SupportedBy VALUES ("YYZ", '737 NG');
INSERT INTO SupportedBy VALUES ("YYZ", '737 Classic');
INSERT INTO SupportedBy VALUES ("YYZ", '001');
INSERT INTO SupportedBy VALUES ("YYZ", '767');
INSERT INTO SupportedBy VALUES ("YYZ", '787');
INSERT INTO SupportedBy VALUES ("YYZ", '757');
INSERT INTO SupportedBy VALUES ("YYZ", '717');
INSERT INTO SupportedBy VALUES ("YYZ", '747');
INSERT INTO SupportedBy VALUES ("YYZ", 'A320');
INSERT INTO SupportedBy VALUES ("YYZ", 'A330-200');
INSERT INTO SupportedBy VALUES ("YYZ", 'A330-300');
INSERT INTO SupportedBy VALUES ("YYZ", 'A321');
INSERT INTO SupportedBy VALUES ("YYZ", 'A319');
INSERT INTO SupportedBy VALUES ("YYZ", 'CRJ700');
INSERT INTO SupportedBy VALUES ("YYZ", 'CRJ900');
INSERT INTO SupportedBy VALUES ("YYZ", 'BRJ-X');
INSERT INTO SupportedBy VALUES ("CAM", "747");
INSERT INTO SupportedBy VALUES ("HKG", '737 NG');
INSERT INTO SupportedBy VALUES ("HKG", '737 Classic');
INSERT INTO SupportedBy VALUES ("HKG", '001');
INSERT INTO SupportedBy VALUES ("HKG", '767');
INSERT INTO SupportedBy VALUES ("HKG", '787');
INSERT INTO SupportedBy VALUES ("HKG", '757');
INSERT INTO SupportedBy VALUES ("HKG", '717');
INSERT INTO SupportedBy VALUES ("HKG", '747');
INSERT INTO SupportedBy VALUES ("HKG", 'A320');
INSERT INTO SupportedBy VALUES ("HKG", 'A330-200');
INSERT INTO SupportedBy VALUES ("HKG", 'A330-300');
INSERT INTO SupportedBy VALUES ("HKG", 'A321');
INSERT INTO SupportedBy VALUES ("HKG", 'A319');
INSERT INTO SupportedBy VALUES ("HKG", 'CRJ700');
INSERT INTO SupportedBy VALUES ("HKG", 'CRJ900');
INSERT INTO SupportedBy VALUES ("HKG", 'BRJ-X');