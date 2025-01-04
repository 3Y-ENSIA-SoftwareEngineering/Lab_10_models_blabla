CREATE DATABASE expense_tracker;

USE expense_tracker;

-- Login Table
CREATE TABLE login (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Profile Table
CREATE TABLE profile (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    full_name VARCHAR(100),
    email VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES login(id)
);

-- Add/Delete Expense Table
CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    category VARCHAR(50),
    title VARCHAR(100),
    amount DECIMAL(10, 2),
    date DATE,
    FOREIGN KEY (user_id) REFERENCES login(id)
);

-- Shared Expense Table
CREATE TABLE shared_expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT,
    shared_with INT,
    expense_id INT,
    FOREIGN KEY (owner_id) REFERENCES login(id),
    FOREIGN KEY (shared_with) REFERENCES login(id),
    FOREIGN KEY (expense_id) REFERENCES expenses(id)
);
