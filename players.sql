CREATE DATABASE players_db;
USE players_db;
CREATE TABLE players(id INT AUTO_INCREMENT PRIMARY KEY, players_email VARCHAR(100) UNIQUE, players_name VARCHAR(100),players_score INT(9)
);