#DROP TABLE IF EXISTS `Sessions`;
#DROP TABLE IF EXISTS `Blog`;
#DROP TABLE IF EXISTS `Users`;
#DROP TABLE IF EXISTS `Permissions`;
#DROP TABLE IF EXISTS `Cookies`;

DELIMITER &&

DROP PROCEDURE IF EXISTS AddUser ;
&&
CREATE PROCEDURE IF NOT EXISTS  `pubmeuk_wp789`.`AddUser`(IN u TEXT, IN e TEXT, IN h TEXT, IN `uuid` varchar(32))
	BEGIN
		SET @n:=0;
		SELECT @n := COUNT(`user`) FROM Users WHERE `email` = e;
		IF @n = 0 THEN
			INSERT INTO Users (`user`, email, password, `uuid`) VALUES (u, e, h, `uuid`);
		ELSE
			UPDATE Users SET password = h WHERE `email` = e;
		END IF;
	END
&&

DROP PROCEDURE IF EXISTS RegisterUser ;
&&
CREATE PROCEDURE IF NOT EXISTS  `pubmeuk_wp789`.`RegisterUser`(IN u varchar(32))
	BEGIN
		SET @n:=0;
		SELECT COUNT(`user`) INTO @n FROM Users WHERE `uuid` = u;
		IF @n = 0 THEN
			SELECT 'fail' AS 'result';
		ELSE
			UPDATE Users SET registered = TRUE WHERE `uuid` = u;
			SELECT 'success' AS 'result';
		END IF;
	END
&&


DROP PROCEDURE IF EXISTS CheckUser ;
&&
CREATE PROCEDURE IF NOT EXISTS  `pubmeuk_wp789`.`CheckUser`(IN e TEXT, IN h TEXT, IN cookie_id VARCHAR(32))
	BEGIN
		
		SET @n:=0;
		SET @res:="";
		#SELECT @n := COUNT(`user`) FROM Users WHERE `user` = u INTO @temp;
		SELECT COUNT(`email`) INTO @n FROM Users WHERE `email` = e AND registered=TRUE;
		IF @n = 0 THEN
			SET @res:="nouser";
		ELSE 
			#SELECT @res := `password` FROM Users WHERE `user` = u INTO @temp;
			SELECT `password`,`id` INTO @res, @uid FROM Users WHERE `email` = e;
		END IF;
		SELECT @res AS 'result';
	END
&&

DROP PROCEDURE IF EXISTS SetUserIdOnCookie;
&&
CREATE PROCEDURE IF NOT EXISTS  `pubmeuk_wp789`.`SetUserIdOnCookie`(IN e TEXT,IN cookie_id VARCHAR(32))
	BEGIN
		SET @n:=0;
		SET @res:="";

		SET @cid:=0;
		SET @uid:=0;
		SET @cuid:=0;
		SELECT COUNT(`email`) INTO @n FROM Users WHERE `email` = e AND registered=TRUE;
		IF @n = 0 THEN
			SET @res:="nouser";
		ELSE 
			SELECT `id` INTO @uid FROM Users WHERE `email` = e;
			SELECT c.user_id INTO @cuid FROM Cookies c WHERE c.cookie_id = cookie_id;
			IF @cuid IS NULL THEN 
				UPDATE Cookies c SET c.user_id = @uid WHERE c.cookie_id = cookie_id ;
				SET @res:="ok";
			ELSE 
				SET @res:="nocookie";
			END IF;
		END IF;
		SELECT @res AS 'result';

	END
&&


DROP PROCEDURE IF EXISTS IsUser ;
&&
CREATE PROCEDURE IF NOT EXISTS  `pubmeuk_wp789`.`IsUser`(IN e TEXT)
	BEGIN
		
		SET @n:=0;
		SET @res:="";
		SELECT COUNT(`email`) INTO @n FROM Users WHERE `email` = e;
		IF @n = 0 THEN
			SET @res:="nouser";
		ELSE 
			SET @res:="exists";
		END IF;
		SELECT @res AS 'result';
	END
&&

DROP PROCEDURE IF EXISTS GetPermissions ;
&&
CREATE PROCEDURE IF NOT EXISTS  `pubmeuk_wp789`.`GetPermissions`(IN e TEXT)
	BEGIN
		
		SET @n:=0;
		SET @res:="";
		SELECT p.upload_images ,p.can_blog  FROM Permissions p
		JOIN Users u WHERE `email` = e;
	END
&&

DROP PROCEDURE IF EXISTS NewCookie;
&&
CREATE PROCEDURE IF NOT EXISTS  `pubmeuk_wp789`.`NewCookie`(IN c VARCHAR(32))
	BEGIN
		INSERT INTO Cookies (cookie_id,set_time ,overpass_place_name,overpass_scale) VALUES (c,Now(),'city,london',0.5);
	END
&&



DROP PROCEDURE IF EXISTS Cookie;
&&
CREATE PROCEDURE IF NOT EXISTS  `pubmeuk_wp789`.`Cookie`(IN c VARCHAR(32))
	BEGIN
		UPDATE Cookies SET set_time = now() WHERE cookie_id = c;
		SELECT * FROM Cookies WHERE Cookies.cookie_id = c;
	END
&&

DROP PROCEDURE IF EXISTS DeleteOldCookies;
&&
CREATE PROCEDURE IF NOT EXISTS  `pubmeuk_wp789`.`DeleteOldCookies`()
	BEGIN
		DELETE FROM Cookies WHERE Cookies.set_time + INTERVAL 1 MONTH  < now();
	END
&&

DROP PROCEDURE IF EXISTS OverPassCookie;
&&
CREATE PROCEDURE IF NOT EXISTS  `pubmeuk_wp789`.`OverPassCookie`(IN c VARCHAR(32), IN `scale` FLOAT , IN `place` TEXT)
	BEGIN
		UPDATE Cookies SET Cookies.overpass_scale = `scale`, Cookies.overpass_place_name =`place` WHERE Cookies.cookie_id = c;
	END
&&


DELIMITER ;

CREATE TABLE IF NOT EXISTS  `Blog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `persist` boolean,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text` TEXT  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `Permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  upload_images boolean,
  can_blog boolean,
  PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS  `Users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(100) COLLATE utf8mb4_unicode_ci,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci UNIQUE,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permissions` int(11),
  `type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `registered` BOOLEAN DEFAULT FALSE,
  `uuid` varchar(32) DEFAULT NULL,
  `loggedin` BOOLEAN DEFAULT FALSE,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`permissions`) REFERENCES `Permissions` (id)
);


CREATE TABLE IF NOT EXISTS  `Sessions` (
  	`id` int(11) NOT NULL AUTO_INCREMENT,
	user_id int(11) NOT NULL,
	session_id TEXT(20),
	PRIMARY KEY (id),
	FOREIGN KEY ( user_id ) REFERENCES Users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS  `Cookies` (
  	`id` int(11) NOT NULL AUTO_INCREMENT,
	user_id int(11),
	cookie_id VARCHAR(32) NOT NULL,
	set_time DATETIME,
	overpass_place_name TEXT,
	overpass_scale FLOAT,
	PRIMARY KEY (id),
	FOREIGN KEY ( user_id ) REFERENCES Users(id) ON DELETE CASCADE,
	CONSTRAINT unique_cookie_id UNIQUE (cookie_id)
);

CREATE OR REPLACE INDEX cookie_id ON Cookies (cookie_id);

#CALL AddUser ("ken","hello");
#SELECT * FROM `Users`;
#CALL AddUser ("ken","goodby");
#CALL AddUser ('Ken','ken@pub.me.uk','hello','0102030405060708090a0b0c0d0e0f00');
#INSERT INTO Permissions (upload_images , can_blog) VALUES (true, true);
#SELECT * from Permissions p ;
#UPDATE Users SET Users.permissions = 1 where Users.email like 'ken@pub.me.uk' ; 
#CALL IsUser("ken@pub.me.uk");
#SELECT * FROM `Users`;
#CALL CheckUser ("ken","ken@pub.me.uk");
#DELETE FROM Users WHERE TRUE;
#CALL GetPermissions ("ken@pub.me.uk");
#CALL NewCookie('0102030405060738090a0b0c0d0e0f00');
#SELECT * FROM Cookies;
#CALL DeleteOldCookies ();
#CALL Cookie('0102030405060708090a0b0c0d0e0f00');
#delete from Cookies WHERE true;
#CALL NewCookie('bbd3c059337c8ab7b178fa7b3112f34c');
#CALL OverPassCookie('bbd3c059337c8ab7b178fa7b3112f34c',.8,'manchaster');
#SELECT * FROM Cookies;