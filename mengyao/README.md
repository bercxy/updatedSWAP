# Project Draft - PHP Web Application

## Table of Contents
1. [Project Overview](#project-overview)
2. [Features](#features)
3. [Project Structure](#project-structure)
4. [Setup Instructions](#setup-instructions)
5. [Running the Application](#running-the-application)
6. [Unit Testing (PHPUnit)](#unit-testing-phpunit)
7. [Security Testing](#security-testing)
8. [Troubleshooting](#troubleshooting)
9. [Learning Resources](#learning-resources)

---

## Project Overview
This project is a PHP-based web application that demonstrates admin creating, retrieving, updating, deleting users from users and user_requests atble in amc_db. 
It is designed for educational purposes to practice **PHP CRUD operations, database interactions, unit testing, and security testing**.

---

## Features
- User registration and login
- Admin approval of user requests
- Edit and update user accounts
- Input validation (contact number, password confirmation)
- Secure password hashing (`password_hash`)
- Unit testing using PHPUnit
- Prepared statements to prevent SQL Injection
- Ensures each user is directed to the right page based on their roles

---

## Project Structure
projectDraft/
├── config/
│ └── config.php ← Database connection & base URL configuration
├── tests/ ← Unit tests for each module
│ ├── AdminRequestTest.php ← Admin request approval tests
│ ├── EditUserTest.php ← User edit/update tests
│ ├── LoginTest.php ← Login tests (valid/invalid credentials)
│ └── RequestTest.php ← User request insertion & validation tests
├── pages/ ← Application pages (login.php, edit_user.php, etc.)
├── css/ ← Stylesheets
├── database.sql ← SQL script to create database/tables
└── README.md


---

## Setup Instructions

1. **Start XAMPP**  
   - Open XAMPP Control Panel
   - Start Apache and MySQL  

2. **Copy Project**  
   - Place the project folder in `C:\xampp\htdocs\` 

3. **Create Database**  
   - Open [phpMyAdmin](http://localhost/phpmyadmin)
   - Click **SQL** and run `database.sql`  
   - Verify database (`AMC_db`) is created  

4. **Configure Base URL**  
   - Open `config/config.php`  
   - Set `$base = '/projectDraft';` if your folder name is `projectDraft`  

5. **Open the Website**  
   - Go to: [http://localhost/projectDraft/](http://localhost/projectDraft/)

---

## Running the Application
- Login as **admin** or **user** (use test credentials if provided in database.sql)
- Navigate through pages to create requests, approve users, or update accounts
- All CRUD operations are supported  

---

## Unit Testing (PHPUnit)
1. Make sure PHP is installed and accessible:  
C:\xampp\php\php.exe -v

2. Download PHPUnit PHAR and place in tools/phpunit.phar

3. Run tests:
C:\xampp\php\php.exe tools\phpunit.phar --bootstrap ./config/config.php ./tests

4. Ensure test result is 100%
