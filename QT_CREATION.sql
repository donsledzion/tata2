CREATE TABLE Permissions 	(	IDPermissions int NOT NULL AUTO_INCREMENT,
								Name varchar(30) UNIQUE,
								P_read boolean,
								P_write boolean,
								P_share boolean,
								PRIMARY KEY(IDPermissions)
								);
								
CREATE TABLE Accounts		( 	IDAccount int NOT NULL AUTO_INCREMENT,								
								Name varchar(20) NOT NULL,
								Avatar varchar(30),
								PRIMARY KEY (IDAccount)
								);
								
								
CREATE TABLE Users			(	IDUser int NOT NULL AUTO_INCREMENT,
								Email varchar(50) UNIQUE NOT NULL,
								First_name varchar(30) NOT NULL,
								Last_name varchar(35),
								Password varchar(1024) NOT NULL,
								Avatar varchar(30),
								PRIMARY KEY (IDUser)								
								);
								
CREATE TABLE AccountsUsersPermissions(	IDAccount int NOT NULL,
										IDUser int NOT NULL,
										IDPermissions int NOT NULL,
										PRIMARY KEY (IDAccount, IDUser),
										FOREIGN KEY (IDAccount) REFERENCES Accounts(IDAccount),
										FOREIGN KEY (IDUser) REFERENCES Users(IDUser)
										);
										
CREATE TABLE Kids			(	IDKid int NOT NULL AUTO_INCREMENT,
								IDAccount int NOT NULL,
								First_name varchar(30) NOT NULL,
								Last_name varchar(35),
								Dim_name varchar(30),
								Birth_date date,
								About LONGTEXT,
								Gender varchar(6),
								Default_pic varchar(30),
								PRIMARY KEY (IDKid),
								FOREIGN KEY (IDAccount) REFERENCES Accounts(IDAccount)
								);
								
CREATE TABLE Posts			(	IDPost int NOT NULL AUTO_INCREMENT,
								Datestamp datetime NOT NULL,
								Quote_date datetime NOT NULL,
								IDAuthor int NOT NULL,
								IDKid int NOT NULL,
								Sentence LONGTEXT NOT NULL,
								Picture varchar(30),
								PRIMARY KEY (IDPost),
								FOREIGN KEY (IDAuthor) REFERENCES Users(IDUser),
								FOREIGN KEY (IDKid) REFERENCES Kids(IDKid)
								);
								
CREATE TABLE PasswordReset	(	IDRequest int NOT NULL AUTO_INCREMENT,
								Email varchar(50) NOT NULL,
								Token varchar(1024) NOT NULL,
								Expiration datetime NOT NULL,
								PRIMARY KEY(IDRequest)
								); 								
								
								
CREATE TABLE AccountRequest (	IDRequest INT NOT NULL AUTO_INCREMENT,
								User_email varchar(50) NOT NULL,
								User_first_name varchar(30) NOT NULL,
								User_last_name varchar(35) NOT NULL,
								User_password varchar(1024) NOT NULL,
								User_avatar varchar(30),
								IDAccount INT,
								IDPermissions INT,
								Account_name varchar(20),
								Account_avatar varchar(30),
								Token varchar(1024),
								Expiration datetime NOT NULL,
								PRIMARY KEY(IDRequest),
								FOREIGN KEY(IDAccount) REFERENCES Accounts(IDAccount),
								FOREIGN KEY(IDPermissions) REFERENCES Permissions(IDPermissions)
								);								
								
CREATE TABLE InnerInvitations (	IDInvitation INT NOT NULL AUTO_INCREMENT,
								IDAccount 	INT NOT NULL,
								IDInviting 	INT NOT NULL,
								IDInvited 	INT NOT NULL,
								IDPermissions INT NOT NULL,
								InvitationDate DateTime NOT NULL,
								PRIMARY KEY(IDInvitation),
								FOREIGN KEY(IDAccount) REFERENCES Accounts(IDAccount),
								FOREIGN KEY(IDInviting) REFERENCES Users(IDUser),
								FOREIGN KEY(IDInvited) REFERENCES Users(IDUser),
								FOREIGN KEY(IDPermissions) REFERENCES Permissions(IDPermissions)
								);