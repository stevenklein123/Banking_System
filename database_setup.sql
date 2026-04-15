-- Create Database
CREATE DATABASE IF NOT EXISTS banking_system;
USE banking_system;

-- Users Table (Admin, Tellers)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id VARCHAR(50) UNIQUE NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teller') NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Customers Table
CREATE TABLE IF NOT EXISTS customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id VARCHAR(50) UNIQUE NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(50) NOT NULL,
    zip_code VARCHAR(10) NOT NULL,
    date_of_birth DATE NOT NULL,
    id_type ENUM('passport', 'driver_license', 'national_id', 'voter_id') NOT NULL,
    id_number VARCHAR(100) NOT NULL,
    occupation VARCHAR(100),
    signature_reference VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Accounts Table
CREATE TABLE IF NOT EXISTS accounts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    account_number VARCHAR(20) UNIQUE NOT NULL,
    customer_id INT NOT NULL,
    account_type ENUM('savings', 'checking', 'business') DEFAULT 'savings',
    balance DECIMAL(15, 2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive', 'frozen') DEFAULT 'active',
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);

-- Transactions Table
CREATE TABLE IF NOT EXISTS transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    account_id INT NOT NULL,
    transaction_type ENUM('deposit', 'withdrawal', 'transfer', 'loan_payment', 'auto_debit') NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    balance_after DECIMAL(15, 2) NOT NULL,
    description VARCHAR(255),
    teller_id INT,
    payment_method ENUM('cash', 'check', 'card', 'transfer') DEFAULT 'cash',
    reference_number VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id),
    FOREIGN KEY (teller_id) REFERENCES users(id)
);

-- Loans Table
CREATE TABLE IF NOT EXISTS loans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    loan_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT NOT NULL,
    principal_amount DECIMAL(15, 2) NOT NULL,
    interest_rate DECIMAL(5, 2) NOT NULL DEFAULT 5.00,
    loan_term_months INT NOT NULL DEFAULT 12,
    collateral_description VARCHAR(255),
    collateral_value DECIMAL(15, 2),
    purpose VARCHAR(255) NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'active', 'closed') DEFAULT 'pending',
    approved_by INT,
    approval_date DATETIME,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (approved_by) REFERENCES users(id)
);

-- Loan Payments Table
CREATE TABLE IF NOT EXISTS loan_payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    loan_id INT NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_method VARCHAR(50),
    reference_number VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (loan_id) REFERENCES loans(id)
);

-- Auto-Debit Records Table
CREATE TABLE IF NOT EXISTS auto_debits (
    id INT PRIMARY KEY AUTO_INCREMENT,
    account_id INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    frequency ENUM('daily', 'weekly', 'monthly') DEFAULT 'monthly',
    status ENUM('active', 'paused', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id)
);

-- Checks Clearing Table
CREATE TABLE IF NOT EXISTS checks_clearing (
    id INT PRIMARY KEY AUTO_INCREMENT,
    check_number VARCHAR(20) NOT NULL,
    account_id INT NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    payee_name VARCHAR(100) NOT NULL,
    check_date DATE NOT NULL,
    status ENUM('pending', 'cleared', 'bounced') DEFAULT 'pending',
    cleared_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id)
);

-- Insert Sample Admin User
INSERT INTO users (employee_id, username, password, role, full_name, email, phone) 
VALUES ('EMP001', 'admin', '$2y$10$O9w6IEPvrgXEABZc7SFRg.hFWzz9HkdOKuM8hPXvQvbJ5EdCpVzAm', 'admin', 'Admin User', 'admin@bank.com', '1234567890');

-- Insert Sample Teller User
INSERT INTO users (employee_id, username, password, role, full_name, email, phone) 
VALUES ('EMP002', 'teller1', '$2y$10$O9w6IEPvrgXEABZc7SFRg.hFWzz9HkdOKuM8hPXvQvbJ5EdCpVzAm', 'teller', 'John Teller', 'teller@bank.com', '0987654321');

-- Insert Sample Customer
INSERT INTO customers (customer_id, first_name, middle_name, last_name, email, phone, address, city, state, zip_code, date_of_birth, id_type, id_number, occupation, status) 
VALUES ('CUST001', 'Robert', 'John', 'Smith', 'robert@email.com', '5551234567', '123 Main Street', 'New York', 'NY', '10001', '1990-05-15', 'national_id', 'ID123456789', 'Engineer', 'active');

-- Insert Sample Account
INSERT INTO accounts (account_number, customer_id, account_type, balance, status) 
VALUES ('ACC000001', 1, 'savings', 5000.00, 'active');

-- Create indexes for better performance
CREATE INDEX idx_customer_id ON accounts(customer_id);
CREATE INDEX idx_account_id ON transactions(account_id);
CREATE INDEX idx_teller_id ON transactions(teller_id);
CREATE INDEX idx_transaction_date ON transactions(created_at);
CREATE INDEX idx_loan_customer ON loans(customer_id);
CREATE INDEX idx_loan_status ON loans(status);
CREATE INDEX idx_username ON users(username);
CREATE INDEX idx_account_number ON accounts(account_number);
