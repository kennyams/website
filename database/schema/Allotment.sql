
DROP TABLE IF EXISTS `Furrows`;
DROP TABLE IF EXISTS `Beds`;
DROP TABLE IF EXISTS `Seeds`;
DROP TABLE IF EXISTS `Suppliers`;

-- pubmeuk_wp789.Suppliers definition

CREATE TABLE `Suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- pubmeuk_wp789.Seeds definition

CREATE TABLE `Seeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `variety` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sew` char(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `indoor` char(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plant_out` char(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harvest` char(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flower` char(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  CONSTRAINT `Seeds_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `Suppliers` (`id`)
);
-- pubmeuk_wp789.Furrows definition
-- pubmeuk_wp789.Beds definition

CREATE TABLE `Beds` (
  `id` int(11) NOT NULL,
  `seed_id` int(11) DEFAULT NULL,
  `parimeter` polygon DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seed_id` (`seed_id`),
  CONSTRAINT `Beds_ibfk_1` FOREIGN KEY (`seed_id`) REFERENCES `Seeds` (`id`)
);

CREATE TABLE `Furrows` (
  `seed_id` int(11) DEFAULT NULL,
  `bed_id` int(11) DEFAULT NULL,
  `furrow` linestring DEFAULT NULL,
  KEY `bed_id` (`bed_id`),
  KEY `seed_id` (`seed_id`),
  CONSTRAINT `Furrows_ibfk_1` FOREIGN KEY (`bed_id`) REFERENCES `Beds` (`id`),
  CONSTRAINT `Furrows_ibfk_2` FOREIGN KEY (`seed_id`) REFERENCES `Seeds` (`id`)
);


INSERT INTO `Suppliers` (name) VALUES ('Thompson and Morgan');
INSERT INTO `Seeds` (name,variety,sew,flower,harvest,supplier_id) VALUES ('Broad Bean','Aquadulce Claudia','AAA.......AA','....AAA.....','....AAA.....',1);
SELECT * FROM Suppliers s 
JOIN Seeds s2 on s.id = s2.supplier_id ;

