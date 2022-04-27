SET profiling =0 ;

#DROP TABLE IF EXISTS Images;
#DROP TABLE IF EXISTS Plants;
#DROP TABLE IF EXISTS Species;
#DROP TABLE IF EXISTS Genus;
#DROP TABLE IF EXISTS Family;

CREATE TABLE IF NOT EXISTS Family (
		id INT NOT NULL AUTO_INCREMENT,
		name VARCHAR(100),
		PRIMARY KEY(id),
		CONSTRAINT unique_family UNIQUE(name) 
	);

CREATE TABLE IF NOT EXISTS Genus (
		id INT NOT NULL AUTO_INCREMENT,
		family_id INT,
		name VARCHAR(100),
		PRIMARY KEY(id),
		FOREIGN KEY ( family_id ) REFERENCES Family(id) ON DELETE CASCADE,
		CONSTRAINT unique_genus UNIQUE(name) 
	);

CREATE TABLE IF NOT EXISTS Species (
		id INT NOT NULL AUTO_INCREMENT,
		genus_id INT,
		name VARCHAR(100),
		PRIMARY KEY(id),
		FOREIGN KEY ( genus_id ) REFERENCES Genus(id) ON DELETE CASCADE,
		CONSTRAINT unique_genus UNIQUE(name) 
	);

CREATE TABLE IF NOT EXISTS Plants (
		id INT NOT NULL AUTO_INCREMENT,
		family INT NOT NULL,
		genus INT NOT NULL, 
		species INT NOT NULL,
		common VARCHAR(100),
		PRIMARY KEY(id),
		FOREIGN KEY ( family ) REFERENCES Family(id) ON DELETE CASCADE,
		FOREIGN KEY ( genus ) REFERENCES Genus(id) ON DELETE CASCADE,
		FOREIGN KEY ( species ) REFERENCES Species(id) ON DELETE CASCADE,
		CONSTRAINT UNIQUE KEY( family, genus, species) 
	);

	-- pubmeuk_wp789.Images definition

CREATE TABLE IF NOT EXISTS Images (
		id INT(11) NOT NULL AUTO_INCREMENT,
		image VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		plant_id INT(11) DEFAULT NULL,
		orientation INT(11) DEFAULT NULL,
		date DATETIME DEFAULT NULL,
		location POINT DEFAULT NULL,
		PRIMARY KEY (id),
		UNIQUE KEY image (image),
		KEY plant_id (plant_id)
);

CREATE OR REPLACE INDEX Family_name ON Family (name);
CREATE OR REPLACE INDEX Genus_name ON Genus (name);
CREATE OR REPLACE INDEX Species_name ON Species (name);

DELIMITER &&
DROP PROCEDURE IF EXISTS FirstDate
&&
CREATE PROCEDURE IF NOT EXISTS  `FirstDate`()
	BEGIN
		SELECT min(Images.`date` ) from Images;
	END
&&

DROP PROCEDURE IF EXISTS GetFamilies
&&

CREATE PROCEDURE IF NOT EXISTS `GetFamilies`()
	#NO SQL
	BEGIN
		SELECT f.name FROM Family f;
	END
&&

DROP PROCEDURE IF EXISTS GetGenus
&&

CREATE PROCEDURE IF NOT EXISTS `GetGenus`(IN f JSON)
	#NO SQL
	BEGIN
		DROP TABLE IF EXISTS t;
		CREATE TEMPORARY TABLE t(family VARCHAR(100));
		SET @i = 0;
		label1: LOOP
				INSERT INTO t (family) (
					SELECT REPLACE (JSON_EXTRACT(f,CONCAT('$[',@i,']')),'"','')
				); 
			SET @i = @i + 1;
			IF @i > JSON_LENGTH(f)-1 THEN
					LEAVE label1;
			END IF;
		END LOOP label1;
		SELECT g.name AS name from Genus g
			JOIN Family f ON f.id = g.family_id
			WHERE f.name IN (SELECT family FROM t) ;
		DROP TABLE t;
	END
&&

DROP PROCEDURE IF EXISTS GetSpecies
&&

CREATE PROCEDURE IF NOT EXISTS `GetSpecies`(IN s JSON)
	#NO SQL
	BEGIN
		DROP TABLE IF EXISTS t;
		CREATE TEMPORARY TABLE t(genus VARCHAR(100));
		SET @i = 0;
		label1: LOOP
				INSERT INTO t (genus) (
					SELECT REPLACE (JSON_EXTRACT(s,CONCAT('$[',@i,']')),'"','')
				);
			SET @i = @i + 1;
			IF @i > JSON_LENGTH(s)-1 THEN
					LEAVE label1;
			END IF;
		END LOOP label1;
		
		SELECT s.name AS name from Species s
			JOIN Genus g ON g.id = s.genus_id 
			WHERE g.name IN (SELECT genus FROM t);
		DROP TABLE t;
	END
&&

DROP PROCEDURE IF EXISTS CreateTempTables
&&

CREATE PROCEDURE IF NOT EXISTS `CreateTempTables`(IN s JSON)
	#NO SQL
	BEGIN
		SET @families = JSON_EXTRACT(s,'$.familySelected');
		SET @genus = JSON_EXTRACT(s,'$.genusSelected');
		SET @species = JSON_EXTRACT(s,'$.speciesSelected');
		SET @from = REPLACE (JSON_EXTRACT(s,'$.from'),'"','');
		SET @to = REPLACE (JSON_EXTRACT(s,'$.to'),'"','');
		#SET @map = REPLACE (JSON_EXTRACT(s,'$.map'),'"','');
	
		#SELECT ISNULL(area) , @families,@genus IS NULL,@species IS NULL,@from,@to,JSON_LENGTH(@genus);
	
		DROP TABLE IF EXISTS t_f;
		CREATE TEMPORARY TABLE t_f(family VARCHAR(100));
		DROP TABLE IF EXISTS t_g;
		CREATE TEMPORARY TABLE t_g(genus VARCHAR(100));
		DROP TABLE IF EXISTS t_s;
		CREATE TEMPORARY TABLE t_s(species VARCHAR(100));

		IF NOT (@families IS NULL) THEN
			SET @i = 0;
			label1: LOOP
					INSERT INTO t_f (family) (
						SELECT REPLACE (JSON_EXTRACT(@families,CONCAT('$[',@i,']')),'"','')
					);
				IF @i >= JSON_LENGTH(@families)-1 THEN
						LEAVE label1;
				END IF;
				SET @i = @i + 1;
			END LOOP label1;
		END IF;
		
		IF NOT (@genus IS NULL) THEN
			SET @i = 0;
			label2: LOOP
					INSERT INTO t_g (genus) (
						SELECT REPLACE (JSON_EXTRACT(@genus,CONCAT('$[',@i,']')),'"','')
					);
				IF @i >= JSON_LENGTH(@genus)-1 THEN
						LEAVE label2;
				END IF;
				SET @i = @i + 1;
			END LOOP label2;
		END IF;
	#SELECT * FROM t_g;
	#SELECT "Solanum" IN (SELECT genus FROM t_g);
		IF NOT (@species IS NULL) THEN
			SET @i = 0;
			label3: LOOP
					INSERT INTO t_s (species) (
						SELECT REPLACE (JSON_EXTRACT(@species,CONCAT('$[',@i,']')),'"','')
					);
				IF @i >= JSON_LENGTH(@genus)-1 THEN
						LEAVE label3;
				END IF;
				SET @i = @i + 1;
			END LOOP label3;
		END IF;
	#SELECT * FROM t_s;

	END
&&

DROP PROCEDURE IF EXISTS GetPictures
&&

#CREATE PROCEDURE IF NOT EXISTS `GetPictures`(IN s JSON, IN area POLYGON, IN o INTEGER, IN c INTEGER, IN i INTEGER)
CREATE PROCEDURE IF NOT EXISTS `GetPictures`(IN s JSON, IN area POLYGON, IN o INTEGER, IN c INTEGER, IN i INTEGER)
	#NO SQL
	BEGIN
		SET @families = JSON_EXTRACT(s,'$.familySelected');
		SET @genus = JSON_EXTRACT(s,'$.genusSelected');
		SET @species = JSON_EXTRACT(s,'$.speciesSelected');
		SET @from = REPLACE (JSON_EXTRACT(s,'$.from'),'"','');
		SET @to = REPLACE (JSON_EXTRACT(s,'$.to'),'"','');

		CALL CreateTempTables(s); 

		SELECT  * FROM (
	  	SELECT Images.id AS 'image_id', Images.image, Images.orientation, Plants.id,
		Family.name AS 'fname',
		Genus.name AS 'gname',
		Species.name AS 'sname',
		Plants.common, Images.date,
	  	REGEXP_REPLACE( ST_AsText(Images.location),"^POINT\\((.+) (.+)\\)","\\1,\\2") AS location
		FROM Images
		JOIN Plants ON Images.plant_id = Plants.id
		JOIN Family ON Plants.family = Family.id
		JOIN Genus ON Plants.genus = Genus.id
		JOIN Species ON Plants.species = Species.id
		WHERE Images.`date` > @from
		AND Images.`date` <= DATE_ADD(@to, INTERVAL 1 DAY)
		AND (Family.name IN (SELECT family FROM t_f) OR ISNULL(@families))
		AND Family.id = Plants.family
		AND (Genus.name IN (SELECT genus FROM t_g) OR ISNULL(@genus))
		AND Genus.id = Plants.genus
		AND (Species.name IN (SELECT species FROM t_s) OR ISNULL(@species))
		AND Species.id = Plants.species
		AND (ISNULL(area) OR ST_CONTAINS(area, Images.location))
		#AND Images.id >= i
		ORDER BY Images.id
		#LIMIT c OFFSET o
		)  AS t ORDER BY t.`date` 
	;
	END
&&
DROP PROCEDURE IF EXISTS GetImagesWithoutLocation
&&
CREATE PROCEDURE IF NOT EXISTS `GetImagesWithoutLocation`()
	#NO SQL
	BEGIN
		SELECT i.id, i.image FROM Images i
		WHERE i.location IS NULL;
	END
&&

DROP PROCEDURE IF EXISTS `AddLocationToImage`
&&

CREATE PROCEDURE IF NOT EXISTS `AddLocationToImage`(IN i INTEGER, IN l POINT)
	#NO SQL
	BEGIN
		UPDATE Images
		SET Images.location = l
		WHERE id = i;
	END
&&

DROP PROCEDURE IF EXISTS `GetRandomImage`
&&

CREATE PROCEDURE IF NOT EXISTS `GetRandomImage`()
	#NO SQL
	BEGIN
		SET @C=0;
		SET @N=0;
		SELECT COUNT(i.id) FROM Images i INTO @N;
		select FLOOR(RAND()*@N) INTO @c;
		SELECT CONCAT("SELECT * From Images i order by i.id LIMIT ",@c) INTO @SQL;
		SELECT CONCAT(@SQL,",1;") INTO @SQL;
		PREPARE stmt FROM @SQL;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;

	END
&&



DELIMITER ;

/*
&&

CALL GetPictures('{"familySelected":["Saxifragaceae","Solanaceae"],"genusSelected":["Solanum"],"from":"2020-04-10","to":"2021-01-09"}', NULL) 
&&
#CALL GetPictures('{"familySelected":["Saxifragaceae","Solanaceae"],"from":"2020-04-10","to":"2021-01-09"}') 
CALL GetPictures('{"from":"2020-04-10","to":"2021-01-09"}', ST_GEOMFROMTEXT('POLYGON((51.262989030960796 -0.5288915097817393, 51.262989030960796 -0.6696538388833018, 51.21140017256014 -0.6696538388833018,51.21140017256014 -0.5288915097817393,51.262989030960796 -0.5288915097817393))')) 
&&
CALL GetPictures('{"from":"2020-04-10","to":"2021-01-09"}', NULL) 
&&
*/
CALL GetRandomImage();
#CALL GetPictures('{"from":"2020-03-01","to":"2022-04-25"}', NULL,0,5,10);
#CALL GetPictures('{"familySelected":["Leguminosae"],"from":"2020-03-15","to":"2021-02-26"}',ST_GEOMFROMTEXT('POLYGON((51.262989030960796 -0.5288915097817393, 51.262989030960796 -0.6696538388833018, 51.21140017256014 -0.6696538388833018,51.21140017256014 -0.5288915097817393,51.262989030960796 -0.5288915097817393))'),0,3);
#CALL GetPictures('{"from":"2021-02-01","to":"2021-03-30"}',ST_GEOMFROMTEXT('POLYGON((51.262989030960796 -0.5288915097817393, 51.262989030960796 -0.6696538388833018, 51.21140017256014 -0.6696538388833018,51.21140017256014 -0.5288915097817393,51.262989030960796 -0.5288915097817393))'),0,1);
#CALL GetPictures('{"from":"2021-03-01","to":"2021-03-27"}', (ST_GeomFromText('')),16,1);
