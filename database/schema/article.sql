DROP TABLE IF EXISTS XRef;
DROP TABLE IF EXISTS Articles;
DROP TABLE IF EXISTS Paragraphs;
DROP PROCEDURE IF EXISTS Paragraph;
DROP PROCEDURE IF EXISTS GetArticle;
CREATE TABLE `Articles` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `Paragraphs` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`text` TEXT,
	PRIMARY KEY (`id`)
);

CREATE TABLE `XRef` (
	`Article_id` INT(11),
	`Paragraph_id` INT(11),
	FOREIGN KEY ( Article_id ) REFERENCES Articles(id) ON DELETE CASCADE,
	FOREIGN KEY ( Paragraph_id ) REFERENCES Paragraphs(id) ON DELETE CASCADE
);
DELIMITER &&
CREATE PROCEDURE IF NOT EXISTS `Paragraph`(IN name VARCHAR(100), IN t TEXT)
	#NO SQL
	BEGIN
		SET @a_id = (SELECT COUNT(id) FROM Articles 
			WHERE Articles.name = name);

		IF @a_id = 0 THEN
			INSERT INTO Articles (name) VALUE (name);
			SET @a_id = LAST_INSERT_ID();
		END IF;

		INSERT INTO Paragraphs (`text`) VALUE (t);
		SET @p_id = LAST_INSERT_ID();


		INSERT INTO XRef(Article_id, Paragraph_id) VALUES (@a_id,@p_id);

	END
&&
CREATE PROCEDURE IF NOT EXISTS `GetArticle`(IN name VARCHAR(100))
	#NO SQL
	BEGIN
		SELECT * FROM Articles a
		JOIN Paragraphs p
		JOIN XRef x on x.Paragraph_id = p.id AND x.Article_id = a.id
		WHERE a.name = name;
	END
&&
DELIMITER ;
CALL Paragraph('Moles', 'Most allotmenter\'s do not like to see moles, horrid things that leave piles of soil about, mainly amongst the veg.');
CALL Paragraph('Moles', 'We actually quite like them, good for churning the soil and they don\'t actually eat your crops as they are carnivorous.');
CALL Paragraph('Moles', 'This little chap was wondering about on the allotment, bold a brass, in broad daylight.');
CALL Paragraph('Moles', 'It was very dry after a period of drought and so was probably thirsty. Ended up in the strawberrys, hopefully not resorting to some fruit.');
CALL GetArticle('Moles');


