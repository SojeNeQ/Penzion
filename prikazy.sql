-- Active: 1675944818254@@127.0.0.1@3306@penzion
CREATE DATABASE penzion DEFAULT CHARACTER SET = 'utf8mb4';

CREATE TABLE stranka (
    id VARCHAR(255) PRIMARY KEY,
    titulek VARCHAR(255),
    menu VARCHAR(255),
    obrazek VARCHAR(255),
    obsah TEXT,
    poradi INT UNSIGNED
);

SHOW TABLES;
DESC stranka;

INSERT INTO stranka SET id="test2", titulek="test titulek", menu="test-menu", obsah="test test test", poradi=1;

SELECT * FROM stranka;

UPDATE stranka SET id="koala" WHERE id="test";

DELETE FROM stranka WHERE id="";

SELECT * FROM stranka ORDER BY poradi DESC LIMIT 1;
SELECT MAX(poradi) AS max_hodnota FROM stranka;

SELECT * FROM stranka ORDER BY poradi;