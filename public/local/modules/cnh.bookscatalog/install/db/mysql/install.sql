CREATE TABLE IF NOT EXISTS `cnh_bookscatalog_author` (
	`ID` int unsigned NOT NULL AUTO_INCREMENT,
	`NAME` varchar(255) NOT NULL,
	`LAST_NAME` varchar(255) NOT NULL,
	PRIMARY KEY(`ID`)
);

CREATE TABLE IF NOT EXISTS `cnh_bookscatalog_book` (
	`ID` int unsigned NOT NULL AUTO_INCREMENT,
	`ISBNCODE` varchar(255) NOT NULL,
	`EDITIONS_ISBN` text NOT NULL,
	`TITLE` varchar(255) NOT NULL,
	`PUBLISH_DATE` date NOT NULL,
	`AUTHOR_ID` int NOT NULL,
	PRIMARY KEY(`ID`)
);

CREATE TABLE IF NOT EXISTS `cnh_bookscatalog_tag` (
	`ID` int unsigned NOT NULL AUTO_INCREMENT,
	`NAME` varchar(255) NOT NULL,
	PRIMARY KEY(`ID`)
);

CREATE TABLE IF NOT EXISTS `cnh_bookscatalog_book_to_tag` (
	`BOOK_ID` int NOT NULL,
	`TAG_ID` int NOT NULL,
	PRIMARY KEY(`BOOK_ID`, `TAG_ID`)
);
