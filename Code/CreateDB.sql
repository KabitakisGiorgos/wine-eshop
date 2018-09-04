CREATE DATABASE winesDb CHARACTER SET utf8;

CREATE TABLE Clients( 
    c_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    password VARCHAR(10) NOT NULL,
    email VARCHAR(30) NOT NULL UNIQUE,
    surname VARCHAR(30) NOT NULL,
    phoneNo VARCHAR(10) NOT NULL,
    address VARCHAR(30) NOT NULL,
    accNo VARCHAR(15) NOT NULL UNIQUE,
    debt FLOAT(9,2) NOT NULL DEFAULT 0
);

CREATE TABLE Merchants(
	c_id int NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(c_id),
    FOREIGN KEY (c_id)  REFERENCES Clients (c_id) ON DELETE CASCADE
);

CREATE TABLE Users(
	c_id int NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(c_id),
    FOREIGN KEY (c_id)  REFERENCES Clients (c_id) ON DELETE CASCADE
);

CREATE TABLE Orders(
	o_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    c_id_FK int NOT NULL,
    date DATE NOT NULL,
    state_credited BOOLEAN NOT NULL,
    state_paid BOOLEAN NOT NULL,
    amount FLOAT(7,2) NOT NULL,
    remain FLOAT(7,2) NOT NULL,
    FOREIGN KEY (c_id_FK)  REFERENCES Clients (c_id)
);

CREATE TABLE Wines(
    p_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(500) NOT NULL,
    name VARCHAR(50) NOT NULL,
    color VARCHAR(10) NOT NULL,
    year YEAR NOT NULL,
    winery VARCHAR(50) NOT NULL,
    photo LONGBLOB NOT NULL,
    saleNo INT(5) NOT NULL DEFAULT 0,
    retail  FLOAT(5,2) NOT NULL,
    wholesale FLOAT(5,2) NOT NULL
);

CREATE TABLE Payments(
    c_id_FK int NOT NULL,
    ο_id_FK int NOT NULL,
	pay_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
	amount FLOAT(7,2) NOT NULL,
    date DATE NOT NULL,
    FOREIGN KEY(c_id_FK) REFERENCES Clients (c_id),
    FOREIGN KEY(ο_id_FK) REFERENCES Orders (o_id)
);


CREATE TABLE Contains(
    p_id_FK int NOT NULL,
    ο_id_FK int NOT NULL,
    amount INT(5) DEFAULT 0,
	PRIMARY KEY(p_id_FK,ο_id_FK),
    FOREIGN KEY(p_id_FK) REFERENCES Wines (p_id),
    FOREIGN KEY(ο_id_FK) REFERENCES Orders (o_id)
);

CREATE TABLE Varieties(
    variety VARCHAR(20) NOT NULL,
    p_id_FK int NOT NULL,
	PRIMARY KEY(p_id_FK,variety),
	FOREIGN KEY(p_id_FK) REFERENCES Wines (p_id)
);

CREATE TABLE Returns(
    c_id_FK int NOT NULL,
    ο_id_FK int NOT NULL,
	r_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    FOREIGN KEY(c_id_FK) REFERENCES Clients (c_id),
    FOREIGN KEY(ο_id_FK) REFERENCES Orders (o_id)
);