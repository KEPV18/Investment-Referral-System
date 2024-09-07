CREATE DATABASE investment_platform;

USE investment_platform;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    phone VARCHAR(20) UNIQUE,
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    cash_wallet VARCHAR(20),
    balance DECIMAL(10, 2) DEFAULT 0,
    referral_bonus DECIMAL(10, 2) DEFAULT 0,
    daily_bonus_date DATE
);

CREATE TABLE investments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    amount DECIMAL(10, 2),
    return DECIMAL(10, 2),
    end_date DATE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE referrals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    referrer_id INT,
    referred_id INT,
    FOREIGN KEY (referrer_id) REFERENCES users(id),
    FOREIGN KEY (referred_id) REFERENCES users(id)
);
