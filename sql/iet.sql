-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 17, 2023 at 12:44 AM
-- Server version: 8.0.18
-- PHP Version: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `iet`
--
CREATE DATABASE IF NOT EXISTS `iet` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `iet`;

-- --------------------------------------------------------

--
-- Table structure for table `bank_accounts`
--

DROP TABLE IF EXISTS `bank_accounts`;
CREATE TABLE IF NOT EXISTS `bank_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `account_balance` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash`
--

DROP TABLE IF EXISTS `cash`;
CREATE TABLE IF NOT EXISTS `cash` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `cash_balance` decimal(15,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expensecategories`
--

DROP TABLE IF EXISTS `expensecategories`;
CREATE TABLE IF NOT EXISTS `expensecategories` (
  `ExpenseCategoryID` int(11) NOT NULL,
  `ExpenseCategoryName` varchar(50) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ExpenseCategoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `expensecategories`
--

INSERT INTO `expensecategories` (`ExpenseCategoryID`, `ExpenseCategoryName`, `Description`) VALUES
(1, 'Housing', 'Expenses related to accommodation.'),
(2, 'Utilities', 'Bills for essential services like electricity, water.'),
(3, 'Transportation', 'Costs associated with commuting or owning a vehicle.'),
(4, 'Food and Groceries', 'Expenses for purchasing food and household supplies.'),
(5, 'Healthcare', 'Medical expenses, insurance, prescriptions, etc.'),
(6, 'Debt Payments', 'Payments for loans, credit cards, or other debts.'),
(7, 'Entertainment', 'Costs for leisure activities, subscriptions, etc.'),
(8, 'Personal Care', 'Expenses for clothing, grooming, personal items.'),
(9, 'Education', 'Costs related to education, tuition fees, supplies.'),
(10, 'Savings and Investments', 'Funds set aside for savings, retirement, or investments.'),
(11, 'Charitable Donations', 'Contributions to charitable organizations.'),
(12, 'Taxes', 'Payments made for income, property, or sales taxes.'),
(13, 'Insurance', 'Premiums paid for various insurance policies.'),
(14, 'Childcare/Child Expenses', 'Costs related to childcare, education for children.');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `category` varchar(100) NOT NULL,
  `spent_using` varchar(100) NOT NULL,
  `remarks` varchar(100) DEFAULT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `bank_account_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `fk_expenses_bank_accounts` (`bank_account_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `income`
--

DROP TABLE IF EXISTS `income`;
CREATE TABLE IF NOT EXISTS `income` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `received_as` varchar(100) NOT NULL,
  `remarks` varchar(100) DEFAULT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `bank_account_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `fk_bank_account_id` (`bank_account_id`)
) ENGINE=MyISAM AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `incomecategories`
--

DROP TABLE IF EXISTS `incomecategories`;
CREATE TABLE IF NOT EXISTS `incomecategories` (
  `IncomeCategoryID` int(11) NOT NULL,
  `IncomeCategoryName` varchar(50) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`IncomeCategoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `incomecategories`
--

INSERT INTO `incomecategories` (`IncomeCategoryID`, `IncomeCategoryName`, `Description`) VALUES
(1, 'Salary/Wages', 'Regular income from employment.'),
(2, 'Self-Employment Income', 'Earnings from freelance work, consulting, or business.'),
(3, 'Interest Income', 'Money earned from interest on savings, investments, bonds.'),
(4, 'Dividend Income', 'Income earned from owning stocks that pay dividends.'),
(5, 'Rental Income', 'Earnings from renting out property or real estate.'),
(6, 'Royalties', 'Income generated from licensing intellectual property.'),
(7, 'Commission Income', 'Earnings from sales or transactions with commissions.'),
(8, 'Pension/Retirement Income', 'Regular payments received upon retirement.'),
(9, 'Capital Gains', 'Profit from selling an investment or asset.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `preferred_currency` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `mobile_number` (`mobile_number`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
