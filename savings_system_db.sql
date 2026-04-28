CREATE DATABASE banking_system;

USE banking_system;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL
);

CREATE TABLE accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    balance DECIMAL(10,2),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT,
    type VARCHAR(20),
    amount DECIMAL(10,2),
    date DATETIME,
    FOREIGN KEY (account_id) REFERENCES accounts(id)
);