-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 08, 2026 at 04:11 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint UNSIGNED NOT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `two_fa_secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_fa` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `avatar`, `name`, `email`, `phone`, `password`, `status`, `created_at`, `updated_at`, `two_fa_secret`, `two_fa`) VALUES
(1, NULL, 'Super Admin', 'admin@qunzo.com', NULL, '$2y$12$WbpxsP5oRRAv.WtRjbBVnuTNUZHX04628SFh5IHWsdY5JDUPNp3xi', 1, '2024-11-19 22:45:36', '2025-04-29 11:27:11', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `agents`
--

CREATE TABLE `agents` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `status` enum('pending','approved','rejected','disabled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `beneficiaries`
--

CREATE TABLE `beneficiaries` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `receiver_id` bigint UNSIGNED NOT NULL,
  `account_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `id` bigint UNSIGNED NOT NULL,
  `bill_service_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `data` json NOT NULL,
  `response_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `amount` decimal(10,2) NOT NULL,
  `charge` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','return') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bill_services`
--

CREATE TABLE `bill_services` (
  `id` bigint UNSIGNED NOT NULL,
  `api_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'service id from api',
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'flutterwave, bloc etc',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` json NOT NULL,
  `data` json NOT NULL,
  `amount` int NOT NULL DEFAULT '0',
  `min_amount` int NOT NULL DEFAULT '0',
  `max_amount` int NOT NULL DEFAULT '0',
  `charge` double NOT NULL DEFAULT '0',
  `charge_type` enum('fixed','percentage','flexible','range') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` bigint UNSIGNED NOT NULL,
  `cover` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `locale_id` int DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'en',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `cover`, `title`, `details`, `locale_id`, `locale`, `created_at`, `updated_at`) VALUES
(1, 'global/uploads/blogs/vvUIidL0gqb1oyW6r8ao.png', 'Experience the Power of Modern Payments', '\r\n  <h1>Experience the Power of Modern Payments</h1>\r\n  <p>Meet the new era of money movement — faster, safer, and built for global life. Modern payment systems remove friction, connect local banks to global rails, and make everyday transactions feel instant.</p>\r\n  <h2>Why it matters</h2>\r\n  <p>Customers expect speed and clarity. Businesses need reliable reconciliation and low fees. The right payments stack solves both.</p>\r\n  <ul>\r\n    <li>Real-time settlement options</li>\r\n    <li>Built-in fraud prevention</li>\r\n    <li>Flexible multi-currency support</li>\r\n  </ul>\r\n  <p>Whether you run an e-commerce shop or a marketplace, adopting modern payments is no longer optional — it\'s a competitive edge.</p>\r\n  <p><strong>Get started:</strong> Evaluate your current rails, test an instant-pay partner, and map out a migration plan that keeps customer experience in front.</p>\r\n', 1, 'en', '2025-12-09 10:03:41', '2025-12-09 10:03:41'),
(2, 'global/uploads/blogs/9mgdeELogGtYnq80fVmU.png', 'How Multi-Wallet Systems Simplify Money Management', '<p>&lt;article&gt;</p>\r\n\r\n<p>  &lt;h1&gt;How Multi-Wallet Systems Simplify Money Management&lt;/h1&gt;</p>\r\n\r\n<p>  &lt;p&gt;Multi-wallet platforms let users hold different currencies, stablecoins, or purpose-based wallets (savings, payroll, escrow) in one account — and that simplicity is powerful.&lt;/p&gt;</p>\r\n\r\n<p>  &lt;h2&gt;Benefits for users and businesses&lt;/h2&gt;</p>\r\n\r\n<p>  &lt;ol&gt;</p>\r\n\r\n<p>    &lt;li&gt;Clear separation of funds (personal vs. business)&lt;/li&gt;</p>\r\n\r\n<p>    &lt;li&gt;Instant internal transfers between wallets&lt;/li&gt;</p>\r\n\r\n<p>    &lt;li&gt;Custom rules and limits per wallet for compliance&lt;/li&gt;</p>\r\n\r\n<p>  &lt;/ol&gt;</p>\r\n\r\n<p>  &lt;p&gt;For developers, multi-wallets reduce friction in UX and make feature rollouts (like agent payouts or merchant settlements) faster and safer.&lt;/p&gt;</p>\r\n\r\n<p>  &lt;p&gt;&lt;em&gt;Tip:&lt;/em&gt; Start with three core wallets (Primary, Savings, Transaction) and expand based on user behavior.&lt;/p&gt;</p>\r\n\r\n<p>&lt;/article&gt;</p>\r\n\r\n<div><br></div>', 2, 'en', '2025-12-09 10:04:39', '2025-12-09 10:04:39'),
(3, 'global/uploads/blogs/OjqyzD6S90ONoUYceBio.png', 'Designing Subscription Plans That Users Actually Buy', '\r\n  <h1>Designing Subscription Plans That Users Actually Buy</h1>\r\n  <p>A good subscription plan balances value, clarity, and trust. Pricing complexity is one of the main reasons users abandon the checkout flow.</p>\r\n  <h2>Best practices</h2>\r\n  <ul>\r\n    <li>Keep tiers distinct — don’t overlap features</li>\r\n    <li>Show usage limits clearly (API calls, credits, seats)</li>\r\n    <li>Offer clear upgrade/downgrade paths with prorated billing</li>\r\n  </ul>\r\n  <p>Also consider free trials or usage-based starter tiers to reduce friction for new users. And make billing transparent: send invoices, usage reports, and renewal reminders.</p>\r\n  <p><strong>Pro tip:</strong> Provide an admin panel to manage plan quotas and monitor who’s close to their limits so you can trigger helpful nudges.</p>\r\n', 3, 'en', '2025-12-09 10:05:11', '2025-12-09 10:05:11'),
(4, 'global/uploads/blogs/hO8bZQ33qckOCJQFNjzh.png', 'How Digital Wallets Are Transforming Global Finance', '\r\n  <h1>How Digital Wallets Are Transforming Global Finance</h1>\r\n\r\n  <p>Digital wallets have reshaped the way individuals and businesses interact with money. What started as a simple tool for storing card numbers has evolved into a powerful financial ecosystem that supports payments, investments, identity verification, and even credit scoring.</p>\r\n\r\n  <h2>The Evolution of Digital Wallets</h2>\r\n  <p>Early digital wallets were limited to basic online payments, but today they offer:</p>\r\n  <ul>\r\n    <li>Instant peer-to-peer transfers</li>\r\n    <li>Multi-currency balances</li>\r\n    <li>QR and NFC payments for retail</li>\r\n    <li>Integration with banking and card networks</li>\r\n    <li>Crypto and stablecoin storage options</li>\r\n  </ul>\r\n\r\n  <h2>Why Digital Wallets Are Growing Fast</h2>\r\n  <p>The rapid shift towards cashless economies, driven by mobile penetration and financial inclusion, is accelerating adoption around the world.</p>\r\n\r\n  <h3>1. Speed and Convenience</h3>\r\n  <p>Users can send money, pay bills, buy airtime, or withdraw to bank accounts instantly — all from their phone. No long queues, no paperwork.</p>\r\n\r\n  <h3>2. Enhanced Security</h3>\r\n  <p>Modern wallets provide advanced security layers like:</p>\r\n  <ul>\r\n    <li>Biometric authentication (Face ID, fingerprint)</li>\r\n    <li>Tokenized payments instead of exposing card data</li>\r\n    <li>AI-driven fraud detection</li>\r\n  </ul>\r\n\r\n  <h3>3. Personal Financial Tools</h3>\r\n  <p>Today\'s wallets often include budgeting tools, spending analysis, saving goals, cashback systems, and reward programs.</p>\r\n\r\n  <h3>4. Financial Inclusion</h3>\r\n  <p>In emerging markets, digital wallets have become the primary banking method for millions of unbanked users, giving them access to digital payments, savings, and micro-loans.</p>\r\n\r\n  <h2>Business Value of Digital Wallets</h2>\r\n  <p>For fintech startups and enterprises, offering a wallet system creates a foundation for additional financial services such as loans, investments, subscriptions, and agent networks.</p>\r\n\r\n  <p><strong>Bottom line:</strong> Digital wallets aren\'t just a payment tool — they are becoming central hubs of the global financial system, expanding opportunities for consumers and redefining how money moves worldwide.</p>\r\n', 4, 'en', '2025-12-09 10:06:24', '2025-12-09 10:06:24'),
(5, 'global/uploads/blogs/raUwlS1YfUZUFU8nkpgx.png', 'Why KYC & Compliance Matter in Modern Fintech', '\r\n  <h1>Why KYC &amp; Compliance Matter in Modern Fintech</h1>\r\n\r\n  <p>Fintech companies operate in one of the most tightly regulated industries in the world. While innovation moves quickly, regulators prioritize security, transparency, and consumer protection. This is where KYC (Know Your Customer) becomes essential — not only to meet legal requirements but also to build trust and reduce long-term risk.</p>\r\n\r\n  <h2>Understanding KYC in Fintech</h2>\r\n  <p>KYC ensures that financial institutions verify the identity of users before providing services. It protects the platform from fraud, money laundering, and illegal activity.</p>\r\n\r\n  <h3>KYC Includes:</h3>\r\n  <ul>\r\n    <li>Identity verification (ID, passport, NID, driver’s license)</li>\r\n    <li>Face matching and liveness detection</li>\r\n    <li>Document verification with OCR and AI</li>\r\n    <li>Address and phone verification</li>\r\n  </ul>\r\n\r\n  <h2>Why KYC Is Critical</h2>\r\n\r\n  <h3>1. Preventing Fraud</h3>\r\n  <p>Without KYC, anyone could create multiple fake accounts, use stolen identities, or funnel illegal funds through your platform. KYC dramatically reduces these risks.</p>\r\n\r\n  <h3>2. Building Customer Trust</h3>\r\n  <p>Users feel safer when they know a platform verifies all participants. This leads to stronger customer loyalty and increased transaction volume.</p>\r\n\r\n  <h3>3. Protecting Your Business From Legal Risk</h3>\r\n  <p>Non-compliance can result in huge fines, shutdown orders, and long-term damage to a startup’s reputation. Proper compliance is an investment, not an expense.</p>\r\n\r\n  <h3>4. Unlocking Global Partnerships</h3>\r\n  <p>Banks, card issuers, PSPs, and liquidity providers prefer working with platforms that maintain strict KYC standards.</p>\r\n\r\n  <h2>Modern KYC Technology</h2>\r\n  <p>Fintech companies now use AI-powered verification tools to make KYC fast and seamless. These include:</p>\r\n  <ul>\r\n    <li>Biometric face matching</li>\r\n    <li>Automated document scanning</li>\r\n    <li>Instant risk scoring</li>\r\n    <li>Automated AML checks</li>\r\n  </ul>\r\n\r\n  <p><strong>The future of fintech depends on compliance.</strong> Businesses that embrace KYC early grow faster, avoid regulatory pressure, and create a safe ecosystem for global payments.</p>\r\n', 5, 'en', '2025-12-09 10:06:52', '2025-12-09 10:06:52'),
(6, 'global/uploads/blogs/JgyLRwGHb6VODaDmE5fY.png', 'The Rise of Cross-Border Payments in the Digital Economy', '\r\n  <h1>The Rise of Cross-Border Payments in the Digital Economy</h1>\r\n\r\n  <p>Cross-border payments have historically been slow, expensive, and full of hidden fees. Traditional banking systems rely on correspondent networks, manual reviews, and outdated infrastructure. Today, fintech innovations are changing everything — making global transfers faster, cheaper, and more transparent.</p>\r\n\r\n  <h2>What’s Driving the Demand?</h2>\r\n\r\n  <h3>1. Global Freelancing &amp; Remote Work</h3>\r\n  <p>Millions of freelancers work for international clients and depend on fast global payments. Slow settlement means delayed salaries and cash-flow problems.</p>\r\n\r\n  <h3>2. Growth of Global E-Commerce</h3>\r\n  <p>Businesses sell products internationally and need platforms that can manage multiple currencies, transparent FX rates, and instant settlements.</p>\r\n\r\n  <h3>3. Rise of Digital Nomads</h3>\r\n  <p>People living abroad require cheaper, more flexible solutions than traditional banks can provide.</p>\r\n\r\n  <h3>4. Crypto and Stablecoin Adoption</h3>\r\n  <p>Stablecoins like USDT and USDC have enabled next-generation remittance systems with near-instant settlement and lower fees.</p>\r\n\r\n  <h2>How Fintech Is Improving Cross-Border Transfers</h2>\r\n\r\n  <h3>1. Real-Time Settlement Rails</h3>\r\n  <p>API-based providers like Wise, Circle, and Finage allow fintech apps to process instant payouts across dozens of countries without relying on slow banking layers.</p>\r\n\r\n  <h3>2. Transparent FX Rates</h3>\r\n  <p>Fintech apps now show real-time exchange rates and fees upfront. No hidden charges, no surprise deductions.</p>\r\n\r\n  <h3>3. Multi-Currency Wallets</h3>\r\n  <p>Users can store funds in multiple currencies, convert instantly, and withdraw when needed.</p>\r\n\r\n  <h3>4. Automated AML &amp; Compliance</h3>\r\n  <p>Fintech platforms use AI to detect suspicious transfers and block illegal activity, reducing risk while maintaining speed.</p>\r\n\r\n  <h2>Future of Cross-Border Payments</h2>\r\n  <p>The next decade will bring massive changes, including:</p>\r\n  <ul>\r\n    <li>Full interoperability between banks, wallets, and crypto networks</li>\r\n    <li>Instant global settlements</li>\r\n    <li>AI-driven compliance automation</li>\r\n    <li>Cheaper remittance corridors powered by real-time APIs</li>\r\n  </ul>\r\n\r\n  <p><strong>Conclusion:</strong> Cross-border payments are moving toward instant, low-cost, and fully transparent systems. For fintech companies, this is a huge opportunity to build global-ready financial products.</p>\r\n', 6, 'en', '2025-12-09 10:07:13', '2025-12-09 10:07:13');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cron_jobs`
--

CREATE TABLE `cron_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `next_run_at` timestamp NULL DEFAULT NULL,
  `last_run_at` timestamp NULL DEFAULT NULL,
  `schedule` int DEFAULT NULL,
  `type` enum('system','custom') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('running','paused') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reserved_method` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cron_jobs`
--

INSERT INTO `cron_jobs` (`id`, `name`, `next_run_at`, `last_run_at`, `schedule`, `type`, `status`, `reserved_method`, `url`, `created_at`, `updated_at`) VALUES
(1, 'User Inactive Account Disabled', '2025-11-01 12:45:52', '2025-11-01 12:15:52', 1800, 'system', 'running', 'userInactive', NULL, NULL, '2025-11-01 12:15:52');

-- --------------------------------------------------------

--
-- Table structure for table `cron_job_logs`
--

CREATE TABLE `cron_job_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `cron_job_id` bigint UNSIGNED DEFAULT NULL,
  `error` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `started_at` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('fiat','crypto') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fiat',
  `symbol` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `conversion_rate` decimal(28,8) DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `custom_csses`
--

CREATE TABLE `custom_csses` (
  `id` bigint UNSIGNED NOT NULL,
  `css` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `custom_csses`
--

INSERT INTO `custom_csses` (`id`, `css`, `created_at`, `updated_at`) VALUES
(1, '//The Custom CSS will be added on the site head tag\r\n.site-head-tag {\r\n	margin: 0;\r\n  	padding: 0;\r\n}', '2024-11-19 22:45:36', '2025-03-19 02:11:55');

-- --------------------------------------------------------

--
-- Table structure for table `deposit_methods`
--

CREATE TABLE `deposit_methods` (
  `id` bigint UNSIGNED NOT NULL,
  `gateway_id` bigint UNSIGNED DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('auto','manual') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'manual',
  `gateway_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `charge` decimal(28,8) DEFAULT '0.00000000',
  `charge_type` enum('percentage','fixed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `minimum_deposit` decimal(28,8) DEFAULT NULL,
  `maximum_deposit` decimal(28,8) DEFAULT NULL,
  `currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_symbol` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `field_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payment_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gateways`
--

CREATE TABLE `gateways` (
  `id` bigint UNSIGNED NOT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `supported_currencies` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `credentials` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_withdraw` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gateways`
--

INSERT INTO `gateways` (`id`, `logo`, `name`, `gateway_code`, `supported_currencies`, `credentials`, `is_withdraw`, `status`, `created_at`, `updated_at`) VALUES
(1, 'global/gateway/paypal.png', 'Paypal', 'paypal', '[\"USD\", \"EUR\", \"GBP\", \"CAD\", \"AUD\", \"JPY\", \"SGD\", \"NZD\", \"CHF\", \"SEK\", \"NOK\", \"DKK\", \"PLN\", \"HUF\", \"CZK\", \"ILS\", \"BRL\", \"MXN\", \"HKD\", \"TWD\", \"TRY\", \"INR\", \"RUB\", \"ZAR\", \"MYR\", \"THB\", \"IDR\", \"PHP\", \"NGN\", \"GHS\"]', '{\"client_id\":\"\",\"client_secret\":\"\",\"app_id\":\"\",\"mode\":\"\"}', 'paypal_email', 1, NULL, '2023-06-20 10:51:45'),
(2, 'global/gateway/stripe.png', 'Stripe', 'stripe', '[\"USD\",\"AUD\",\"BRL\",\"CAD\",\"CHF\",\"DKK\",\"EUR\",\"GBP\",\"HKD\",\"INR\",\"JPY\",\"MXN\",\"MYR\",\"NOK\",\"NZD\",\"PLN\",\"SEK\",\"SGD\"]', '{\"stripe_key\":\"\",\"stripe_secret\":\"\"}', '0', 1, NULL, '2022-11-13 01:46:46'),
(3, 'global/gateway/mollie.png', 'Mollie', 'mollie', '[\"EUR\", \"USD\", \"GBP\", \"CAD\", \"AUD\", \"CHF\", \"DKK\", \"NOK\", \"SEK\", \"PLN\", \"CZK\", \"HUF\", \"RON\", \"BGN\", \"HRK\", \"ISK\", \"ZAR\"]', '{\"api_key\":\"\"}', '0', 1, NULL, '2022-10-28 09:43:50'),
(4, 'global/gateway/perfectmoney.png', 'Perfect Money', 'perfectmoney', '[\"USD\", \"EUR\", \"RUB\", \"UAH\"]', '{\"PM_ACCOUNTID\":\"\",\"PM_PASSPHRASE\":\"\",\"PM_MARCHANTID\":\"\",\"PM_MARCHANT_NAME\":\"\"}', 'member_id', 1, NULL, '2023-06-19 07:01:24'),
(5, 'global/gateway/coinbase.png', 'Coinbase', 'coinbase', '[\"USD\", \"EUR\", \"GBP\", \"CAD\", \"AUD\", \"JPY\", \"BTC\", \"ETH\", \"LTC\", \"BCH\", \"XRP\", \"EOS\"]', '{\"apiKey\":\"\",\"account_id\":\"\",\"private_key\":\"\",\"webhookSecret\":\"\",\"apiVersion\":\"\"}', 'email_address', 1, NULL, '2022-10-28 13:16:15'),
(6, 'global/gateway/paystack.png', 'Paystack', 'paystack', '[\"NGN\", \"USD\", \"GBP\", \"EUR\", \"GHS\", \"KES\", \"ZAR\", \"UGX\", \"TZS\", \"RWF\"]', '{\"public_key\":\"\",\"secret_key\":\"\",\"merchant_email\":\"\"}', '0', 1, NULL, '2022-12-17 03:18:45'),
(7, 'global/gateway/voguepay.png', 'Voguepay', 'voguepay', '[\"USD\", \"EUR\", \"GBP\", \"NGN\", \"GHS\", \"KES\", \"ZAR\", \"UGX\", \"TZS\", \"RWF\"]', '{\"merchant_id\":\"\"}', '0', 1, NULL, '2022-11-13 20:08:13'),
(8, 'global/gateway/flutterwave.png', 'Flutterwave', 'flutterwave', '[\"USD\", \"EUR\", \"GBP\", \"NGN\", \"GHS\", \"KES\", \"ZAR\", \"UGX\", \"TZS\", \"RWF\", \"CAD\", \"AUD\", \"JPY\", \"INR\"]', '{\"public_key\":\"\",\"secret_key\":\"\",\"encryption_key\":\"\"}', 'account_bank,account_number', 1, NULL, '2022-12-16 17:06:06'),
(9, 'global/gateway/coingate.png', 'CoinGate', 'coingate', '[\"BTC\", \"ETH\", \"LTC\", \"XRP\", \"BCH\", \"EOS\", \"XLM\", \"XMR\", \"DASH\", \"DOGE\", \"ZEC\"]', '{\"api_token\":\"\"}', '0', 1, NULL, '2022-11-22 16:01:12'),
(10, 'global/gateway/monnify.svg', 'Monnify', 'monnify', '[\"NGN\"]', '{\"api_key\":\"\",\"api_secret\":\"\",\"base_url\":\"\",\"contract_code\":\"\"}', '0', 1, NULL, '2022-12-05 03:02:39'),
(11, 'global/gateway/securionpay.png', 'SecurionPay', 'securionpay', '[\"USD\", \"EUR\", \"GBP\", \"JPY\", \"CAD\", \"AUD\", \"CHF\", \"SEK\", \"NOK\", \"DKK\"]', '{\"public_key\":\"\",\"secret_key\":\"\"}', '0', 1, NULL, '2022-12-07 05:11:19'),
(12, 'global/gateway/coinpayments.svg', 'CoinPayments', 'coinpayments', '["BTC", "BTC.LN", "BCH", "LTC", "VLX", "VLX.Native", "APL", "ASK", "BCN", "BEAM", "BIZZ.TRC20", "BNB", "BNB.BSC", "BTCV", "BTG", "BTT.OLD", "BTT.TRC20", "CELO", "CLOAK", "CRW", "CURE", "cUSD", "USD", "CAD", "EUR", "ARS", "AUD", "AZN", "BGN", "BRL", "BYN", "CHF", "CLP", "CNY", "COP", "CZK", "DKK", "GBP", "GIP", "HKD", "HUF", "IDR", "ILS", "INR", "IRR", "IRT", "ISK", "JPY", "KRW", "LAK", "MKD", "MXN", "ZAR", "MYR", "NGN", "NOK", "NZD", "PEN", "PHP", "PKR", "PLN", "RON", "RUB", "SEK", "SGD", "THB", "TRY", "TWD", "UAH", "VND", "ETH", "TUSD.ERC20", "TON.ERC20", "SHIB.ERC20", "XRP", "SOL", "USDT", "TRX", "DOGE"]', '{"buyer_email":"","public_key":"","private_key":"","ipn_secret":"","marchant_id":""}', '0', 1, NULL, '2023-07-07 20:18:04'),
(13, 'global/gateway/nowpayments.png', 'Nowpayments', 'nowpayments', '[\"BTC\", \"ETH\", \"LTC\", \"BCH\", \"BNB\", \"XRP\", \"USDT\", \"DOGE\", \"ADA\", \"DOT\", \"LINK\", \"XLM\", \"USDC\", \"TRX\", \"ATOM\", \"XTZ\", \"EOS\", \"XMR\", \"ZEC\", \"DASH\"]', '{\"api_key\":\"\",\"secret_key\":\"\"}', '0', 1, NULL, '2023-07-06 21:33:31'),
(14, 'global/gateway/coinremitter.png', 'Coinremitter', 'coinremitter', '[\"BTC\",\"ETH\"]', '{\"api_key\":\"\",\"password\":\"\"}', 'to_address', 1, NULL, '2023-07-06 21:33:31'),
(15, 'global/gateway/cryptomus.png', 'Cryptomus', 'cryptomus', '[\"BCH\",\"BNB\",\"BTC\",\"BUSD\",\"CGPT\",\"DAI\",\"DASH\",\"DOFE\",\"ETH\",\"LTC\",\"MATIC\",\"TON\",\"TRX\",\"USDC\",\"USDT\",\"VERSE\",\"XMR\"]\r\n\r\n', '{\"payment_key\":\"\",\"payout_key\":\"\",\"merchant_id\":\"\"}', 'address', 1, NULL, '2023-07-08 04:20:55'),
(16, 'global/gateway/paymongo.png', 'Paymongo', 'paymongo', '[\"PHP\", \"USD\", \"AUD\", \"CAD\", \"EUR\", \"HKD\", \"JPY\", \"SGD\", \"GBP\"]\n', '{\"public_key\":\"\",\"secret_key\":\"\",\"password\":\"\"}', '0', 1, NULL, '2023-07-10 22:48:12'),
(17, 'global/gateway/btcpayserver.png', 'Btcpayserver', 'btcpayserver', '[\"BTC\", \"ETH\", \"XRP\", \"BCH\", \"LTC\", \"ADA\", \"DOT\", \"LINK\", \"XLM\", \"DOGE\"]', '{\"host\":\"\",\"api_key\":\"\",\"store_id\":\"\",\"webhook_secret\":\"\"}', '0', 1, NULL, '2023-07-06 21:33:31'),
(18, 'global/gateway/binance.svg', 'Binance', 'binance', '[\"BTC\", \"ETH\", \"BNB\", \"XRP\", \"LTC\", \"BCH\", \"ADA\", \"DOT\", \"LINK\",\"XLM\",\"USDT\",\"USDC\",\"DOGE\",\"SOL\",\"UNI\",\"MATIC\",\"ICP\", \"ETC\", \"XMR\", \"THETA\"]', '{\"api_key\":\"\",\"api_secret\":\"\"}', 'address', 1, NULL, '2023-08-17 11:42:51'),
(19, 'global/gateway/cashmaal.png', 'Cashmaal', 'cashmaal', '[\"USD\", \"PKR\"]', '{\"web_id\":\"\",\"secret_key\":\"\"}', 'to_email', 1, NULL, '2023-08-19 12:47:56'),
(20, 'global/gateway/blockio.png', 'Block.io', 'blockio', '[\"BTC\", \"LTC\",\"DOGE\"]', '{\"pin\":\"\",\"api_key\":\"\"}', 'to_address', 1, NULL, '2023-09-02 08:06:34'),
(21, 'global/gateway/blockchain.png', 'Blockchain', 'blockchain', '[\"BTC\"]', '{\"api_key\":\"\",\"xpub_code\":\"\"}', '0', 1, NULL, '2023-08-28 22:30:54'),
(22, 'global/gateway/instamojo.png', 'Instamojo', 'instamojo', '[\"INR\"]', '{\"api_key\":\"\",\"auth_token\":\"\",\"salt\":\"\"}', '0', 1, NULL, '2023-08-28 22:30:54'),
(23, 'global/gateway/paytm.png', 'Paytm', 'paytm', '[\"AUD\", \"ARS\", \"BDT\", \"BRL\", \"BGN\", \"CAD\", \"CLP\", \"CNY\", \"COP\",\r\n    \"HRK\", \"CZK\", \"DKK\", \"EGP\", \"EUR\", \"GEL\", \"GHS\", \"HKD\", \"HUF\",\r\n    \"INR\", \"IDR\", \"ILS\", \"JPY\", \"KES\", \"MYR\", \"MXN\", \"MAD\", \"NPR\",\r\n    \"NZD\", \"NGN\", \"NOK\", \"PKR\", \"PEN\", \"PHP\", \"PLN\", \"RON\", \"RUB\",\r\n    \"SGD\", \"ZAR\", \"KRW\", \"LKR\", \"SEK\", \"CHF\", \"THB\", \"TRY\", \"UGX\",\r\n    \"UAH\", \"AED\", \"GBP\", \"USD\", \"VND\", \"XOF\"]', '{\"merchant_id\":\"\",\"merchant_key\":\"\",\"merchant_website\":\"\",\"channel\":\"\",\"industry_type\":\"\"}', '0', 1, NULL, '2023-08-30 01:47:57'),
(24, 'global/gateway/razorpay.png', 'Razorpay', 'razorpay', '[\"INR\"]', '{\"razorpay_key\":\"\",\"razorpay_secret\":\"\"}', '0', 1, NULL, '2024-02-07 03:38:46'),
(25, 'global/gateway/twocheckout.png', '2Checkout', 'twocheckout', '[\"AFN\", \"ALL\", \"DZD\", \"ARS\", \"AUD\", \"AZN\", \"BSD\", \"BDT\", \"BBD\", \"BZD\", \r\n    \"BMD\", \"BOB\", \"BWP\", \"BRL\", \"GBP\", \"BND\", \"BGN\", \"CAD\", \"CLP\", \"CNY\", \r\n    \"COP\", \"CRC\", \"HRK\", \"CZK\", \"DKK\", \"DOP\", \"XCD\", \"EGP\", \"EUR\", \"FJD\", \r\n    \"GTQ\", \"HKD\", \"HNL\", \"HUF\", \"INR\", \"IDR\", \"ILS\", \"JMD\", \"JPY\", \"KZT\", \r\n    \"KES\", \"LAK\", \"MMK\", \"LBP\", \"LRD\", \"MOP\", \"MYR\", \"MVR\", \"MRO\", \"MUR\", \r\n    \"MXN\", \"MAD\", \"NPR\", \"TWD\", \"NZD\", \"NIO\", \"NOK\", \"PKR\", \"PGK\", \"PEN\", \r\n    \"PHP\", \"PLN\", \"QAR\", \"RON\", \"RUB\", \"WST\", \"SAR\", \"SCR\", \"SGD\", \"SBD\", \r\n    \"ZAR\", \"KRW\", \"LKR\", \"SEK\", \"CHF\", \"SYP\", \"THB\", \"TOP\", \"TTD\", \"TRY\", \r\n    \"UAH\", \"AED\", \"USD\", \"VUV\", \"VND\", \"XOF\", \"YER\"]', '{\"seller_id\":\"\",\"secret_word\":\"\"}', '0', 1, NULL, '2023-08-30 08:13:27');

-- --------------------------------------------------------

--
-- Table structure for table `gifts`
--

CREATE TABLE `gifts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `currency_id` bigint UNSIGNED NOT NULL,
  `redeemer_id` bigint UNSIGNED DEFAULT NULL,
  `code` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(20,8) NOT NULL,
  `charge` decimal(20,8) NOT NULL,
  `final_amount` decimal(20,8) NOT NULL,
  `claimed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `issue_date` date DEFAULT NULL,
  `to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `items` json DEFAULT NULL,
  `charge` decimal(28,8) DEFAULT NULL,
  `amount` decimal(28,8) DEFAULT NULL,
  `total_amount` decimal(28,8) DEFAULT NULL,
  `is_paid` tinyint(1) NOT NULL,
  `is_published` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kycs`
--

CREATE TABLE `kycs` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `for` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `fields` json DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kycs`
--

INSERT INTO `kycs` (`id`, `name`, `for`, `fields`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Govt ID Card', 'user', '{\"1\": {\"name\": \"Front NID\", \"type\": \"camera\", \"validation\": \"required\", \"instructions\": \"Take a clear photo of the front side of your NID card.\"}, \"2\": {\"name\": \"Back NID\", \"type\": \"camera\", \"validation\": \"required\", \"instructions\": \"Take a clear photo of the back side of your NID card.\"}, \"3\": {\"name\": \"Your Selfie\", \"type\": \"front_camera\", \"validation\": \"required\", \"instructions\": \"Take a selfie holding your NID card.\"}}', 1, '2025-11-01 22:00:00', '2025-11-02 05:14:25'),
(2, 'Passport', 'user', '{\"1\": {\"name\": \"Passport Photo\", \"type\": \"file\", \"validation\": \"required\", \"instructions\": \"Upload the main photo page of your passport.\"}, \"2\": {\"name\": \"Your Selfie\", \"type\": \"front_camera\", \"validation\": \"required\", \"instructions\": \"Take a selfie holding your passport open to the photo page.\"}}', 1, '2025-11-01 22:00:00', '2025-11-01 22:00:00'),
(3, 'Driving License', 'user', '{\"1\": {\"name\": \"Front Driving License\", \"type\": \"camera\", \"validation\": \"required\", \"instructions\": \"Capture the front side of your driving license clearly.\"}, \"2\": {\"name\": \"Your Selfie\", \"type\": \"front_camera\", \"validation\": \"required\", \"instructions\": \"Take a selfie holding your driving license.\"}}', 1, '2025-11-01 22:00:00', '2025-11-01 22:00:00'),
(4, 'Govt ID Card', 'agent', '{\"1\": {\"name\": \"Front NID\", \"type\": \"camera\", \"validation\": \"required\", \"instructions\": \"Upload a clear front photo of your NID.\"}, \"2\": {\"name\": \"Back NID\", \"type\": \"camera\", \"validation\": \"required\", \"instructions\": \"Upload a clear back photo of your NID.\"}, \"3\": {\"name\": \"Your Selfie\", \"type\": \"front_camera\", \"validation\": \"required\", \"instructions\": \"Take a selfie holding your NID card.\"}}', 1, '2025-11-01 22:00:00', '2025-11-01 22:00:00'),
(5, 'Passport', 'agent', '{\"1\": {\"name\": \"Passport Photo\", \"type\": \"file\", \"validation\": \"required\", \"instructions\": \"Upload the photo page of your passport.\"}, \"2\": {\"name\": \"Your Selfie\", \"type\": \"front_camera\", \"validation\": \"required\", \"instructions\": \"Take a selfie holding your passport.\"}}', 1, '2025-11-01 22:00:00', '2025-11-01 22:00:00'),
(6, 'Driving License', 'agent', '{\"1\": {\"name\": \"Front Driving License\", \"type\": \"camera\", \"validation\": \"required\", \"instructions\": \"Take a photo of the front side of your license.\"}, \"2\": {\"name\": \"Your Selfie\", \"type\": \"front_camera\", \"validation\": \"required\", \"instructions\": \"Take a selfie holding your driving license.\"}}', 1, '2025-11-01 22:00:00', '2025-11-01 22:00:00'),
(7, 'Trade License', 'merchant', '{\"1\": {\"name\": \"Trade License\", \"type\": \"file\", \"validation\": \"required\", \"instructions\": \"Upload a valid trade license copy.\"}, \"2\": {\"name\": \"Your Selfie\", \"type\": \"front_camera\", \"validation\": \"required\", \"instructions\": \"Take a selfie holding your trade license.\"}}', 1, '2025-11-01 22:00:00', '2025-11-01 22:00:00'),
(8, 'Passport', 'merchant', '{\"1\": {\"name\": \"Passport Photo\", \"type\": \"file\", \"validation\": \"required\", \"instructions\": \"Upload the passport photo page of the business owner.\"}, \"2\": {\"name\": \"Your Selfie\", \"type\": \"front_camera\", \"validation\": \"required\", \"instructions\": \"Take a selfie holding the passport.\"}}', 1, '2025-11-01 22:00:00', '2025-11-01 22:00:00'),
(9, 'Govt ID Card', 'merchant', '{\"1\": {\"name\": \"NID Front\", \"type\": \"camera\", \"validation\": \"required\", \"instructions\": \"Upload the front side of the owner\'s NID.\"}, \"2\": {\"name\": \"NID Back\", \"type\": \"camera\", \"validation\": \"required\", \"instructions\": \"Upload the back side of the owner\'s NID.\"}, \"3\": {\"name\": \"Owner Selfie\", \"type\": \"front_camera\", \"validation\": \"required\", \"instructions\": \"Take a selfie holding the NID card.\"}}', 1, '2025-11-01 22:00:00', '2025-11-01 22:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `landing_contents`
--

CREATE TABLE `landing_contents` (
  `id` bigint UNSIGNED NOT NULL,
  `theme` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locale_id` int DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `landing_contents`
--

INSERT INTO `landing_contents` (`id`, `theme`, `icon`, `title`, `description`, `photo`, `type`, `locale_id`, `locale`, `created_at`, `updated_at`) VALUES
(73, 'default', 'global/uploads/landing-list-assets/about/icons//69ukEZNRn6m8y9aQRQ32.png', 'Affiliate System', 'Earn rewards by referring users and partners. Grow your income while helping others experience seamless digital payments.', NULL, 'about', 73, 'en', '2025-04-08 10:27:59', '2025-12-09 05:48:12'),
(74, 'default', 'global/uploads/landing-list-assets/about/icons//CmXulJtkO46dsYcTdwe1.png', 'Withdraw Money', 'Easily transfer your funds to your bank or mobile wallet anytime, anywhere, quickly and securely.', NULL, 'about', 74, 'en', '2025-04-08 10:28:13', '2025-12-09 05:49:18'),
(75, 'default', 'global/uploads/landing-list-assets/about/icons//Jy2ClwuaUEvgZLxPgOfQ.png', 'Digital Platform', 'Experience instant, borderless payments for goods and services with just a few clicks.', NULL, 'about', 75, 'en', '2025-04-08 10:28:27', '2025-12-09 05:50:27'),
(76, 'default', 'global/uploads/images/KqXHqkwcEV3wySFffykW.webp', 'Agent & Merchant', 'Buy crypto using your credit or debit cards, bank transfers, and over 70 other payment methods tailored to suit your individual needs.Buy crypto using credit or debit cards, transfers,an', NULL, 'solutions', 76, 'en', '2025-04-08 11:05:16', '2025-04-28 05:51:02'),
(77, 'default', 'global/uploads/images/Z5jjM1yf5SatnJnxvsCo.webp', 'Internet Retailer & Ecommerce', 'Buy crypto using your credit or debit cards, bank transfers, and over 70 other payment methods tailored to suit your individual needs.Buy crypto using credit or debit cards, transfers,an', NULL, 'solutions', 77, 'en', '2025-04-08 11:05:33', '2025-04-27 09:20:57'),
(78, 'default', 'global/uploads/images/I1IPIeKFR5St3nnEiTo9.webp', 'Digital Payments', 'Buy crypto using your credit or debit cards, bank transfers, and over 70 other payment methods tailored to suit your individual needs.Buy crypto using credit or debit cards, transfers,an', NULL, 'solutions', 78, 'en', '2025-04-08 11:05:58', '2025-04-28 05:50:43'),
(79, 'default', 'global/uploads/images/n1TlRuDDS3U9ZpGAHKNh.webp', 'Affiliate Marketer', 'Buy crypto using your credit or debit cards, bank transfers, and over 70 other payment methods tailored to suit your individual needs.Buy crypto using credit or debit cards, transfers,an', NULL, 'solutions', 79, 'en', '2025-04-08 11:06:11', '2025-04-24 09:54:31'),
(88, 'default', 'global/uploads/landing-list-assets/merchant/icons//PS5q7O18BMOXudASIh8N.webp', 'Multi-Currency Support', 'Accept payments in various cryptocurrencies', NULL, 'merchant', 29, 'en', '2025-03-25 05:37:23', '2025-12-15 06:58:55'),
(89, 'default', 'global/uploads/landing-list-assets/merchant/icons//l9hfRon7Dmk1lAs1sEfV.webp', 'API & Plugins', 'Easy integration with major e-commerce, subscription platforms. online shops etc', NULL, 'merchant', 55, 'en', '2025-03-25 05:38:01', '2025-12-15 07:01:54'),
(90, 'default', 'global/uploads/landing-list-assets/merchant/icons//XyL3HcmpsQNcaDAQWIxV.webp', 'Custom Payment Links', 'Generate links for direct payments', NULL, 'merchant', 56, 'en', '2025-03-25 05:38:20', '2025-12-15 07:02:00'),
(91, 'default', 'global/uploads/landing-list-assets/merchant/icons//hkkw5fEXZOsbq3j1SdO2.webp', 'Instant Payouts', 'Withdraw funds in real-time', NULL, 'merchant', 57, 'en', '2025-03-25 05:38:35', '2025-12-15 07:02:06'),
(97, 'default', 'global/uploads/images/iAfBXIL1Knr3ai9PFXFe.png', 'Total Users', '52220', NULL, 'counter', 97, 'en', '2025-04-28 03:42:41', '2026-01-03 08:49:28'),
(98, 'default', 'global/uploads/landing-list-assets/counter/icons//0vGdVfjTjKCDfZAXHEaJ.png', 'Total Deposit', '25300', NULL, 'counter', 98, 'en', '2025-04-28 03:42:59', '2025-04-28 03:44:05'),
(99, 'default', 'global/uploads/images/EP78i91NKf9ZLArhGCNA.png', 'Total Agents', '250', NULL, 'counter', 99, 'en', '2025-04-28 03:44:20', '2025-04-28 03:44:20'),
(100, 'default', 'global/uploads/images/gvRiQb3F8CRR2OZPXb3h.png', 'Total Merchants', '300', NULL, 'counter', 100, 'en', '2025-04-28 03:44:37', '2025-04-28 03:44:37'),
(101, 'default', 'global/uploads/images/XosUFvm6asjsXa45lu7y.png', 'Register Account', 'By Registering the website you will able to start your operation', NULL, 'howitworks', 101, 'en', '2025-04-28 05:36:07', '2025-04-28 05:36:07'),
(102, 'default', 'global/uploads/images/cuTgwRUhXtZEaUiN1WQb.png', 'Verify Email', 'After creating the account user need to verify the email for account purpose', NULL, 'howitworks', 102, 'en', '2025-04-28 05:36:20', '2025-04-28 05:36:20'),
(103, 'default', 'global/uploads/images/UMjprpq2WKD17WxSJSq4.png', 'Verify KYC', 'Users\' KYC needs to verify to before making any withdrawals', NULL, 'howitworks', 103, 'en', '2025-04-28 05:36:34', '2025-04-28 05:36:34'),
(104, 'default', 'global/uploads/images/tIski8sScfXarKXARa5V.png', 'Deposit Money', 'Users can deposit using any automatic or manual gateways', NULL, 'howitworks', 104, 'en', '2025-04-28 05:37:10', '2025-04-28 05:37:10'),
(105, 'default', 'global/uploads/images/MYPgwN0YLPk5smvXW0UE.png', 'Merchants & Agents', 'User can pay to the merchant and also cashout money from agent.', NULL, 'howitworks', 105, 'en', '2025-04-28 05:37:56', '2025-04-30 16:40:29'),
(106, 'default', 'global/uploads/images/nNWtj0GYodUU3kh9wYb1.png', 'Transfer, Gift, Invoice', 'The user can transfer money to another user. Also, gift creation for other users and redeeming the gift. Also, create an invoice and pay.', NULL, 'howitworks', 106, 'en', '2025-04-28 05:39:32', '2025-04-28 05:39:32'),
(107, 'default', 'global/uploads/images/KvzkrRuf32hqfaE7Jsgh.png', 'Refer To Friends', 'User can refer their friends and get a referral bonus.', NULL, 'howitworks', 107, 'en', '2025-04-28 05:40:40', '2025-04-28 05:40:40'),
(108, 'default', 'global/uploads/images/jZXxwNV06fFDukHUfLUn.png', 'Withdraw & Cash Out', 'User can withdraw their money or cash out from the agent.', NULL, 'howitworks', 108, 'en', '2025-04-28 05:41:30', '2025-04-28 05:41:30'),
(111, 'default', 'global/uploads/landing-list-assets/about/icons//uXYOeuoRtIjTVEcx9ewN.png', 'Secure Transaction', 'Make fast and secure payments to online and offline merchants using your Qunzo balance.', NULL, 'about', 109, 'en', '2025-04-30 16:45:59', '2025-12-09 05:51:49'),
(117, 'default', 'global/uploads/images/hAEdFekUSr0uNSzMvyjG.svg', 'Agent & Merchant', 'Accept and manage payments easily in-store or online — fast, secure, and reliable.', NULL, 'features', 112, 'en', '2025-12-09 06:20:06', '2025-12-09 06:20:06'),
(118, 'default', 'global/uploads/images/aApqTOZi0fKsW0viB7iK.svg', 'Internet Retailer & Ecommerce', 'Power your store with instant, safe online payments that build customer trust.', NULL, 'features', 118, 'en', '2025-12-09 06:21:32', '2025-12-09 06:21:32'),
(119, 'default', 'global/uploads/images/1x0whI9UHTjX5lLjUmT9.svg', 'Digital Payments', 'Send or receive payments instantly safe, seamless, and always available.', NULL, 'features', 119, 'en', '2025-12-09 06:21:45', '2025-12-09 06:21:45'),
(120, 'default', 'global/uploads/images/WrSFo6jH9RNxkcaDPaxp.svg', 'Affiliate Marketer', 'Get paid quickly and securely for every sale or referral, anywhere, anytime.', NULL, 'features', 120, 'en', '2025-12-09 06:21:59', '2025-12-09 06:21:59'),
(121, 'default', NULL, 'How do I create an account?', 'Simply download the Qunzo app or visit our website, click \"Sign Up,\" and follow the registration process. You\'ll need to provide basic information and verify your email/phone number.', NULL, 'faqs', 121, 'en', '2025-12-09 06:43:59', '2025-12-09 06:43:59'),
(122, 'default', NULL, 'What is KYC verification and why is it required?', 'KYC (Know Your Customer) is an identity verification process required for security and regulatory compliance. It helps protect your account and enables higher transaction limits.', NULL, 'faqs', 122, 'en', '2025-12-09 06:44:17', '2025-12-09 06:44:17'),
(123, 'default', NULL, 'Can I have multiple wallets?', 'Yes, Qunzo supports multiple currency wallets. You can create and manage wallets for different currencies within a single account.', NULL, 'faqs', 123, 'en', '2025-12-09 06:44:27', '2025-12-09 06:44:27'),
(124, 'default', NULL, 'What are the transaction fees?', 'Transaction fees vary depending on the type of transaction (transfer, payment, withdrawal, etc.) and the payment method used. You\'ll see the exact fee before confirming any transaction.', NULL, 'faqs', 124, 'en', '2025-12-09 06:44:44', '2025-12-09 06:44:44'),
(125, 'default', NULL, 'Which currencies does Qunzo support?', 'Qunzo supports multiple fiat and cryptocurrencies. The available currencies depend on your region and account type. Check the app for the complete list of supported currencies.', NULL, 'faqs', 125, 'en', '2025-12-09 06:45:11', '2025-12-09 06:45:11'),
(126, 'default', NULL, 'Can I exchange between different currencies?', 'Yes, Qunzo offers currency exchange features allowing you to convert between different currencies in your wallet at competitive rates.', NULL, 'faqs', 126, 'en', '2025-12-09 06:45:24', '2025-12-09 06:45:24'),
(127, 'default', NULL, 'Can I integrate Qunzo with my website?', 'Yes, Qunzo provides API integration and plugins (including WooCommerce) to accept payments on your website or e-commerce platform.', NULL, 'faqs', 127, 'en', '2025-12-09 06:45:41', '2025-12-09 06:45:41'),
(128, 'default', 'global/uploads/landing-list-assets/agent/icons//VUJjfHbOwfOl5x9XmZTU.webp', 'Instant Cash Services', 'Provide fast cash-in and cash-out services to customers using Qunzo wallets.', NULL, 'agent', 59, 'en', '2025-03-25 08:25:04', '2025-12-15 06:49:09'),
(129, 'default', 'global/uploads/landing-list-assets/agent/icons//7qM2wlKOT9BzbLVB7wSS.webp', 'Agent Dashboard', 'Manage transactions, commissions, and balances from one powerful dashboard.', NULL, 'agent', 61, 'en', '2025-03-25 08:25:25', '2025-12-15 06:49:15'),
(130, 'default', 'global/uploads/landing-list-assets/agent/icons//wWlrmBEIzYo9bjAh51w1.webp', 'Cash In & Cash Out Commission', 'Earn commissions on every successful cash-in and cash-out transaction.', NULL, 'agent', 62, 'en', '2025-03-25 08:25:48', '2025-12-15 06:49:21'),
(131, 'default', 'global/uploads/landing-list-assets/agent/icons//hv0OPwj191gegF2AsLKz.webp', 'Secure Transactions', 'All agent activities are protected with advanced security and fraud prevention.', NULL, 'agent', 63, 'en', '2025-03-25 08:26:08', '2025-12-15 06:49:27'),
(211, 'default', 'global/uploads/images/QIa7tb5UQUQhHGrYJ4vL.svg', '01. Create Card', 'Create a virtual card in seconds', NULL, 'virtual-cards-how-it-works', 132, 'en', '2026-01-21 05:57:13', '2026-01-21 05:57:13'),
(212, 'default', 'global/uploads/images/GG7VdwTFcEwavv2mjgjF.svg', '02. Pay Online', 'Use it for online payments and subscriptions', NULL, 'virtual-cards-how-it-works', 212, 'en', '2026-01-21 05:58:27', '2026-01-21 05:58:27'),
(213, 'default', 'global/uploads/images/892NJtRZ2Dxoz8oRysTR.svg', '03. Track Spending', 'Track and control spending in real time', NULL, 'virtual-cards-how-it-works', 213, 'en', '2026-01-21 05:58:43', '2026-01-21 05:58:43'),
(214, 'default', 'global/uploads/images/OyCTV79mYxVF7SUVRzL2.png', 'Instant card generation', 'Get paid on every successful transaction.', NULL, 'virtual-cards-features', 214, 'en', '2026-01-21 06:01:59', '2026-01-21 06:01:59'),
(215, 'default', 'global/uploads/images/C6wgo2veXqIPQjaX5QvB.png', 'Global online acceptance', 'Work on your own schedule and increase earnings.', NULL, 'virtual-cards-features', 215, 'en', '2026-01-21 06:02:17', '2026-01-21 06:02:17'),
(216, 'default', 'global/uploads/images/VNO7cq53JWNEU0cJpp4h.png', 'Spending limits & freeze option', 'All transactions are protected with advanced security.', NULL, 'virtual-cards-features', 216, 'en', '2026-01-21 06:02:46', '2026-01-21 06:02:46'),
(217, 'default', 'global/uploads/images/OGy07doP8hhIsgOMQmwC.png', 'Real-time transaction tracker', 'Track balance, earnings, and transactions easily.', NULL, 'virtual-cards-features', 217, 'en', '2026-01-21 06:03:11', '2026-01-21 06:03:11'),
(218, 'default', NULL, 'What is a Virtual Card?', 'A Virtual Card is a digital payment card that works like a physical debit or prepaid card but exists only online. It can be used for online purchases, subscriptions, and international payments.', NULL, 'virtual-cards-faqs', 218, 'en', '2026-01-21 06:04:01', '2026-01-21 06:04:01'),
(219, 'default', NULL, 'Is the Virtual Card a debit or credit card?', 'The Virtual Card is a prepaid/debit card. You must load funds before making any transactions.', NULL, 'virtual-cards-faqs', 219, 'en', '2026-01-21 06:04:36', '2026-01-21 06:04:36'),
(220, 'default', NULL, 'Which card types are supported?', 'Visa, Mastercard (Availability depends on the selected card provider and region).', NULL, 'virtual-cards-faqs', 220, 'en', '2026-01-21 06:05:09', '2026-01-21 06:05:09'),
(221, 'default', NULL, 'Can I top up my Virtual Card?', 'Yes. You can add funds to your virtual card anytime as long as the card is active.', NULL, 'virtual-cards-faqs', 221, 'en', '2026-01-21 06:05:28', '2026-01-21 06:05:28'),
(222, 'default', NULL, 'Is KYC required to use Virtual Cards?', 'Yes. Identity verification may be required before creating or using virtual cards, depending on regulatory requirements.', NULL, 'virtual-cards-faqs', 222, 'en', '2026-01-21 06:05:51', '2026-01-21 06:05:51'),
(223, 'default', 'global/uploads/images/g8Y0hAYDEyY8aU3Eo07Z.png', 'Instant Recharge', 'Fast and real-time mobile recharges with immediate confirmation.', NULL, 'mobile-recharge-features', 223, 'en', '2026-01-22 04:43:36', '2026-01-22 04:43:36'),
(224, 'default', 'global/uploads/images/gRF9yuu914wSq69OstXh.png', 'Secure Payments', 'All transactions are protected with advanced encryption and security.', NULL, 'mobile-recharge-features', 224, 'en', '2026-01-22 04:44:07', '2026-01-22 04:44:07'),
(225, 'default', 'global/uploads/images/oHkT3LaYgm4KI0NsG65a.png', 'All Operators Supported', 'Recharge any mobile operator from a single platform with ease.', NULL, 'mobile-recharge-features', 225, 'en', '2026-01-22 04:44:22', '2026-01-22 04:44:22'),
(226, 'default', 'global/uploads/images/jAybwuOeDK0gmDGg8P9f.png', 'Transaction History', 'View and track all your past recharges and payment details.', NULL, 'mobile-recharge-features', 226, 'en', '2026-01-22 04:44:38', '2026-01-22 04:44:38'),
(227, 'default', 'global/uploads/images/m5iXmz7nBBzZIdZuiHYt.png', 'Multiple Payment Methods', 'Pay using cards, mobile wallets, or balance wallet options.', NULL, 'mobile-recharge-features', 227, 'en', '2026-01-22 04:44:55', '2026-01-22 04:44:55'),
(228, 'default', 'global/uploads/images/mPrHoDFuoAIDpoTUjfGK.png', 'Nationwide Coverage', 'Recharge numbers across the country without limitations.', NULL, 'mobile-recharge-features', 228, 'en', '2026-01-22 04:45:08', '2026-01-22 04:45:08'),
(229, 'default', 'global/uploads/landing-list-assets/mobile-recharge-features/icons//u4AyutzG9cWfBGW2YpO8.png', 'Offers & Cashback', 'Enjoy exclusive deals, discounts, and cashback on recharges.', NULL, 'mobile-recharge-features', 229, 'en', '2026-01-22 04:45:27', '2026-01-22 04:55:00'),
(230, 'default', 'global/uploads/images/D2tSL5MrIekNtBDLi7Oe.png', 'Reliable Service', 'High success rate with instant refund on failed recharges.', NULL, 'mobile-recharge-features', 230, 'en', '2026-01-22 04:55:18', '2026-01-22 04:55:18'),
(231, 'default', 'global/uploads/images/QCqvHVXxhqr1n11pReb8.png', 'Enter Mobile Number', 'Choose the number you want to recharge.', NULL, 'mobile-recharge-how-it-works', 231, 'en', '2026-01-22 04:56:33', '2026-01-22 04:56:33'),
(232, 'default', 'global/uploads/images/GombY0O13TG0ynwgyIGS.png', 'Select Amount', 'Pick a top-up value or custom amount.', NULL, 'mobile-recharge-how-it-works', 232, 'en', '2026-01-22 04:56:50', '2026-01-22 04:56:50'),
(233, 'default', 'global/uploads/images/n2pv0x3m5WuMyBECVc7h.png', 'Pay Securely', 'Pay via wallets', NULL, 'mobile-recharge-how-it-works', 233, 'en', '2026-01-22 04:57:12', '2026-01-22 04:57:12'),
(234, 'default', 'global/uploads/images/Ligdfy0OiX7KHhizWahB.png', 'Instant Recharge', 'Get confirmation in seconds.', NULL, 'mobile-recharge-how-it-works', 234, 'en', '2026-01-22 04:57:28', '2026-01-22 04:57:28'),
(235, 'default', NULL, 'How long does a recharge take?', 'Most recharges are processed instantly. In rare cases, it may take a few minutes depending on the operator.', NULL, 'mobile-recharge-faqs', 235, 'en', '2026-01-22 04:58:00', '2026-01-22 04:58:00'),
(236, 'default', NULL, 'What happens if my recharge fails?', 'If a recharge fails, the amount will be refunded automatically to your wallet.', NULL, 'mobile-recharge-faqs', 236, 'en', '2026-01-22 04:58:30', '2026-01-22 04:58:30'),
(237, 'default', NULL, 'Can I view my past recharges?', 'Yes, all your recharge and payment history is available in the Transaction History section.', NULL, 'mobile-recharge-faqs', 237, 'en', '2026-01-22 04:58:41', '2026-01-22 04:58:41'),
(238, 'default', NULL, 'Can I recharge for someone else?', 'Yes, you can recharge any valid mobile number, including friends and family.', NULL, 'mobile-recharge-faqs', 238, 'en', '2026-01-22 04:59:04', '2026-01-22 04:59:04'),
(239, 'default', 'global/uploads/images/c4EHb6VphkErxO9VO6H2.png', 'Instant Bill Payment', 'Pay your bills in seconds with real-time confirmation.', NULL, 'bill-payment-features', 239, 'en', '2026-01-22 09:28:05', '2026-01-22 09:28:05'),
(240, 'default', 'global/uploads/images/qHuMuKRI6LVmK9ReOeIn.png', 'Secure Transactions', 'Bank-grade encryption keeps every payment safe.', NULL, 'bill-payment-features', 240, 'en', '2026-01-22 09:28:22', '2026-01-22 09:28:22'),
(241, 'default', 'global/uploads/images/HBXiOaILlHiKvi5MDoy4.png', 'Auto Bill Fetch', 'Enter your account number to fetch bill details automatically.', NULL, 'bill-payment-features', 241, 'en', '2026-01-22 09:28:53', '2026-01-22 09:28:53'),
(242, 'default', 'global/uploads/images/LZCPKW0JybNJKOet6ey2.png', 'Multiple Payment Methods', 'Pay using different wallets.', NULL, 'bill-payment-features', 242, 'en', '2026-01-22 09:29:19', '2026-01-22 09:29:19'),
(243, 'default', 'global/uploads/images/wu8UOC55uRivVsJgC4aU.png', 'All Major Billers Supported', 'Electricity, gas, water, internet, TV, and more.', NULL, 'bill-payment-features', 243, 'en', '2026-01-22 09:29:34', '2026-01-22 09:29:34'),
(244, 'default', NULL, 'What is Bill Payment?', 'Bill Payment allows you to pay your utility bills, internet, TV, electricity, water, and other services directly from your wallet or linked account.', NULL, 'bill-payment-faqs', 244, 'en', '2026-01-22 09:30:04', '2026-01-22 09:30:04'),
(245, 'default', NULL, 'Are there any fees?', 'Some billers or payment providers may charge a convenience fee. Any fee will be shown before you confirm the payment.', NULL, 'bill-payment-faqs', 245, 'en', '2026-01-22 09:30:19', '2026-01-22 09:30:19'),
(246, 'default', NULL, 'How long does it take for my payment to be processed?', 'Most payments are instant, but some may take 1–2 business days depending on the biller.', NULL, 'bill-payment-faqs', 246, 'en', '2026-01-22 09:30:36', '2026-01-22 09:30:36'),
(247, 'default', NULL, 'Can I cancel a bill payment?', 'Once a payment is processed, it cannot be canceled. Make sure the bill details are correct before confirming.', NULL, 'bill-payment-faqs', 247, 'en', '2026-01-22 09:30:50', '2026-01-22 09:30:50'),
(248, 'default', NULL, 'Do I need KYC to pay bills?', 'Yes. You must complete identity verification (KYC) to use bill payment services, as required by regulations.', NULL, 'bill-payment-faqs', 248, 'en', '2026-01-22 09:31:08', '2026-01-22 09:31:08'),
(249, 'default', 'global/uploads/images/2nLVa9Q7MqE851zbRG5I.png', 'Airtime', 'Recharge any mobile number instantly.', NULL, 'bill-payment-categories', 249, 'en', '2026-01-22 09:35:06', '2026-01-22 09:35:06'),
(250, 'default', 'global/uploads/images/dlvgRG5OqoOuN0NdVIio.png', 'Electricity', 'Pay electricity bills without hassle.', NULL, 'bill-payment-categories', 250, 'en', '2026-01-22 09:35:21', '2026-01-22 09:35:21'),
(251, 'default', 'global/uploads/images/wZpVjLsSZlU1trE5SJOV.png', 'Data Bundle', 'Buy internet data packs instantly.', NULL, 'bill-payment-categories', 251, 'en', '2026-01-22 09:35:36', '2026-01-22 09:35:36'),
(252, 'default', 'global/uploads/images/eH0YPkOBfITf8RJsIdDG.png', 'Cables', 'Pay cable TV bills with ease.', NULL, 'bill-payment-categories', 252, 'en', '2026-01-22 09:35:55', '2026-01-22 09:35:55'),
(253, 'default', 'global/uploads/images/DGgbjJvBH0x29GQASNML.png', 'Escrow Protection', 'Funds are securely held and released only after payment confirmation.', NULL, 'p2p-trading-how-it-works', 253, 'en', '2026-01-24 05:28:51', '2026-01-24 05:28:51'),
(254, 'default', 'global/uploads/images/QVuznv046LQLWUCg2klN.png', 'Real-Time Market Rates', 'View live buy and sell prices before placing an order.', NULL, 'p2p-trading-how-it-works', 254, 'en', '2026-01-24 05:29:18', '2026-01-24 05:29:18'),
(255, 'default', 'global/uploads/images/fUM7Yn1CJgNoG5mcXMqg.png', 'Verified Traders', 'Trade with KYC-verified users for safer transactions.', NULL, 'p2p-trading-how-it-works', 255, 'en', '2026-01-24 05:29:42', '2026-01-24 05:29:42'),
(256, 'default', 'global/uploads/images/Vz2KeWTblgG9v2Hw5d3Y.png', 'Multiple Payment Methods', 'Support for bank transfer, mobile banking, and wallet balance.', NULL, 'p2p-trading-how-it-works', 256, 'en', '2026-01-24 05:29:59', '2026-01-24 05:29:59'),
(257, 'default', 'global/uploads/images/jpM7YmDMpEHedm4zeXcX.png', 'Fast Order Matching', 'Find buyers and sellers instantly at competitive rates.', NULL, 'p2p-trading-how-it-works', 257, 'en', '2026-01-24 05:30:14', '2026-01-24 05:30:14'),
(258, 'default', 'global/uploads/images/zNrc2MZbXq6y3yAvijAL.png', 'Dispute Resolution System', 'Fair and secure dispute handling with admin support.', NULL, 'p2p-trading-how-it-works', 258, 'en', '2026-01-24 05:30:32', '2026-01-24 05:30:32'),
(259, 'default', 'global/uploads/images/1FT4qPtfuu5R9IjSsxJU.png', 'In-Trade Chat', 'Communicate directly with traders inside the platform.', NULL, 'p2p-trading-how-it-works', 259, 'en', '2026-01-24 05:30:53', '2026-01-24 05:30:53'),
(260, 'default', 'global/uploads/images/ExuKVjJwkrFYBrXZOfFO.png', 'Multi-Currency Support', 'Exchange between supported local and international currencies.', NULL, 'p2p-trading-how-it-works', 260, 'en', '2026-01-24 05:31:18', '2026-01-24 05:31:18'),
(261, 'default', NULL, 'Choose an Offer', 'Select a buy or sell offer with your desired rate.', NULL, 'p2p-trading-features', 261, 'en', '2026-01-24 05:36:23', '2026-01-24 05:36:23'),
(262, 'default', NULL, 'Place Order', 'Enter amount and confirm trade.', NULL, 'p2p-trading-features', 262, 'en', '2026-01-24 05:36:44', '2026-01-24 05:36:44'),
(263, 'default', NULL, 'Make Payment', 'Pay seller using agreed method.', NULL, 'p2p-trading-features', 263, 'en', '2026-01-24 05:37:08', '2026-01-24 05:37:08'),
(264, 'default', NULL, 'Release Funds', 'Funds are released after confirmation.', NULL, 'p2p-trading-features', 264, 'en', '2026-01-24 05:37:21', '2026-01-24 05:37:21'),
(265, 'default', NULL, 'What is P2P Trading?', 'P2P (Peer-to-Peer) trading allows users to buy and sell assets directly with other users without an intermediary. The platform provides an escrow system to ensure safe transactions.', NULL, 'p2p-trading-faqs', 265, 'en', '2026-01-24 05:38:17', '2026-01-24 05:38:17'),
(266, 'default', NULL, 'What is escrow and how does it work?', 'Escrow temporarily locks the seller’s assets during a trade. Once the seller confirms payment, the assets are released to the buyer automatically.', NULL, 'p2p-trading-faqs', 266, 'en', '2026-01-24 05:38:35', '2026-01-24 05:38:35'),
(267, 'default', NULL, 'What happens if the buyer doesn’t pay?', 'If payment is not completed within the allowed time, the trade is automatically canceled and funds are released back to the seller.', NULL, 'p2p-trading-faqs', 267, 'en', '2026-01-24 05:38:56', '2026-01-24 05:38:56'),
(268, 'default', NULL, 'Can I cancel a trade?', 'Yes, you can cancel a trade before payment is made. Once payment is marked as completed, cancellation is not allowed.', NULL, 'p2p-trading-faqs', 268, 'en', '2026-01-24 05:39:14', '2026-01-24 05:39:14'),
(269, 'default', NULL, 'Do I need KYC to use P2P trading?', 'Yes. Identity verification is required to create offers or trade on the P2P platform.', NULL, 'p2p-trading-faqs', 269, 'en', '2026-01-24 05:39:30', '2026-01-24 05:39:30'),
(270, 'default', 'global/uploads/images/uovPbdu1zzoLvAUXY4t1.png', 'Instant Send & Receive', 'Transfer money instantly to anyone, anytime.', NULL, 'service-wallets-features', 270, 'en', '2026-01-25 04:09:07', '2026-01-25 04:09:07'),
(271, 'default', 'global/uploads/images/VzxvLG5PFk2eyvOqmQje.png', 'Multi-Currency Support', 'Hold and manage multiple currencies in one wallet.', NULL, 'service-wallets-features', 271, 'en', '2026-01-25 04:09:25', '2026-01-25 04:09:25'),
(272, 'default', 'global/uploads/images/y27ycr8pUSIZi5QIWYAi.png', 'Secure Storage', 'Advanced encryption keeps your funds protected.', NULL, 'service-wallets-features', 272, 'en', '2026-01-25 04:10:26', '2026-01-25 04:10:26'),
(273, 'default', 'global/uploads/images/1BscAbRcmfSWwxsyUZ6h.png', 'Transaction History', 'View detailed records of all wallet activity.', NULL, 'service-wallets-features', 273, 'en', '2026-01-25 04:10:54', '2026-01-25 04:10:54'),
(274, 'default', 'global/uploads/images/UOu0OAYWjhEJkeiObkdG.png', 'Quick Top-Up', 'Add funds using bank, card, or mobile banking.', NULL, 'service-wallets-features', 274, 'en', '2026-01-25 04:11:17', '2026-01-25 04:11:17'),
(275, 'default', 'global/uploads/images/XMnlCAleKliUrFc21JOA.png', 'Easy Withdrawals', 'Cash out to your bank or mobile wallet anytime.', NULL, 'service-wallets-features', 275, 'en', '2026-01-25 04:12:18', '2026-01-25 04:12:18'),
(276, 'default', NULL, 'Create Your Wallet', 'Sign up and create your wallet in minutes.', NULL, 'service-wallets-steps', 276, 'en', '2026-01-25 04:14:27', '2026-01-25 04:14:27'),
(277, 'default', NULL, 'Add Funds', 'Sign up and create your wallet in minutes.', NULL, 'service-wallets-steps', 277, 'en', '2026-01-25 04:14:48', '2026-01-25 04:14:48'),
(278, 'default', NULL, 'Pay, Transfer or Exchange', 'Send, receive, exchange and manage money easily.', NULL, 'service-wallets-steps', 278, 'en', '2026-01-25 04:15:40', '2026-01-25 04:15:40'),
(279, 'default', NULL, 'What is a wallet?', 'A wallet is a digital account that allows you to store funds, send and receive money, and make payments securely on the platform.', NULL, 'service-wallets-faqs', 279, 'en', '2026-01-25 04:16:48', '2026-01-25 04:16:48'),
(280, 'default', NULL, 'Can I have multiple wallets?', 'Yes. You may create and manage multiple wallets, depending on your account limits and supported currencies.', NULL, 'service-wallets-faqs', 280, 'en', '2026-01-25 04:17:04', '2026-01-25 04:17:04'),
(281, 'default', NULL, 'How long do wallet transactions take?', 'Most wallet transactions are instant. Some may take longer depending on the payment method or provider.', NULL, 'service-wallets-faqs', 281, 'en', '2026-01-25 04:17:27', '2026-01-25 04:17:27'),
(282, 'default', NULL, 'What happens if a wallet transaction fails?', 'If a transaction fails, the amount will remain in your wallet or be refunded automatically, depending on the transaction status.', NULL, 'service-wallets-faqs', 282, 'en', '2026-01-25 04:17:50', '2026-01-25 04:17:50'),
(283, 'default', 'global/uploads/images/BTat0IfWZtDV5dbq0Ypx.png', 'Instant Wallet Credit', 'Your balance is updated in real time as soon as your payment is confirmed — no waiting, no delays.', NULL, 'service-add-money-features', 283, 'en', '2026-01-25 04:25:47', '2026-01-25 04:25:47'),
(284, 'default', 'global/uploads/images/fpV4uzs8BAMqmEKGao4a.png', 'Bank-Grade Security', 'All deposits are protected with advanced encryption and fraud-prevention systems to keep your money safe.', NULL, 'service-add-money-features', 284, 'en', '2026-01-25 04:26:12', '2026-01-25 04:26:12'),
(285, 'default', 'global/uploads/images/icbU4H0E0dJoLbJpDocX.png', 'Multiple Payment Options', 'Add money using bank transfer, cards, mobile wallets, crypto, or P2P — all in one place.', NULL, 'service-add-money-features', 285, 'en', '2026-01-25 04:26:30', '2026-01-25 04:26:30'),
(286, 'default', 'global/uploads/images/6UqxOGXP8FvWwhX18RHw.png', 'Fast Processing', 'Most deposits are completed within seconds, so you can start trading or paying immediately.', NULL, 'service-add-money-features', 286, 'en', '2026-01-25 04:26:47', '2026-01-25 04:26:47'),
(287, 'default', 'global/uploads/images/scAfB0IiZEH2ADg5dJaH.png', 'Global & Local Support', 'Deposit using international cards, local payment methods, and global payment methods.', NULL, 'service-add-money-features', 287, 'en', '2026-01-25 04:27:46', '2026-01-25 04:27:46'),
(288, 'default', 'global/uploads/images/laDPyI8WOZiPP9SBS64y.png', 'Transparent Fees', 'See all charges upfront before confirming — no hidden costs, no surprises.', NULL, 'service-add-money-features', 288, 'en', '2026-01-25 04:28:03', '2026-01-25 04:28:03'),
(289, 'default', NULL, 'Topup', 'Select your preferred top-up method', NULL, 'service-add-money-steps', 289, 'en', '2026-01-25 04:28:20', '2026-01-25 04:28:20'),
(290, 'default', NULL, 'Payment', 'Enter the amount and complete payment', NULL, 'service-add-money-steps', 290, 'en', '2026-01-25 04:28:32', '2026-01-25 04:28:32'),
(291, 'default', NULL, 'Wallet', 'Your wallet is credited instantly', NULL, 'service-add-money-steps', 291, 'en', '2026-01-25 04:28:45', '2026-01-25 04:28:45'),
(292, 'default', NULL, 'What does “Add Money” mean?', '“Add Money” allows you to fund your wallet using supported payment methods so you can make transactions on the platform.', NULL, 'service-add-money-faqs', 292, 'en', '2026-01-25 04:29:15', '2026-01-25 04:29:15'),
(293, 'default', NULL, 'Is there a minimum or maximum add money limit?', 'Yes. Minimum and maximum limits may apply depending on the payment method, provider, and your account level.', NULL, 'service-add-money-faqs', 293, 'en', '2026-01-25 04:29:28', '2026-01-25 04:29:28'),
(294, 'default', NULL, 'Are there any fees for adding money?', 'Fees may apply based on the payment method or provider. All fees will be shown before you confirm the transaction.', NULL, 'service-add-money-faqs', 294, 'en', '2026-01-25 04:29:44', '2026-01-25 04:29:44'),
(295, 'default', NULL, 'How long does it take to add money?', 'Most add-money transactions are instant, but some methods may take a few minutes or up to 1 business day.', NULL, 'service-add-money-faqs', 295, 'en', '2026-01-25 04:30:06', '2026-01-25 04:30:06'),
(296, 'default', 'global/uploads/images/B0Qyarh9WTdlu19O0WEP.png', 'Instant Payments', 'Send money or pay merchants in real time — no delays, no waiting.', NULL, 'service-make-payment-features', 296, 'en', '2026-01-25 04:42:55', '2026-01-25 04:42:55'),
(297, 'default', 'global/uploads/images/qqJfSoqeI5OBUCC9Tuwb.png', 'Secure Transactions', 'Every payment is protected with encryption, OTP and fraud-detection systems.', NULL, 'service-make-payment-features', 297, 'en', '2026-01-25 04:43:12', '2026-01-25 04:43:12'),
(298, 'default', 'global/uploads/images/zcrBP4eB8fcV4fojzotu.png', 'Multiple Payment Modes', 'Pay via wallet balance, QR code, user ID, or merchant account — all in one place.', NULL, 'service-make-payment-features', 298, 'en', '2026-01-25 04:43:31', '2026-01-25 04:43:31'),
(299, 'default', 'global/uploads/images/7TFdFcOQq2DEMaVG7Okx.png', 'Payment History & Tracking', 'View, download and track all your payments anytime with full details.', NULL, 'service-make-payment-features', 299, 'en', '2026-01-25 04:43:51', '2026-01-25 04:43:51'),
(300, 'default', 'global/uploads/images/x8Mvyw9RQP3rERlrceev.png', 'Local & Global Payments', 'Pay both local merchants and international services from a single wallet.', NULL, 'service-make-payment-features', 300, 'en', '2026-01-25 04:44:19', '2026-01-25 04:44:19'),
(301, 'default', 'global/uploads/images/DGP5kRNsJDEB0f5dnlbL.png', 'Business & Personal Use', 'Perfect for shopping, subscriptions, bill payments, and business transactions.', NULL, 'service-make-payment-features', 301, 'en', '2026-01-25 04:44:37', '2026-01-25 04:44:37'),
(302, 'default', NULL, 'Select Payment Type', 'Choose merchant, user, QR or service.', NULL, 'service-make-payment-steps', 302, 'en', '2026-01-25 04:44:59', '2026-01-25 04:44:59'),
(303, 'default', NULL, 'Enter Details', 'Input amount, ID or scan QR.', NULL, 'service-make-payment-steps', 303, 'en', '2026-01-25 04:45:12', '2026-01-25 04:45:12'),
(304, 'default', NULL, 'Confirm & Pay', 'Review and send securely.', NULL, 'service-make-payment-steps', 304, 'en', '2026-01-25 04:45:24', '2026-01-25 04:45:24'),
(305, 'default', NULL, 'What does “Make Payment” mean?', '“Make Payment” allows you to pay for services, products, or bills directly from your wallet balance.', NULL, 'service-make-payment-faqs', 305, 'en', '2026-01-25 04:45:53', '2026-01-25 04:45:53'),
(306, 'default', NULL, 'Are there any fees for making payments?', 'Some payments may include service or processing fees. Any applicable fee will be shown before confirmation.', NULL, 'service-make-payment-faqs', 306, 'en', '2026-01-25 04:46:08', '2026-01-25 04:46:08'),
(307, 'default', NULL, 'How long does a payment take to process?', 'Most payments are processed instantly. Some may take additional time depending on the provider or network.', NULL, 'service-make-payment-faqs', 307, 'en', '2026-01-25 04:46:22', '2026-01-25 04:46:22'),
(308, 'default', NULL, 'Can I cancel a payment after confirmation?', 'No. Once a payment is confirmed and processed, it cannot be canceled.', NULL, 'service-make-payment-faqs', 308, 'en', '2026-01-25 04:46:36', '2026-01-25 04:46:36'),
(309, 'default', NULL, 'Do I need KYC to make payments?', 'Yes. Identity verification (KYC) may be required to access payment features.', NULL, 'service-make-payment-steps', 309, 'en', '2026-01-25 04:46:57', '2026-01-25 04:46:57'),
(310, 'default', 'global/uploads/images/7Eiib9p5TisuFeNuhOej.png', 'Request Instantly', 'Send a payment request using email, or wallet ID.', NULL, 'service-request-money-features', 310, 'en', '2026-01-25 04:51:03', '2026-01-25 04:51:03'),
(311, 'default', 'global/uploads/images/HymTiQWLd4b0hPHpRDJt.png', 'Custom Amounts', 'Request exact amounts with notes for clarity.', NULL, 'service-request-money-features', 311, 'en', '2026-01-25 04:51:17', '2026-01-25 04:51:17'),
(312, 'default', 'global/uploads/images/wGHTOb8kqYc5hiE9laYx.png', 'Real-Time Notifications', 'Get notified when the request is viewed or paid.', NULL, 'service-request-money-features', 312, 'en', '2026-01-25 04:51:38', '2026-01-25 04:51:38'),
(313, 'default', 'global/uploads/images/pvSZA8EDTK7ZjzRUy9u6.png', 'Request History', 'Track all sent, pending, and completed requests.', NULL, 'service-request-money-features', 313, 'en', '2026-01-25 04:51:57', '2026-01-25 04:51:57'),
(314, 'default', 'global/uploads/images/iPa4WbuhycnkDK2rta28.png', 'Secure Transactions', 'Protected by advanced security and encryption.', NULL, 'service-request-money-features', 314, 'en', '2026-01-25 04:52:13', '2026-01-25 04:52:13'),
(315, 'default', 'global/uploads/images/Y2hklF1kXY1mRQu46tHl.png', 'Multiple Currency Support', 'Request money - supported international currencies.', NULL, 'service-request-money-features', 315, 'en', '2026-01-25 04:52:30', '2026-01-25 04:52:30'),
(316, 'default', NULL, 'Create Request', 'Enter the amount, select recipient, and add a note.', NULL, 'service-request-money-steps', 316, 'en', '2026-01-25 04:52:48', '2026-01-25 04:52:48'),
(317, 'default', NULL, 'Send Request', 'Share the request instantly through Qunzo.', NULL, 'service-request-money-steps', 317, 'en', '2026-01-25 04:52:58', '2026-01-25 04:52:58'),
(318, 'default', NULL, 'Get Paid', 'Receive money directly into your wallet—fast and secure.', NULL, 'service-request-money-steps', 318, 'en', '2026-01-25 04:53:14', '2026-01-25 04:53:14'),
(319, 'default', NULL, 'What does “Request Money” mean?', '“Request Money” allows you to ask another user to send funds to your wallet securely through the platform.', NULL, 'service-request-money-faqs', 319, 'en', '2026-01-25 04:53:50', '2026-01-25 04:53:50'),
(320, 'default', NULL, 'Who can I request money from?', 'You can request money from any registered user on the platform using their username, phone number, or email (if supported).', NULL, 'service-request-money-faqs', 320, 'en', '2026-01-25 04:54:02', '2026-01-25 04:54:02'),
(321, 'default', NULL, 'Does requesting money cost anything?', 'No. Sending a money request is free. Transaction fees may apply only when the payment is completed.', NULL, 'service-request-money-faqs', 321, 'en', '2026-01-25 04:54:22', '2026-01-25 04:54:22'),
(322, 'default', NULL, 'Can I cancel a money request?', 'Yes. You can cancel a request as long as it has not been accepted.', NULL, 'service-request-money-faqs', 322, 'en', '2026-01-25 04:54:37', '2026-01-25 04:54:37'),
(323, 'default', NULL, 'What if the request is declined or ignored?', 'If declined or not responded to, no funds will be transferred and the request will expire after a certain time.', NULL, 'service-request-money-faqs', 323, 'en', '2026-01-25 04:54:51', '2026-01-25 04:54:51'),
(324, 'default', 'global/uploads/images/eB4SowgvtR7e4zC2AaCA.png', 'Instant Transfers', 'Send money instantly to any  user without delays.', NULL, 'service-transfer-features', 324, 'en', '2026-01-25 05:15:20', '2026-01-25 05:15:20'),
(325, 'default', 'global/uploads/images/WTem2VpiZvWasU3EdUMT.png', 'Multiple Transfer Methods', 'Transfer using wallet ID, phone number, or QR code.', NULL, 'service-transfer-features', 325, 'en', '2026-01-25 05:15:40', '2026-01-25 05:15:40'),
(326, 'default', 'global/uploads/images/7SmMeVK8r23tTtRbfOg8.png', 'Real-Time Notifications', 'Both the sender and the receiver receive transaction alerts.', NULL, 'service-transfer-features', 326, 'en', '2026-01-25 05:16:15', '2026-01-25 05:16:15'),
(327, 'default', 'global/uploads/images/k5DljdhTMu2lSXyiF88S.png', 'Transfer History', 'View and track all completed and pending transfers.', NULL, 'service-transfer-features', 327, 'en', '2026-01-25 05:16:42', '2026-01-25 05:16:42'),
(328, 'default', 'global/uploads/images/wfPdACfxJLBDSYt6iJ74.png', 'Secure Transactions', 'Advanced security ensures every transfer', NULL, 'service-transfer-features', 328, 'en', '2026-01-25 05:17:03', '2026-01-25 05:17:03'),
(329, 'default', 'global/uploads/images/ErwM3lhwPMqHmCHL424X.png', 'Multi-Currency Support', 'Send and receive money in supported currencies.', NULL, 'service-transfer-features', 329, 'en', '2026-01-25 05:17:22', '2026-01-25 05:17:22'),
(330, 'default', NULL, 'Choose Recipient', 'Select a contact or enter wallet details.', NULL, 'service-transfer-steps', 330, 'en', '2026-01-25 05:17:38', '2026-01-25 05:17:38'),
(331, 'default', NULL, 'Enter Amount', 'Add the amount and optional note.', NULL, 'service-transfer-steps', 331, 'en', '2026-01-25 05:17:55', '2026-01-25 05:17:55'),
(332, 'default', NULL, 'Confirm & Send', 'Authenticate and send money instantly.', NULL, 'service-transfer-steps', 332, 'en', '2026-01-25 05:18:07', '2026-01-25 05:18:07'),
(333, 'default', NULL, 'What is a transfer?', 'A transfer allows you to send money from your wallet to another wallet or supported account within the platform.', NULL, 'service-transfer-faqs', 333, 'en', '2026-01-25 05:18:30', '2026-01-25 05:18:30'),
(334, 'default', NULL, 'Are there any transfer fees?', 'Transfer fees may apply depending on the transfer type and amount. Any applicable fee will be shown before confirmation.', NULL, 'service-transfer-faqs', 334, 'en', '2026-01-25 05:18:44', '2026-01-25 05:18:44'),
(335, 'default', NULL, 'How long does a transfer take?', 'Most internal transfers are instant. Some transfers may take longer depending on platform rules.', NULL, 'service-transfer-faqs', 335, 'en', '2026-01-25 05:18:57', '2026-01-25 05:18:57'),
(336, 'default', NULL, 'Is there a minimum or maximum transfer limit?', 'Yes. Transfer limits may apply based on your account level and platform policies.', NULL, 'service-transfer-faqs', 336, 'en', '2026-01-25 05:19:12', '2026-01-25 05:19:12'),
(337, 'default', 'global/uploads/images/O9e0uAKumv05Xni6RpBU.png', 'Multiple Withdrawal Options', 'Cash out via agents, bank accounts, or supported payment channels.', NULL, 'service-cash-out-features', 337, 'en', '2026-01-25 05:24:15', '2026-01-25 05:24:15'),
(338, 'default', 'global/uploads/images/dydCklAEPoyVL95fejrk.png', 'Fast Processing', 'Withdrawals are processed quickly based on system configuration.', NULL, 'service-cash-out-features', 338, 'en', '2026-01-25 05:24:33', '2026-01-25 05:24:33'),
(339, 'default', 'global/uploads/images/Jzs552FrGY9mxaJ9gYoL.png', 'Real-Time Status Updates', 'Track your cash-out requests from submission to completion.', NULL, 'service-cash-out-features', 339, 'en', '2026-01-25 05:26:37', '2026-01-25 05:26:37'),
(340, 'default', 'global/uploads/images/rL09LWozgBiuOdN88OBW.png', 'Withdrawal History', 'View all completed, pending, and rejected withdrawals.', NULL, 'service-cash-out-features', 340, 'en', '2026-01-25 05:27:24', '2026-01-25 05:27:24'),
(341, 'default', 'global/uploads/images/HaGGElANRsGgi7zoDtc3.png', 'Secure Verification', 'Withdrawals are protected with authentication and security checks.', NULL, 'service-cash-out-features', 341, 'en', '2026-01-25 05:27:47', '2026-01-25 05:27:47'),
(342, 'default', 'global/uploads/images/aCusvC8d5TZ4mYuiBCMa.png', 'Flexible Limits', 'Custom withdrawal limits based on account type and verification level.', NULL, 'service-cash-out-features', 342, 'en', '2026-01-25 05:28:32', '2026-01-25 05:28:32'),
(343, 'default', NULL, 'Enter Agent AID', 'Enter your agent AID for cashout money.', NULL, 'service-cash-out-steps', 343, 'en', '2026-01-25 05:29:57', '2026-01-25 05:29:57'),
(344, 'default', NULL, 'Enter Amount', 'Specify how much you want to withdraw.', NULL, 'service-cash-out-steps', 344, 'en', '2026-01-25 05:30:12', '2026-01-25 05:30:12'),
(345, 'default', NULL, 'Confirm & Receive', 'Verify the request and receive your funds.', NULL, 'service-cash-out-steps', 345, 'en', '2026-01-25 05:30:26', '2026-01-25 05:30:26'),
(346, 'default', NULL, 'What is a cashout?', 'A cashout allows you to withdraw money from your wallet to a supported bank account, agent, or payout method.', NULL, 'service-cash-out-faqs', 346, 'en', '2026-01-25 05:31:04', '2026-01-25 05:31:04'),
(347, 'default', NULL, 'Are there any cashout fees?', 'Yes. Cashout fees may apply depending on the payout method and amount. All fees are shown before confirmation.', NULL, 'service-cash-out-faqs', 347, 'en', '2026-01-25 05:31:19', '2026-01-25 05:31:19'),
(348, 'default', NULL, 'Is there a minimum or maximum cashout limit?', 'Yes. Minimum and maximum cashout limits may apply.', NULL, 'service-cash-out-faqs', 348, 'en', '2026-01-25 05:31:59', '2026-01-25 05:31:59'),
(350, 'default', NULL, 'Can I cancel a cashout request?', 'Once a cashout request is processed, it cannot be canceled.', NULL, 'service-cash-out-faqs', 350, 'en', '2026-01-25 05:32:21', '2026-01-25 05:32:21'),
(351, 'default', NULL, 'Can I view my cashout history?', 'Yes. All cashout transactions are recorded and available in your transaction history.', NULL, 'service-cash-out-faqs', 351, 'en', '2026-01-25 05:32:49', '2026-01-25 05:32:49'),
(352, 'default', 'global/uploads/images/L15TThUuReCViGElYslY.png', 'Linked Bank Accounts', 'Withdraw directly to your saved and verified bank accounts.', NULL, 'service-withdraw-features', 352, 'en', '2026-01-25 05:45:20', '2026-01-25 05:45:20'),
(353, 'default', 'global/uploads/images/1oJm34xzy8zSBBa3w1Vk.png', 'Fast Processing', 'Withdrawals are processed efficiently based on banking schedules.', NULL, 'service-withdraw-features', 353, 'en', '2026-01-25 05:45:35', '2026-01-25 05:45:35'),
(354, 'default', 'global/uploads/images/wxBnseUQOGp5OMQPZrnE.png', 'Real-Time Status Tracking', 'Track your withdrawal status from request to completion.', NULL, 'service-withdraw-features', 354, 'en', '2026-01-25 05:45:53', '2026-01-25 05:45:53'),
(355, 'default', 'global/uploads/images/SxMPwfZVqBzl01eSOyXB.png', 'Withdrawal Records', 'View detailed history of all successful and pending withdrawals.', NULL, 'service-withdraw-features', 355, 'en', '2026-01-25 05:46:12', '2026-01-25 05:46:12'),
(356, 'default', 'global/uploads/images/sHd1AWezPkXmvFmVmzyS.png', 'Secure Authentication', 'Every withdrawal requires verification for added safety.', NULL, 'service-withdraw-features', 356, 'en', '2026-01-25 05:46:30', '2026-01-25 05:46:30'),
(357, 'default', 'global/uploads/images/j0EwEAkUvXkaOxwwQMDu.png', 'Configurable Limits', 'Withdrawal limits are set by admin and account verification level.', NULL, 'service-withdraw-features', 357, 'en', '2026-01-25 05:46:47', '2026-01-25 05:46:47'),
(358, 'default', NULL, 'Select Withdraw Account', 'Choose your linked bank account.', NULL, 'service-withdraw-steps', 358, 'en', '2026-01-25 05:47:09', '2026-01-25 05:47:09'),
(359, 'default', NULL, 'Enter Withdrawal Amount', 'Specify the amount you want to withdraw.', NULL, 'service-withdraw-steps', 359, 'en', '2026-01-25 05:47:22', '2026-01-25 05:47:22'),
(360, 'default', NULL, 'Confirm & Submit', 'Verify the details and submit your request.', NULL, 'service-withdraw-steps', 360, 'en', '2026-01-25 05:47:35', '2026-01-25 05:47:35'),
(361, 'default', NULL, 'What is a withdrawal?', 'A withdrawal allows you to move funds from your wallet to a supported external account or payout method.', NULL, 'service-withdraw-faqs', 361, 'en', '2026-01-25 05:48:17', '2026-01-25 05:48:17'),
(362, 'default', NULL, 'Are there any withdrawal fees?', 'Yes. Withdrawal fees may apply depending on the method and amount. All fees are displayed before confirmation.', NULL, 'service-withdraw-faqs', 362, 'en', '2026-01-25 05:48:30', '2026-01-25 05:48:30'),
(363, 'default', NULL, 'Is there a minimum or maximum withdrawal limit?', 'Yes. Withdrawal limits depend on your account level and selected withdrawal method.', NULL, 'service-withdraw-faqs', 363, 'en', '2026-01-25 05:48:43', '2026-01-25 05:48:43'),
(364, 'default', NULL, 'Can I cancel a withdrawal request?', 'Once a withdrawal request is submitted or processed, it cannot be canceled.', NULL, 'service-withdraw-faqs', 364, 'en', '2026-01-25 05:48:55', '2026-01-25 05:48:55'),
(365, 'default', NULL, 'Can I track my withdrawal status?', 'Yes. You can view the withdrawal status and history in your transaction records.', NULL, 'service-withdraw-faqs', 365, 'en', '2026-01-25 05:49:11', '2026-01-25 05:49:11'),
(366, 'default', 'global/uploads/images/Xan6wCfVAv7EKzDTILEu.png', 'Multi-Currency Wallet', 'Hold and manage multiple currencies in one wallet.', NULL, 'service-exchange-features', 366, 'en', '2026-01-25 05:52:04', '2026-01-25 05:52:04'),
(367, 'default', 'global/uploads/images/7JHW5h1xcvLJV9R6mmYd.png', 'Secure Processing', 'All exchanges are protected with secure authentication in Qunzo.', NULL, 'service-exchange-features', 367, 'en', '2026-01-25 05:52:21', '2026-01-25 05:52:21'),
(368, 'default', 'global/uploads/images/GrfB5SBXF0Rl0uOB12y8.png', 'Instant Conversion', 'Funds are converted instantly within your wallet.', NULL, 'service-exchange-features', 368, 'en', '2026-01-25 05:52:40', '2026-01-25 05:52:40'),
(369, 'default', 'global/uploads/images/RxKnf48iqrzH7UkaOT9X.png', 'Exchange History', 'Track all completed and pending exchange transactions.', NULL, 'service-exchange-features', 369, 'en', '2026-01-25 05:52:58', '2026-01-25 05:52:58'),
(370, 'default', 'global/uploads/images/OfE5f09vuxzWK9X2gpwj.png', 'Real-Time Rates', 'Exchange using live rates configured by the system.', NULL, 'service-exchange-features', 370, 'en', '2026-01-25 05:53:19', '2026-01-25 05:53:19'),
(371, 'default', 'global/uploads/images/afYE8wYnAbQnal0ctK5Z.png', 'Configurable Limits', 'Exchange limits depend on admin settings.', NULL, 'service-exchange-features', 371, 'en', '2026-01-25 05:53:51', '2026-01-25 05:53:51'),
(372, 'default', NULL, 'Select From Currency and To Currency', 'Choose the currency you want to exchange from and to.', NULL, 'service-exchange-steps', 372, 'en', '2026-01-25 05:54:34', '2026-01-25 05:54:34'),
(373, 'default', NULL, 'Enter Amount', 'Input the amount to convert and view the rate.', NULL, 'service-exchange-steps', 373, 'en', '2026-01-25 05:54:46', '2026-01-25 05:54:46'),
(374, 'default', NULL, 'Confirm & Exchange', 'Verify details and complete the exchange instantly.', NULL, 'service-exchange-steps', 374, 'en', '2026-01-25 05:54:58', '2026-01-25 05:54:58'),
(375, 'default', NULL, 'What is money exchange?', 'Money exchange allows you to convert funds from one currency to another within your wallet using the current exchange rate.', NULL, 'service-exchange-faqs', 375, 'en', '2026-01-25 05:55:25', '2026-01-25 05:55:25'),
(376, 'default', NULL, 'Which currencies can I exchange?', 'You can exchange between supported currencies. Available currencies depend on platform support.', NULL, 'service-exchange-faqs', 376, 'en', '2026-01-25 05:55:47', '2026-01-25 05:55:47'),
(377, 'default', NULL, 'What exchange rate is used?', 'The exchange rate is based on real-time or provider-defined rates and is shown before you confirm the exchange.', NULL, 'service-exchange-faqs', 377, 'en', '2026-01-25 05:56:03', '2026-01-25 05:56:03'),
(378, 'default', NULL, 'How long does an exchange take?', 'Most exchanges are processed instantly. Some may take a short time depending on system or provider availability.', NULL, 'service-exchange-faqs', 378, 'en', '2026-01-25 05:56:15', '2026-01-25 05:56:15'),
(379, 'default', 'global/uploads/images/eon4Kp5odhbb0SjZOTcU.png', 'Custom Payment Links Creation', 'Create invoices with amount, description, and due date.', NULL, 'service-payment-links-features', 379, 'en', '2026-01-25 06:13:22', '2026-01-25 06:13:22'),
(380, 'default', 'global/uploads/images/KVEBgAFgUFqT9w7N9rER.png', 'Instant Payment Collection', 'Customers can pay invoices directly from their wallets.', NULL, 'service-payment-links-features', 380, 'en', '2026-01-25 06:13:38', '2026-01-25 06:13:38');
INSERT INTO `landing_contents` (`id`, `theme`, `icon`, `title`, `description`, `photo`, `type`, `locale_id`, `locale`, `created_at`, `updated_at`) VALUES
(381, 'default', 'global/uploads/images/VTrpgIYmDzopaWFi7f5l.png', 'Link Status Tracking', 'Monitor paid, pending, and expired invoices in real time.', NULL, 'service-payment-links-features', 381, 'en', '2026-01-25 06:13:54', '2026-01-25 06:14:08'),
(382, 'default', 'global/uploads/images/i9WvXFbm1a4avdLd9PMX.png', 'Payment Links History', 'Access detailed records of all created and paid links.', NULL, 'service-payment-links-features', 382, 'en', '2026-01-25 06:14:33', '2026-01-25 06:14:33'),
(383, 'default', 'global/uploads/images/TqkMo3R28IlPM2DjseoG.png', 'Secure Payments', 'Payment Links are protected with secure authentication and verification.', NULL, 'service-payment-links-features', 383, 'en', '2026-01-25 06:14:57', '2026-01-25 06:14:57'),
(384, 'default', 'global/uploads/images/UuLMaYTWaqywQL5sbJLL.png', 'Configurable Limits', 'Payment link limits are managed by the admin.', NULL, 'service-payment-links-features', 384, 'en', '2026-01-25 06:15:30', '2026-01-25 06:15:30'),
(385, 'default', NULL, 'Create Link', 'Enter details and generate a payment link.', NULL, 'service-payment-links-steps', 385, 'en', '2026-01-25 06:16:06', '2026-01-25 06:16:06'),
(386, 'default', NULL, 'Share the link', 'Send the payment link or notification to the payer.', NULL, 'service-payment-links-steps', 386, 'en', '2026-01-25 06:16:28', '2026-01-25 06:16:28'),
(387, 'default', NULL, 'Get Paid', 'Receive payment instantly into your wallet once paid.', NULL, 'service-payment-links-steps', 387, 'en', '2026-01-25 06:16:41', '2026-01-25 06:16:41'),
(388, 'default', NULL, 'What is a payment link?', 'A payment link is a secure URL that lets your customers pay you online without needing a full online store or account.', NULL, 'service-payment-links-faqs', 388, 'en', '2026-01-25 06:18:08', '2026-01-25 06:18:08'),
(390, 'default', NULL, 'How do customers pay using a payment link?', 'Customers click the link, open a secure payment page, and complete the payment using cards, mobile wallets, or other supported methods.', NULL, 'service-payment-links-faqs', 390, 'en', '2026-01-25 06:18:35', '2026-01-25 06:18:35'),
(391, 'default', NULL, 'Is it safe to share a payment link?', 'Yes. Payment links are secure and encrypted. Share them only with intended customers to prevent misuse.', NULL, 'service-payment-links-faqs', 391, 'en', '2026-01-25 06:18:48', '2026-01-25 06:18:48'),
(392, 'default', NULL, 'Can payment links support multiple currencies?', 'Depending on the gateway, payment links may accept multiple currencies, converting automatically to your settlement currency.', NULL, 'service-payment-links-faqs', 392, 'en', '2026-01-25 06:19:25', '2026-01-25 06:19:25'),
(393, 'default', 'global/uploads/images/X706OLMqI9s0Ug26p6f4.png', 'Instant Gift Code Creation', 'Generate unique gift codes directly from your wallet.', NULL, 'service-gift-code-features', 393, 'en', '2026-01-25 06:22:27', '2026-01-25 06:22:36'),
(394, 'default', 'global/uploads/images/caNGfuvCkGg7EaF5Up3q.png', 'Flexible Amounts', 'Create gift codes with custom amounts.', NULL, 'service-gift-code-features', 394, 'en', '2026-01-25 06:22:54', '2026-01-25 06:22:54'),
(395, 'default', 'global/uploads/images/Da445VrOQRnG3yYdhgux.png', 'Anyone Redeem Gift', 'After created gift code, anyone can able to redeem this code within the system.', NULL, 'service-gift-code-features', 395, 'en', '2026-01-25 06:24:24', '2026-01-25 06:24:24'),
(396, 'default', 'global/uploads/landing-list-assets/service-gift-code-features/icons//TcaLbYik3j1vkdR3IRgK.png', 'Easy Redemption', 'Recipients can redeem codes instantly in their wallet.', NULL, 'service-gift-code-features', 396, 'en', '2026-01-25 06:24:45', '2026-01-25 06:25:54'),
(397, 'default', 'global/uploads/images/65yEOiOKHiR1Eclvx3HL.png', 'Gift Code Tracking', 'Track active, redeemed, and expired gift codes.', NULL, 'service-gift-code-features', 397, 'en', '2026-01-25 06:25:12', '2026-01-25 06:25:12'),
(398, 'default', 'global/uploads/images/rCrHpTxSImd0oPNHlB53.png', 'Secure & Unique Codes', 'Each gift code is uniquely generated and protected.', NULL, 'service-gift-code-features', 398, 'en', '2026-01-25 06:26:16', '2026-01-25 06:26:16'),
(399, 'default', NULL, 'Create Gift Code', 'Choose an amount and generate a unique gift code.', NULL, 'service-gift-code-steps', 399, 'en', '2026-01-25 06:26:38', '2026-01-25 06:26:38'),
(400, 'default', NULL, 'Share Code', 'Send the code via message, email, or any platform.', NULL, 'service-gift-code-steps', 400, 'en', '2026-01-25 06:26:48', '2026-01-25 06:26:48'),
(401, 'default', NULL, 'Redeem & Enjoy', 'Recipient redeems the code and receives funds instantly.', NULL, 'service-gift-code-steps', 401, 'en', '2026-01-25 06:27:03', '2026-01-25 06:27:03'),
(402, 'default', NULL, 'What is a gift code?', 'A gift code is a prepaid code that can be redeemed for a specific value or product in your system. It’s like a digital gift card.', NULL, 'service-gift-code-faqs', 402, 'en', '2026-01-25 06:28:05', '2026-01-25 06:28:05'),
(403, 'default', NULL, 'How do I get a gift code?', 'Gift codes can be purchased from your platform, received as a promotion, or sent by another user.', NULL, 'service-gift-code-faqs', 403, 'en', '2026-01-25 06:28:41', '2026-01-25 06:28:41'),
(404, 'default', NULL, 'How do I redeem a gift code?', 'Enter the code in the designated section of your app or website. The value will be added to your account or applied to a purchase.', NULL, 'service-gift-code-faqs', 404, 'en', '2026-01-25 06:28:53', '2026-01-25 06:28:53'),
(407, 'default', NULL, 'Can I use a gift code multiple times?', 'Usually, gift codes are single-use. Once redeemed, the code cannot be used again unless explicitly stated otherwise.', NULL, 'service-gift-code-faqs', 405, 'en', '2026-01-25 06:29:32', '2026-01-25 06:29:32'),
(408, 'default', NULL, 'Can I get a refund for a gift code?', 'Typically, gift codes are non-refundable once issued, unless the platform’s policy allows it.', NULL, 'service-gift-code-faqs', 408, 'en', '2026-01-25 06:29:49', '2026-01-25 06:29:49'),
(642, 'default', NULL, 'Choose Gift Card', 'Choose a gift card value that fits your budget.', NULL, 'gift-cards-how-it-works', 409, 'en', '2026-02-07 08:31:56', '2026-02-07 08:31:56'),
(643, 'default', NULL, 'Enter Details', 'Add recipient email or phone number.', NULL, 'gift-cards-how-it-works', 643, 'en', '2026-02-07 08:32:26', '2026-02-07 08:32:26'),
(644, 'default', NULL, 'Confirm Quantity', 'Select how many gift cards you want.', NULL, 'gift-cards-how-it-works', 644, 'en', '2026-02-07 08:32:42', '2026-02-07 08:32:42'),
(645, 'default', NULL, 'Pay & Send', 'Complete payment and send details to your recipient\'s email instantly.', NULL, 'gift-cards-how-it-works', 645, 'en', '2026-02-07 08:33:19', '2026-02-07 08:33:19'),
(646, 'default', NULL, 'What is a Gift Card?', 'A virtual gift card is a digital code that can be redeemed online or in-store (where supported). It’s delivered instantly and doesn’t require a physical card.', NULL, 'gift-cards-faqs', 646, 'en', '2026-02-07 08:34:21', '2026-02-07 08:34:21'),
(647, 'default', NULL, 'How fast will I receive my gift card?', 'Most gift cards are delivered instantly after successful payment. In rare cases, delivery may take a few minutes due to verification checks.', NULL, 'gift-cards-faqs', 647, 'en', '2026-02-07 08:34:38', '2026-02-07 08:34:38'),
(648, 'default', NULL, 'Can I use gift cards internationally?', 'Yes, but gift cards are country or region-specific. Make sure to purchase a card that matches the country where you plan to redeem it.', NULL, 'gift-cards-faqs', 648, 'en', '2026-02-07 08:34:59', '2026-02-07 08:34:59'),
(649, 'default', NULL, 'Are there any fees or hidden charges?', 'No hidden fees. Any applicable service charge or conversion fee is clearly shown before checkout.', NULL, 'gift-cards-faqs', 649, 'en', '2026-02-07 08:35:17', '2026-02-07 08:35:17');

-- --------------------------------------------------------

--
-- Table structure for table `landing_pages`
--

CREATE TABLE `landing_pages` (
  `id` bigint UNSIGNED NOT NULL,
  `theme` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint DEFAULT NULL,
  `sort` int DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `landing_pages`
--

INSERT INTO `landing_pages` (`id`, `theme`, `name`, `code`, `data`, `status`, `sort`, `locale`, `created_at`, `updated_at`) VALUES
(25, 'default', 'Hero Section', 'hero', '{\"hero_title\":\"Experience the Power of Modern Payments with [[color_text= Qunzo]].\",\"hero_description\":\"Empower your digital lifestyle with Qunzo, next-generation payments made simple, secure, and borderless for everyone, everywhere.\",\"bonus_text\":\"Fast, secure, and seamless \\u2014 the way payments should be.\",\"bubble_text\":\"Our client satisfy\",\"bubble_counter\":\"80+\",\"image\":\"global\\/uploads\\/landing-contents\\/image\\/Tdr0atSiwBUqULMzgisa.jpg\",\"button_label\":\"Get Started\",\"button_url\":\"\\/auth\\/login\",\"button_target\":\"_self\",\"rounded_button_label\":\"About\",\"rounded_button_url\":\"\\/about\",\"rounded_button_target\":\"_blank\",\"rounded_button_image\":\"global\\/uploads\\/landing-contents\\/rounded_button_image\\/OUo5Y0GHyEKzbI80RXAg.png\",\"bubble_image\":\"global\\/uploads\\/landing-contents\\/bubble_image\\/DQHJBD4Mfav5EcWOaQHI.webp\"}', 1, 1, 'en', NULL, '2026-01-20 08:46:07'),
(26, 'default', 'Features Section', 'features', '{\"features_title\":\"Qunzo [[color_text= Features ]]\",\"features_subtitle\":\"Discover how Qunzo makes every transaction faster, safer, and smarter empowering your daily financial activities with ease.\"}', 1, 5, 'en', NULL, '2025-12-10 05:31:10'),
(27, 'default', 'About Section', 'about', '{\"title\":\"[[color_text= What ]]  We Offer\"}', 1, 4, 'en', NULL, '2025-12-10 05:31:10'),
(29, 'default', 'FAQ Section', 'faqs', '{\"faq_title\":\"Frequently Asked [[color_text=Questions]]\",\"faq_subtitle\":\"Find quick answers to common questions about our  Qunzo app.\",\"image\":\"global\\/uploads\\/landing-contents\\/image\\/4E5SZpqjSRqSv6dZ1C3Y.webp\"}', 1, 8, 'en', NULL, '2025-12-10 05:31:10'),
(31, 'default', 'Marketing Section', 'marketing', '{\"merchant_title\":\"Looking for a [[color_text=  Business Account?]]\",\"merchant_subtitle\":\"Accepting payments as a merchant opens up new opportunities for businesses by enabling fast, secure, and borderless transactions.\",\"button_label\":\"Explore\",\"button_url\":\"\\/agent\",\"button_target\":\"_self\",\"agent_title\":\"Looking for a [[color_text=  Agent Account?]]\",\"agent_subtitle\":\"Becoming a payment agent allows individuals and businesses to facilitate digital transactions while earning commissions.\",\"merchant_image\":\"global\\/uploads\\/landing-contents\\/merchant_image\\/SFnga0pevC2Nb6EtBZQ8.svg\",\"agent_image\":\"global\\/uploads\\/landing-contents\\/agent_image\\/A5DK6meMYTr2VEXoPBLN.svg\",\"merchant_button_label\":\"Explore\",\"merchant_button_url\":\"\\/merchant\",\"merchant_button_target\":\"_self\"}', 1, 6, 'en', '2025-03-20 08:07:55', '2025-12-10 05:31:10'),
(33, 'default', 'Testimonial Section', 'testimonial', '{\"testimonial_title\":\"Trusted by Users Worldwide\",\"sub_title\":\"Don\'t just take our word for it. Here\'s what our community has to say about their experience.\"}', 1, 9, 'en', '2025-03-20 08:07:55', '2026-01-20 08:56:54'),
(34, 'default', 'Blog Section', 'blog', '{\"blog_title\":\"Our [[color_text=Blogs]]\",\"blog_subtitle\":\"Explore expert tips, platform updates, and the latest trends in digital payments.\"}', 1, 10, 'en', '2025-03-20 08:07:55', '2025-12-10 05:31:10'),
(35, 'default', 'Newsletter Section', 'newsletter', '{\"title\":\"Join With [[color_text= Qunzo ]] Platform\",\"subtitle\":\"Join thousands of users who stay informed about platform updates, new payment gateways, and exclusive promotions.\",\"button_label\":\"Join Now\",\"button_url\":\"\\/register\",\"button_target\":\"_self\"}', 1, 11, 'en', '2025-03-20 08:07:55', '2025-12-10 05:31:10'),
(36, 'default', 'Footer Section\r\n', 'footer', '{\"newsletter_title\":\"Empowering the future of digital payments.\",\"newsletter_sub_title\":\"By subscribing you agree to with our Privacy Policy and provide consent to receive updates from our company.\",\"copyright_text\":\"Qunzo \\u00a9 2025. All rights reserved.\",\"widget_title_1\":\"Company\",\"widget_title_2\":\"Important Links\",\"widget_title_3\":\"Help Center\",\"newsletter_description\":\"Stay updated with the latest in payments, and financial innovation.\",\"footer_bottom_text\":\"Qunzo\"}', 1, 15, 'en', '2022-10-22 07:54:48', '2025-12-17 04:08:33'),
(39, 'default', 'Counter Section', 'counter', '{\"title\":\"We are Helping Customer Globally Every Time\"}', 1, 2, 'en', NULL, '2026-01-03 09:44:19'),
(40, 'default', 'Gateway Section', 'gateway', '{}', 1, 7, 'en', NULL, '2025-12-10 05:31:10'),
(41, 'default', 'How It Works Section', 'howitworks', '{\"title\":\"How It [[color_text= Works]]\",\"subtitle\":\"Discover how Qunzo works\"}', 1, 3, 'en', NULL, '2025-12-10 05:31:10'),
(66, 'default', 'App Section', 'app', '{\"title\":\"Download and Explore Our App\",\"subtitle\":\"Get the app and start managing your finances anytime, anywhere.\",\"app_store_link\":\"#\",\"play_store_link\":\"#\",\"image\":\"global\\/uploads\\/landing-contents\\/image\\/luZ75gBG1fJI23WvLNWS.png\",\"app_store_image\":\"global\\/uploads\\/landing-contents\\/app_store_image\\/tFZHwsVBLhTmCaqBSLNc.svg\",\"play_store_image\":\"global\\/uploads\\/landing-contents\\/play_store_image\\/5FVFbtOJANi0YgPfbOQj.svg\"}', 1, 12, 'en', NULL, '2026-01-25 10:40:40');

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` bigint UNSIGNED NOT NULL,
  `flag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_rtl` tinyint(1) NOT NULL DEFAULT '0',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `flag`, `name`, `locale`, `is_rtl`, `is_default`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 'English', 'en', 0, 0, 1, NULL, '2025-04-13 11:56:52');

-- --------------------------------------------------------

--
-- Table structure for table `level_referrals`
--

CREATE TABLE `level_referrals` (
  `id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `the_order` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bounty` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `level_referrals`
--

INSERT INTO `level_referrals` (`id`, `type`, `the_order`, `bounty`, `created_at`, `updated_at`) VALUES
(1, 'deposit', '1', 10, '2025-03-06 06:05:53', '2025-03-06 06:05:53'),
(2, 'transfer', '1', 5, '2025-03-06 06:06:42', '2025-03-06 06:06:42'),
(3, 'cash_out', '1', 6, '2025-03-06 06:06:48', '2025-03-06 06:07:56'),
(5, 'payment', '1', 3, '2025-03-06 06:07:04', '2025-03-06 06:07:04'),
(6, 'invoice_pay', '1', 6, '2025-03-06 06:07:13', '2025-03-06 06:07:13'),
(7, 'withdraw', '1', 5, '2025-03-06 06:07:26', '2025-03-06 06:07:26'),
(8, 'create_gift', '1', 2, '2025-03-06 06:07:34', '2025-03-06 06:07:34'),
(9, 'exchange', '1', 10, '2025-03-06 06:07:45', '2025-03-06 06:07:45'),
(11, 'request_money', '1', 5, '2025-03-09 06:23:20', '2025-03-09 06:23:20'),
(12, 'transfer', '2', 20, '2025-03-09 06:48:34', '2025-03-09 06:48:34');

-- --------------------------------------------------------

--
-- Table structure for table `login_activities`
--

CREATE TABLE `login_activities` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int DEFAULT NULL,
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `megamenu_items`
--

CREATE TABLE `megamenu_items` (
  `id` bigint UNSIGNED NOT NULL,
  `navigation_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preview_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preview_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `page_id` bigint UNSIGNED DEFAULT NULL,
  `preview_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `translate` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `megamenu_items`
--

INSERT INTO `megamenu_items` (`id`, `navigation_id`, `title`, `description`, `icon`, `url`, `preview_title`, `preview_description`, `page_id`, `preview_image`, `is_featured`, `sort_order`, `status`, `translate`, `created_at`, `updated_at`) VALUES
(2, 2, 'Virtual Cards', 'Issue virtual cards instantly for cashless transactions.', 'global/uploads/megamenu/icons/w0ruOXCrbK6lgZ4X1IRi.svg', 'virtual-cards', 'Fast Virtual Cards', 'A secure virtual card for fast, simple, and cashless digital payments.', 59, 'global/uploads/megamenu/fYOCkvCbXuoCxZFg5cuD.webp', 1, 1, 1, NULL, '2026-01-19 06:37:39', '2026-01-25 06:31:11'),
(3, 2, 'Bill Payment', 'Pay your bills quickly and securely', 'global/uploads/megamenu/icons/NmrxJGjs37yPSwslAGuW.svg', 'bill-payment', 'Bill Payment', 'Settle your bills quickly without standing in lines.', 61, 'global/uploads/megamenu/wksVdwxDxusSZh0mjhhI.png', 0, 3, 1, NULL, '2026-01-19 06:46:44', '2026-01-25 06:31:48'),
(4, 2, 'Mobile Topup', 'Recharge your mobile instantly', 'global/uploads/megamenu/icons/mfpR3ynRMBjnVczyqqRm.png', 'mobile-recharge', 'Mobile Topup', 'Recharge mobile balances instantly in just a few taps.', 60, 'global/uploads/megamenu/xKptQtgYMbd8LHxQ7Ol0.png', 0, 2, 0, NULL, '2026-01-19 06:48:00', '2026-01-25 06:31:36'),
(5, 2, 'Agent', 'Cash in on users and earn commissions.', 'global/uploads/megamenu/icons/StFoVMvBHGoSOpaobZxR.png', 'agent', 'Agent', 'Empower your business by operating as a digital payment agent.', 34, 'global/uploads/megamenu/8kT9eLkXobdZrpwPDDRX.png', 0, 5, 1, NULL, '2026-01-19 06:49:58', '2026-01-25 06:32:19'),
(6, 2, 'P2P Trading', 'Trade directly with verified users', 'global/uploads/megamenu/icons/YmGzry7LQv6ruhsLqAji.png', 'p2p-trading', 'P2P Trading', 'Transfer funds instantly between users with full transparency.', 62, 'global/uploads/megamenu/7VMJbF8HTbInEbMZGI3U.png', 0, 4, 0, NULL, '2026-01-19 06:51:14', '2026-01-25 06:31:59'),
(7, 2, 'Merchant', 'Businesses that accept payments', 'global/uploads/megamenu/icons/8UT9tbN35DPGtisns3lN.png', 'merchant', 'Merchant', 'Handle customer transactions and grow your business.', 33, 'global/uploads/megamenu/HcgOvEZrzJvRkVrYKuO1.png', 0, 6, 1, NULL, '2026-01-19 06:53:34', '2026-01-25 06:32:33'),
(8, 39, 'Wallets', 'A secure hub for all your digital funds.', 'global/uploads/megamenu/icons/qkZs2P4A5uvfzvTk3epl.png', 'wallets', '', '', 63, NULL, 0, 1, 1, NULL, '2026-01-20 05:08:25', '2026-01-25 06:33:49'),
(9, 39, 'Add Money', 'Quickly load your wallet via bank, card, or mobile banking.', 'global/uploads/megamenu/icons/0vjoxPtpQoBB1nDjLexV.png', 'add-money', '', '', 64, NULL, 0, 2, 1, NULL, '2026-01-20 05:09:05', '2026-01-25 06:34:02'),
(10, 39, 'Request Money', 'Send a request and get paid instantly by other users.', 'global/uploads/megamenu/icons/1F0SPsrBt3XxwM02eig0.svg', 'request-money', '', '', 66, NULL, 0, 3, 1, NULL, '2026-01-20 05:09:40', '2026-01-25 06:34:24'),
(11, 39, 'Transfer', 'Send money to friends, family, or businesses safely.', 'global/uploads/megamenu/icons/3r4fSRyWaCmvgYoocW6g.png', 'transfer', '', '', 67, NULL, 0, 4, 1, NULL, '2026-01-20 05:10:18', '2026-01-25 06:34:38'),
(12, 39, 'Cash Out', 'Withdraw your balance directly to your bank or wallet.', 'global/uploads/megamenu/icons/MWrNWuooT0PFTVO4FyOO.png', 'cash-out', '', '', 68, NULL, 0, 5, 1, NULL, '2026-01-20 05:10:50', '2026-01-26 03:46:43'),
(13, 39, 'Withdraw', 'Move funds to your preferred withdrawal option securely.', 'global/uploads/megamenu/icons/VOTDTzpZJMntCmvoAdy0.png', 'withdraw', '', '', 69, NULL, 0, 6, 1, NULL, '2026-01-20 05:11:21', '2026-01-25 06:34:50'),
(14, 39, 'Bill Payments', 'Settle utility and service bills easily from one place.', 'global/uploads/megamenu/icons/yVsE0GOwkUOzsJOFHhmO.png', 'bill-payment', '', '', 61, NULL, 0, 7, 1, NULL, '2026-01-20 05:11:41', '2026-01-25 06:35:16'),
(15, 39, 'Exchange', 'Convert currencies at competitive rates instantly.', 'global/uploads/megamenu/icons/fJbjGb59hHX0LqjWOnIh.png', 'exchange', '', '', 70, NULL, 0, 8, 1, NULL, '2026-01-20 05:12:05', '2026-01-25 06:35:32'),
(16, 39, 'Payment Links', 'Create payment links, share with your recipient, and receive money.', 'global/uploads/megamenu/icons/e1baFXyycZybKjAVBruk.png', 'payment-links', '', '', 71, NULL, 0, 9, 1, NULL, '2026-01-20 05:12:28', '2026-01-25 06:35:44'),
(17, 39, 'Gift Code', 'Redeem or send gift codes to add balance instantly.', 'global/uploads/megamenu/icons/VwSXX3B1fgUMDZeo8nFa.png', 'gift-code', '', '', 72, NULL, 0, 10, 1, NULL, '2026-01-20 05:13:59', '2026-01-25 06:35:56'),
(18, 2, 'Gift Card', 'Buy and send digital gift cards', 'global/uploads/megamenu/icons/yFrHMqXWkGUnMcvLW4rF.svg', 'gift-cards', 'Gift Card', 'Buy and send digital gift cards instantly with secure, cashless payments.', 96, 'global/uploads/megamenu/rwDIAFjqcHTOA9nMy3rB.svg', 0, 7, 1, NULL, '2026-02-07 08:42:31', '2026-02-07 08:42:31');

-- --------------------------------------------------------

--
-- Table structure for table `merchants`
--

CREATE TABLE `merchants` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `public_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `secret_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected','disabled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint UNSIGNED NOT NULL,
  `model` enum('user','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `user_id` bigint UNSIGNED NOT NULL,
  `ticket_id` bigint UNSIGNED NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attach` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(6, '2024_08_18_061828_create_settings_table', 1),
(7, '2024_08_18_063514_create_admins_table', 1),
(8, '2024_08_18_071630_create_permission_tables', 1),
(10, '2024_08_18_083253_create_gateways_table', 1),
(11, '2024_08_18_090031_create_notifications_table', 1),
(12, '2024_08_18_095609_create_kycs_table', 1),
(13, '2024_08_18_101453_create_user_kycs_table', 1),
(14, '2024_08_19_041604_create_plugins_table', 1),
(16, '2024_08_19_043611_create_tickets_table', 1),
(18, '2024_08_19_044159_create_navigations_table', 1),
(19, '2024_08_19_044603_create_themes_table', 1),
(22, '2024_08_19_044845_create_custom_csses_table', 1),
(23, '2024_08_19_045416_create_level_referrals_table', 1),
(25, '2024_08_19_050813_create_withdrawal_schedules_table', 1),
(27, '2024_08_19_051434_create_login_activities_table', 1),
(29, '2024_08_20_091013_create_cron_jobs_table', 1),
(30, '2024_08_20_091118_create_cron_job_logs_table', 1),
(31, '2024_08_20_091341_create_blogs_table', 1),
(32, '2024_08_20_092532_create_set_tunes_table', 1),
(33, '2024_08_20_092845_create_page_settings_table', 1),
(35, '2024_08_20_093138_create_testimonials_table', 1),
(37, '2024_09_08_121409_create_withdraw_accounts_table', 1),
(38, '2024_09_19_054431_create_referrals_table', 1),
(39, '2024_11_14_040813_create_messages_table', 1),
(42, '2018_08_29_205156_create_translations_table', 2),
(44, '2018_08_29_200844_create_languages_table', 3),
(45, '2024_11_23_084815_create_languages_table', 4),
(51, '2024_11_20_045313_create_merchants_table', 5),
(52, '2024_11_24_060620_create_merchant_forms_table', 5),
(53, '2024_11_30_094656_create_agents_table', 6),
(54, '2024_11_30_094702_create_agent_forms_table', 6),
(60, '2025_01_04_091256_create_personal_access_tokens_table', 10),
(64, '2024_11_17_061922_create_currencies_table', 12),
(66, '0001_01_01_000000_create_users_table', 13),
(67, '2024_08_18_082613_create_transactions_table', 13),
(69, '2024_08_19_050624_create_withdraw_methods_table', 13),
(70, '2024_08_19_050945_create_deposit_methods_table', 13),
(71, '2024_11_17_105848_create_user_wallets_table', 13),
(73, '2024_12_29_114936_create_invoices_table', 13),
(75, '2025_01_08_092924_create_sandbox_transactions_table', 13),
(76, '2024_12_30_035108_create_money_requests_table', 14),
(77, '2024_12_10_110437_create_gifts_table', 15),
(80, '2024_08_20_053202_create_templates_table', 16),
(81, '2024_08_19_043507_create_subscribers_table', 17),
(84, '2024_08_19_044604_create_landing_pages_table', 18),
(85, '2024_08_19_044605_create_landing_contents_table', 18),
(86, '2024_08_19_044052_create_pages_table', 19),
(88, '2024_08_20_093008_create_socials_table', 20),
(90, '2024_08_20_093321_create_user_navigations_table', 21),
(91, '2025_08_19_174218_create_telescope_entries_table', 22),
(92, '2025_09_07_093943_add_current_step_column_to_users_table', 23),
(93, '2025_09_15_045428_add_for_column_to_kycs_table', 24),
(94, '2025_09_16_053110_drop_data_column_from_users', 25),
(95, '2025_10_30_103209_create_beneficiaries_table', 26),
(96, '2025_11_06_084029_create_user_devices_table', 27),
(97, '2025_08_11_165827_add_columns_to_admins_table', 28),
(98, '2025_12_03_102053_create_bills_table', 28),
(99, '2025_12_03_102234_create_bill_services_table', 28),
(100, '2025_12_10_092302_add_passcode_column_to_users_table', 29),
(101, '2025_12_22_083821_create_card_holders_table', 30),
(102, '2025_12_22_083914_create_cards_table', 30),
(103, '2026_01_04_090457_change_invoices_table', 31),
(106, '2026_01_19_061429_create_megamenu_items_table', 32),
(107, '2026_01_19_061440_add_has_megamenu_to_navigations_table', 32),
(108, '2026_01_20_042858_add_megamenu_support_columns', 33),
(109, '2026_01_20_061038_add_megamenu_name_to_navigations_table', 34),
(110, '2026_01_24_105249_add_service_type_to_pages_table', 35),
(112, '2026_02_05_061934_create_gift_cards_table', 36);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\Admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `money_requests`
--

CREATE TABLE `money_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `requester_user_id` bigint UNSIGNED NOT NULL,
  `recipient_user_id` bigint UNSIGNED NOT NULL,
  `currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(20,8) NOT NULL,
  `charge` decimal(20,8) NOT NULL,
  `final_amount` decimal(20,8) NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('success','pending','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `navigations`
--

CREATE TABLE `navigations` (
  `id` bigint UNSIGNED NOT NULL,
  `page_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `megamenu_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` json DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `footer_position` int DEFAULT NULL,
  `header_position` int DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `has_megamenu` tinyint(1) NOT NULL DEFAULT '0',
  `megamenu_type` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `translate` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `navigations`
--

INSERT INTO `navigations` (`id`, `page_id`, `name`, `megamenu_name`, `type`, `url`, `footer_position`, `header_position`, `status`, `has_megamenu`, `megamenu_type`, `created_at`, `updated_at`, `translate`) VALUES
(2, NULL, 'Products', 'Explore Our Amazing Products', '[\"header\"]', '#', 14, 2, 1, 1, 1, '2022-10-24 08:02:26', '2026-02-07 08:42:31', NULL),
(4, 14, 'Privacy', NULL, '[\"widget_two\"]', 'privacy-policy', 17, NULL, 1, 0, 1, '2022-10-24 08:05:24', '2025-12-17 11:35:52', NULL),
(11, 32, 'About', NULL, '\"Footer\"', 'about', NULL, NULL, 1, 0, 2, '2022-10-27 09:38:03', '2026-01-22 06:21:31', NULL),
(14, NULL, 'Home', NULL, '[\"header\", \"widget_one\"]', '/', NULL, 1, 1, 0, 1, '2022-10-28 02:34:49', '2025-02-04 04:06:02', NULL),
(16, 30, 'Blog', NULL, '[\"widget_one\", \"header\"]', 'blog', NULL, 4, 1, 0, 1, '2022-11-16 20:35:57', '2026-01-26 10:50:29', NULL),
(18, NULL, 'Account Login', NULL, '[\"widget_one\"]', '/auth/login', 16, NULL, 1, 0, 1, '2022-11-16 18:01:03', '2025-12-17 11:35:05', NULL),
(19, 13, 'Terms and Conditions', NULL, '[\"widget_two\"]', 'terms-conditions', 15, NULL, 1, 0, 1, '2022-11-16 18:03:30', '2025-12-17 11:34:47', NULL),
(32, 8, 'Agent', NULL, '\"Footer\"', 'agent', 18, NULL, 1, 0, 1, '2025-03-24 08:13:48', '2026-01-22 06:19:49', NULL),
(33, 7, 'Merchant', NULL, '\"Footer\"', 'merchant', 19, NULL, 1, 0, 1, '2025-03-24 08:14:11', '2026-01-22 06:19:52', NULL),
(34, NULL, 'API Documentation', NULL, '[\"widget_three\"]', '/api-documentation', 20, NULL, 1, 0, 1, '2025-03-24 08:17:22', '2025-03-24 08:17:22', NULL),
(35, NULL, 'Agent Portal', NULL, '[\"widget_one\"]', 'https://qunzo-agent.tdevs.co', 21, NULL, 1, 0, 1, '2025-03-25 08:27:57', '2025-12-18 04:34:37', NULL),
(36, NULL, 'Merchant Portal', NULL, '[\"widget_one\"]', 'https://qunzo-merchant.tdevs.co', 22, NULL, 1, 0, 1, '2025-03-25 08:28:16', '2025-12-18 04:34:54', NULL),
(37, 37, 'How It Works', NULL, '\"Footer\"', 'how-it-works', 23, NULL, 1, 0, 1, '2025-04-28 05:02:58', '2026-01-26 03:32:43', NULL),
(38, 31, 'Contact', NULL, '[\"widget_three\", \"widget_two\", \"header\"]', 'contact', 24, 5, 1, 0, 1, '2026-01-19 08:14:38', '2026-01-26 10:50:29', NULL),
(39, NULL, 'Services', 'Everyday Payment Services', '[\"header\"]', '#', 25, 3, 1, 1, 2, '2026-01-20 05:07:15', '2026-01-26 10:53:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `for` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notice` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `action_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` bigint UNSIGNED NOT NULL,
  `theme` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` enum('static','dynamic','service') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'en',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `theme`, `title`, `code`, `data`, `type`, `status`, `locale`, `url`, `created_at`, `updated_at`) VALUES
(13, 'default', 'Terms & Conditions', 'terms-conditions', '{\"section_id\":\"[]\",\"meta_keywords\":\"Terms and Conditions\",\"meta_description\":\"Terms and Conditions\",\"content\":\"<p>Welcome to <strong>Qunzo<\\/strong>. By accessing or using our platform, you agree to be bound by the following terms and conditions. Please read them carefully before using any Qunzo services.<\\/p>\\r\\n\\r\\n    <h4>1. Acceptance of Terms<\\/h4>\\r\\n    <p>By registering, accessing, or using Qunzo, you acknowledge that you have read, understood, and agree to comply with these Terms &amp; Conditions. If you do not agree, please discontinue using our services immediately.<\\/p>\\r\\n\\r\\n    <h4>2. Account Responsibilities<\\/h4>\\r\\n    <p>Each user is responsible for maintaining the confidentiality of their account credentials. Any actions taken under your account will be considered authorized by you. Please contact support immediately if you detect unauthorized activity.<\\/p>\\r\\n\\r\\n    <h4>3. Services Overview<\\/h4>\\r\\n    <p><strong>Qunzo<\\/strong> is a smart digital wallet and payment ecosystem designed to simplify financial management. Our key features include:<\\/p>\\r\\n    <ul>\\r\\n        <li>Multi-Wallet System (Main, Savings, Profit, etc.)<\\/li>\\r\\n        <li>Merchant Module for business payments<\\/li>\\r\\n        <li>Agent Module for cash-in and cash-out<\\/li>\\r\\n        <li>Gift Code and Invoice Payment Links<\\/li>\\r\\n        <li>Money Exchange between currencies<\\/li>\\r\\n        <li>Send &amp; Request Money features<\\/li>\\r\\n        <li>QR Code for all wallet operations<\\/li>\\r\\n        <li>Merchant API for developers<\\/li>\\r\\n    <\\/ul>\\r\\n\\r\\n    <h4>4. User Conduct<\\/h4>\\r\\n    <p>You agree not to use Qunzo for any unlawful or fraudulent activities, including but not limited to money laundering, scams, identity theft, or unauthorized access attempts.<\\/p>\\r\\n\\r\\n    <h4>5. Fees and Transactions<\\/h4>\\r\\n    <p>Applicable fees are displayed before confirming any transaction. You agree to review and accept all charges before proceeding. Qunzo reserves the right to modify fees with prior notice.<\\/p>\\r\\n\\r\\n    <h4>6. Refund Policy<\\/h4>\\r\\n    <p>All successful transactions are final. Refunds will only be issued in cases of technical errors or duplicate payments, verified by our system and support team.<\\/p>\\r\\n\\r\\n    <h4>7. Limitation of Liability<\\/h4>\\r\\n    <p>Qunzo is not liable for any indirect, incidental, or consequential damages resulting from service use, data loss, or technical failures, except as required by law.<\\/p>\\r\\n\\r\\n    <h4>8. Privacy Policy<\\/h4>\\r\\n    <p>We value your privacy. Your data is protected and processed in accordance with our <a href=\\\"\\/privacy-policy\\\">Privacy Policy<\\/a>. Qunzo ensures compliance with global data protection standards.<\\/p>\\r\\n\\r\\n    <h4>9. Modification of Terms<\\/h4>\\r\\n    <p>Qunzo reserves the right to update or modify these Terms at any time. Any significant changes will be communicated via email or platform notice. Continued use of Qunzo indicates acceptance of updates.<\\/p>\\r\\n\\r\\n    <h4>10. Suspension and Termination<\\/h4>\\r\\n    <p>Qunzo may suspend or terminate accounts that violate our Terms, engage in suspicious activity, or attempt to exploit system vulnerabilities.<\\/p>\\r\\n\\r\\n    <h4>11. Contact Information<\\/h4>\\r\\n    <p>For questions regarding these Terms &amp; Conditions, don\'t hesitate to get in touch with us at <a href=\\\"mailto:support@qunzo.com\\\">support@qunzo.com<\\/a>.<\\/p>\\r\\n\\r\\n    <p><em>Last Updated: November 1, 2025<\\/em><\\/p>\",\"title\":\"Terms and Conditions\"}', 'static', 1, 'en', 'terms-conditions', '2025-04-05 08:48:43', '2025-11-01 10:41:53'),
(14, 'default', 'Privacy & Policy', 'privacy-policy', '{\"meta_keywords\":\"Privacy & Policy\",\"meta_description\":\"Privacy & Policy\",\"section_id\":\"[]\",\"content\":\"<p>At <strong>Qunzo<\\/strong>, we value your privacy and are committed to protecting your personal data. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our platform and related services.<\\/p>\\r\\n\\r\\n    <h4>1. Information We Collect<\\/h4>\\r\\n    <p>We collect several types of information to provide and improve our services:<\\/p>\\r\\n    <ul>\\r\\n        <li><strong>Personal Information:<\\/strong> Name, email address, phone number, and verification documents (if required).<\\/li>\\r\\n        <li><strong>Financial Information:<\\/strong> Wallet details, transactions, and payment history.<\\/li>\\r\\n        <li><strong>Technical Data:<\\/strong> IP address, browser type, device information, and access timestamps.<\\/li>\\r\\n        <li><strong>Usage Data:<\\/strong> Interactions with our platform, preferences, and behavioral patterns.<\\/li>\\r\\n    <\\/ul>\\r\\n\\r\\n    <h4>2. How We Use Your Information<\\/h4>\\r\\n    <p>We use collected information to:<\\/p>\\r\\n    <ul>\\r\\n        <li>Provide, maintain, and improve Qunzo\\u2019s services.<\\/li>\\r\\n        <li>Process transactions securely and efficiently.<\\/li>\\r\\n        <li>Verify user identity and prevent fraudulent activities.<\\/li>\\r\\n        <li>Send important updates, alerts, and promotional messages (if permitted).<\\/li>\\r\\n        <li>Comply with legal and regulatory requirements.<\\/li>\\r\\n    <\\/ul>\\r\\n\\r\\n    <h4>3. Data Security<\\/h4>\\r\\n    <p>We implement industry-standard security measures, including encryption and access controls, to protect your personal information from unauthorized access, disclosure, or destruction. However, no online system is entirely risk-free, and users share data at their own discretion.<\\/p>\\r\\n\\r\\n    <h4>4. Cookies and Tracking<\\/h4>\\r\\n    <p>Qunzo uses cookies and similar technologies to enhance your browsing experience and analyze platform performance. You may adjust browser settings to reject cookies, but some features may not function properly.<\\/p>\\r\\n\\r\\n    <h4>5. Sharing of Information<\\/h4>\\r\\n    <p>We do not sell or rent user data. However, we may share information with:<\\/p>\\r\\n    <ul>\\r\\n        <li>Trusted service providers assisting in operations (e.g., payment gateways, analytics).<\\/li>\\r\\n        <li>Regulatory authorities, if legally required.<\\/li>\\r\\n        <li>Business partners with user consent or for integrated services.<\\/li>\\r\\n    <\\/ul>\\r\\n\\r\\n    <h4>6. Data Retention<\\/h4>\\r\\n    <p>Your data will be retained as long as necessary to provide services, comply with laws, or resolve disputes. Once no longer needed, we securely delete or anonymize the information.<\\/p>\\r\\n\\r\\n    <h4>7. Your Rights<\\/h4>\\r\\n    <p>You have the right to:<\\/p>\\r\\n    <ul>\\r\\n        <li>Access, review, or correct your personal data.<\\/li>\\r\\n        <li>Request deletion of your data (subject to legal retention requirements).<\\/li>\\r\\n        <li>Withdraw consent for promotional communications.<\\/li>\\r\\n        <li>File a complaint with a data protection authority if you believe your rights are violated.<\\/li>\\r\\n    <\\/ul>\\r\\n\\r\\n    <h4>8. Third-Party Links<\\/h4>\\r\\n    <p>Our platform may include links to third-party websites. Qunzo is not responsible for the privacy practices or content of external sites. We recommend reviewing their privacy policies before providing personal information.<\\/p>\\r\\n\\r\\n    <h4>9. Children\'s Privacy<\\/h4>\\r\\n    <p>Qunzo\\u2019s services are not directed toward individuals under 18 years old. We do not knowingly collect personal data from minors. If you believe we have done so inadvertently, please contact us for removal.<\\/p>\\r\\n\\r\\n    <h4>10. Policy Updates<\\/h4>\\r\\n    <p>We may revise this Privacy Policy periodically. Updates will be posted on this page with a new \\\"Last Updated\\\" date. Continued use of Qunzo after updates means you accept the revised policy.<\\/p>\\r\\n\\r\\n    <h4>11. Contact Us<\\/h4>\\r\\n    <p>If you have any questions or concerns regarding this Privacy Policy, don\'t hesitate to get in touch with us at:<\\/p>\\r\\n    <p><a href=\\\"mailto:support@qunzo.com\\\">support@qunzo.com<\\/a><\\/p>\\r\\n\\r\\n    <p><em>Last Updated: November 1, 2025<\\/em><\\/p>\",\"title\":\"Privacy & Policy\"}', 'static', 1, 'en', 'privacy-policy', '2025-04-05 09:02:29', '2025-11-01 10:42:52'),
(29, 'default', 'Api Documentation', 'api-documentation', '{\"title\":\"API Documentation\",\"meta_keywords\":\"API Documentation\",\"meta_description\":\"API Documentation\",\"section_id\":\"[]\"}', 'static', 1, 'en', 'api-documentation', '2025-04-05 09:02:29', '2025-11-01 08:51:00'),
(30, 'default', 'Blog ', 'blog', '{\"title\":\"All Blogs\",\"meta_keywords\":\"All Blogs\",\"meta_description\":\"All Blogs\",\"section_id\":\"[]\"}', 'static', 1, 'en', 'blog', '2025-01-13 11:10:19', '2025-12-09 09:49:23'),
(31, 'default', 'Contact', 'contact', '{\"title\":\"Contact Us\",\"phone_no\":\"+05555555555\",\"email\":\"contact@qunzo.com\",\"address\":\"2972 Westheimer Rd. Santa Ana, Illinois 85486\",\"meta_keywords\":\"contact us, email\",\"meta_description\":\"Contact\",\"phone_icon\":\"global\\/uploads\\/images\\/ct86uRaB6LjvH9O3ZwuS.svg\",\"email_icon\":\"global\\/uploads\\/images\\/iRMVI9m4woOAUNfoJhkB.svg\",\"address_icon\":\"global\\/uploads\\/images\\/l5ej7KAwWNA0jyhFKmjF.svg\",\"section_id\":\"[]\",\"phone_no_label\":\"Email Address\",\"email_label\":\"Email Address\",\"address_label\":\"Address\",\"form_right_image\":\"global\\/uploads\\/images\\/y6LyUrtki0rCNAd8MYSw.webp\",\"form_title\":\"Contact Information\",\"form_subtitle\":\"Say something to start a conversation!\",\"form_button_text\":\"Send Message\"}', 'static', 1, 'en', 'contact', '2025-01-13 11:10:19', '2025-12-17 04:04:43'),
(32, 'default', 'About', 'about', '{\"title\":\"About Us\",\"content\":\"<p>\\r\\n        We are a modern digital payment platform built to simplify how individuals and businesses\\r\\n        manage money in a fast-moving, borderless world. Our mission is to make financial\\r\\n        transactions seamless, secure, and accessible for everyone.\\r\\n      <\\/p>\\r\\n\\r\\n      <p>\\r\\n        From instant transfers and multi-wallet support to merchant and agent solutions,\\r\\n        our platform is designed with flexibility and performance at its core.\\r\\n        We combine cutting-edge technology with a user-first approach to deliver\\r\\n        reliable financial services you can trust.\\r\\n      <\\/p>\\r\\n\\r\\n      <p>\\r\\n        Whether you are sending money, managing your business payments, or exploring\\r\\n        new digital opportunities, we are committed to empowering your financial journey\\r\\n        with innovation, transparency, and security.\\r\\n      <\\/p>\\r\\n\\r\\n      <ul>\\r\\n        <li>\\u2714 Secure and scalable payment infrastructure<\\/li>\\r\\n        <li>\\u2714 User, merchant, and agent-friendly solutions<\\/li>\\r\\n        <li>\\u2714 Fast, reliable, and borderless transactions<\\/li>\\r\\n        <li>\\u2714 Built for modern digital economies<\\/li>\\r\\n      <\\/ul>\",\"meta_keywords\":\"About Us\",\"meta_description\":\"About Us\",\"section_id\":\"[\\\"27\\\"]\",\"about_title\":\"Who We Are\",\"button_label\":\"Start Your Journey\",\"button_url\":\"\\/login\",\"button_target\":\"_self\",\"right_image\":\"global\\/uploads\\/images\\/N2ltnepgPhrDWzQlv1hE.webp\",\"mission_title\":\"Our Mission\",\"mission_content\":\"At Qunzo, our mission is to simplify global payments through innovation, transparency, and security \\u2014 empowering individuals and businesses to transact freely without borders or hidden barriers.\",\"vision_title\":\"Our Vision\",\"vision_content\":\"We envision a world where financial freedom is accessible to everyone \\u2014 powered by digital currencies, smart technology, and a trusted ecosystem built for growth.\"}', 'static', 1, 'en', 'about', '2025-01-13 11:10:19', '2025-12-15 12:15:24'),
(33, 'default', 'Merchant', 'merchant', '{\"title\":\"Merchant\",\"section_title\":\"Power Your Business with Qunzo\",\"section_subtitle\":\"Instant payments, real-time tracking, and seamless customer checkout.\",\"button_label\":\"Join as Merchant\",\"button_url\":\"https:\\/\\/qunzo-merchant.tdevs.co\\/auth\\/login\",\"button_target\":\"_self\",\"content\":\"<h2>Become a Qunzo Merchant<\\/h2>\\r\\n  <p>\\r\\n    Grow your business with Qunzo by accepting fast, secure, and cashless\\r\\n    payments. As a Qunzo merchant, you can receive payments instantly, manage\\r\\n    transactions easily, and provide a smooth checkout experience for your\\r\\n    customers.\\r\\n  <\\/p>\\r\\n\\r\\n  <h3>What Can You Do as a Merchant?<\\/h3>\\r\\n  <ul>\\r\\n    <li><strong>Accept Digital Payments:<\\/strong> Receive payments via QR code, wallet balance, or digital transfer.<\\/li>\\r\\n    <li><strong>Instant Settlement:<\\/strong> Get paid instantly into your Qunzo merchant wallet.<\\/li>\\r\\n    <li><strong>Transaction History:<\\/strong> Track all sales and payments in real time.<\\/li>\\r\\n    <li><strong>Customer Payments:<\\/strong> Accept secure payments from Qunzo users anytime.<\\/li>\\r\\n  <\\/ul>\\r\\n\\r\\n  <h3>Why Choose Qunzo for Your Business?<\\/h3>\\r\\n  <ul>\\r\\n    <li><strong>Faster Payments:<\\/strong> No waiting, no delays \\u2014 instant confirmation.<\\/li>\\r\\n    <li><strong>Lower Costs:<\\/strong> Reduce cash handling and operational expenses.<\\/li>\\r\\n    <li><strong>Secure Transactions:<\\/strong> Protected by advanced security and fraud prevention.<\\/li>\\r\\n    <li><strong>Merchant Dashboard:<\\/strong> Manage payments, balance, and reports from one place.<\\/li>\\r\\n  <\\/ul>\\r\\n\\r\\n  <h3>How It Works<\\/h3>\\r\\n  <ol>\\r\\n    <li>Register for a merchant account<\\/li>\\r\\n    <li>Complete verification and approval<\\/li>\\r\\n    <li>Start accepting digital payments from customers<\\/li>\\r\\n  <\\/ol>\\r\\n\\r\\n  <h3>Who Can Become a Merchant?<\\/h3>\\r\\n  <p>\\r\\n    Retail stores, restaurants, online shops, service providers, and small\\r\\n    businesses can join Qunzo and go fully digital.\\r\\n  <\\/p>\\r\\n\\r\\n  <p>\\r\\n    <strong>Upgrade your business with Qunzo and accept payments smarter.<\\/strong>\\r\\n  <\\/p>\",\"section_id\":\"[]\",\"app_title\":\"Download Merchant App\",\"app_subtitle\":\"Trusted by thousands of merchants for fast, safe, and reliable digital payments.\",\"app_store_link\":\"#\",\"play_store_link\":\"#\",\"app_image\":\"global\\/uploads\\/images\\/9xFXbdBkGsf0JWHyjWi8.webp\",\"app_store_image\":\"global\\/uploads\\/images\\/CcnH88n5ec3hfw75E94R.svg\",\"play_store_image\":\"global\\/uploads\\/images\\/0Mn2BThNdWYl7oE6NV44.svg\"}', 'static', 1, 'en', 'merchant', '2025-01-13 11:10:19', '2026-01-20 10:53:50'),
(34, 'default', 'Agent', 'agent', '{\"title\":\"Agent\",\"section_title\":\"Become a Qunzo Agent\",\"button_label\":\"Join as Agent\",\"button_url\":\"https:\\/\\/qunzo-agent.tdevs.co\\/auth\\/login\",\"button_target\":\"_self\",\"content\":\"<h2>Become a Qunzo Agent<\\/h2>\\r\\n  <p>\\r\\n    Join Qunzo\\u2019s trusted agent network and earn by providing essential digital\\r\\n    financial services in your community. As an agent, you help customers\\r\\n    deposit, withdraw, and manage their money securely while growing your income.\\r\\n  <\\/p>\\r\\n\\r\\n  <h3>What Can You Do as an Agent?<\\/h3>\\r\\n  <ul>\\r\\n    <li><strong>Cash In &amp; Cash Out:<\\/strong> Help customers deposit or withdraw funds instantly through their Qunzo wallet.<\\/li>\\r\\n    <li><strong>Wallet Transfers:<\\/strong> Assist users with fast and secure wallet-to-wallet transfers.<\\/li>\\r\\n    <li><strong>Bill Payments:<\\/strong> Enable customers to pay utility bills, mobile recharges, and service fees.<\\/li>\\r\\n    <li><strong>Merchant Support:<\\/strong> Help local merchants accept digital payments and manage transactions.<\\/li>\\r\\n  <\\/ul>\\r\\n\\r\\n  <h3>Why Become a Qunzo Agent?<\\/h3>\\r\\n  <ul>\\r\\n    <li><strong>Earn Commission:<\\/strong> Get paid on every successful transaction.<\\/li>\\r\\n    <li><strong>Flexible Income:<\\/strong> Work on your own schedule and increase earnings.<\\/li>\\r\\n    <li><strong>Secure System:<\\/strong> All transactions are protected with advanced security.<\\/li>\\r\\n    <li><strong>Agent Dashboard:<\\/strong> Track balance, earnings, and transactions easily.<\\/li>\\r\\n  <\\/ul>\\r\\n\\r\\n  <h3>How It Works<\\/h3>\\r\\n  <ol>\\r\\n    <li>Apply for an agent account<\\/li>\\r\\n    <li>Complete verification and approval<\\/li>\\r\\n    <li>Start serving customers and earning commissions<\\/li>\\r\\n  <\\/ol>\\r\\n\\r\\n  <h3>Who Can Become an Agent?<\\/h3>\\r\\n  <p>\\r\\n    Shop owners, entrepreneurs, mobile recharge points, and small business\\r\\n    owners can become Qunzo agents and offer trusted financial services.\\r\\n  <\\/p>\\r\\n\\r\\n  <p>\\r\\n    <strong>Start your agent journey today and grow with Qunzo.<\\/strong>\\r\\n  <\\/p>\",\"section_id\":\"[]\",\"widget_title\":\"Start Earning as an Agent Today!\",\"widget_button_label\":\"Login As Agent\",\"widget_button_url\":\"\\/agent\\/login\",\"widget_button_target\":\"_self\",\"section_subtitle\":\"Join Qunzo\\u2019s trusted agent network and earn by providing essential digital financial services in your community. As an agent, you help customers deposit, withdraw, and manage their money securely \\u2014 while growing your own income.\",\"app_title\":\"Download Agent App\",\"app_subtitle\":\"Serve customers, process payments, and earn commissions anytime with our powerful Agent App.\",\"app_store_link\":\"#\",\"play_store_link\":\"#\",\"app_image\":\"global\\/uploads\\/images\\/BkLAS7H1mAy71DEzGKtX.webp\",\"app_store_image\":\"global\\/uploads\\/images\\/yg4vUtjRkUXs6s1caQPV.svg\",\"play_store_image\":\"global\\/uploads\\/images\\/YUEHVOz6BYV8yLDZNn1C.svg\"}', 'static', 1, 'en', 'agent', '2025-01-13 11:10:19', '2026-01-20 09:31:11'),
(37, 'default', 'How It Works', 'how-it-works', '{\"meta_keywords\":\"How It Works\",\"meta_description\":\"How It Works\",\"section_id\":\"[\\\"35\\\"]\",\"content\":\"\",\"title\":\"How It Works\"}', 'static', 1, 'en', 'how-it-works', '2025-04-27 10:13:11', '2025-12-15 06:09:36'),
(59, 'default', 'Virtual Cards', 'virtual-cards', '{\"title\":\"Virtual Cards\",\"meta_keywords\":\"Virtual Cards\",\"meta_description\":\"Virtual Cards\",\"section_id\":\"[]\",\"content\":\"\",\"hero_title\":\"Virtual Card for Secure  [[color_text= Online Payments ]]\",\"hero_description\":\"Instantly generate virtual cards for fast, safe, and cashless transactions worldwide.\",\"hero_button_label\":\"Create Your Virtual Card\",\"hero_button_url\":\"#\",\"hero_button_target\":\"_self\",\"howitworks_title\":\"How It Works\",\"confidence_title\":\"Pay Online with Confidence, Anytime\",\"confidence_description\":\"Protect your money with virtual cards designed for fast, safe, and hassle-free digital transactions worldwide. Generate instant virtual cards for shopping, subscriptions, and international payments \\u2014 all from one secure platform. Create and manage virtual cards to pay online securely, control spending, and make global payments without borders.\",\"confidence_button_label\":\"Start Your Journey\",\"confidence_button_url\":\"#\",\"confidence_button_target\":\"_self\",\"features_title\":\"Key Features\",\"faq_title\":\"Frequently Asked  [[color_text= Questions  ]]\",\"faq_subtitle\":\"Find quick answers to common questions about our secure crypto wallet. Can\'t find what you\'re looking for?\\u00a0Contact our Support Team.\",\"hero_image\":\"global\\/uploads\\/images\\/DjqhqYqo4kYouyvXLo5i.svg\",\"confidence_image\":\"global\\/uploads\\/images\\/gV55oNg3jwq9PO2dl95o.svg\",\"faq_image\":\"global\\/uploads\\/images\\/EBO0XIpFkDiRNc1mQinT.svg\"}', 'static', 1, 'en', 'virtual-cards', '2025-04-27 10:13:11', '2026-01-21 05:45:39'),
(60, 'default', 'Mobile Recharge', 'mobile-recharge', '{\"title\":\"Mobile Recharge\",\"meta_keywords\":\"Mobile Recharge\",\"meta_description\":\"Mobile Recharge\",\"section_id\":\"[]\",\"hero_title\":\"Recharge Your Mobile in Seconds\",\"hero_description\":\"Top up any prepaid or postpaid number instantly with secure payments and real-time confirmation.\",\"hero_button_label\":\"Top Up Now\",\"hero_button_url\":\"#\",\"hero_button_target\":\"_self\",\"features_title\":\"Key Features\",\"hero_image\":\"global\\/uploads\\/images\\/Sv7YCCCwHl0aqmf9r9DV.svg\",\"powering_title\":\"Powering Instant Mobile Recharge\",\"powering_description\":\"<p><b>Top up your mobile in seconds with a secure, reliable, and easy-to-use platform built for everyday convenience.\\r\\n<\\/b><\\/p><p>- Fast payments\\r\\n<br>- Real-time processing\\r\\n<br>-Transparent pricing<\\/p>\",\"powering_button_label\":\"Recharge Now\",\"powering_button_url\":\"#\",\"powering_button_target\":\"_self\",\"howitworks_title\":\"How It Works\",\"faq_title\":\"Frequently Asked Questions\",\"faq_subtitle\":\"Find quick answers to common questions about our secure crypto wallet. Can\'t find what you\'re looking for?\\u00a0Contact our Support Team.\",\"powering_image\":\"global\\/uploads\\/images\\/le61q8Oh6lpOWpwrykIf.svg\",\"faq_image\":\"global\\/uploads\\/images\\/m2YipDpWlWndAgyyYcvI.svg\"}', 'static', 1, 'en', 'mobile-recharge', '2025-04-27 10:13:11', '2026-01-22 04:32:31'),
(61, 'default', 'Bill Payment', 'bill-payment', '{\"title\":\"Bill Payment\",\"meta_keywords\":\"Bill Payment\",\"meta_description\":\"Bill Payment\",\"hero_title\":\"All Your Bills, One Secure Place\",\"hero_description\":\"Pay utility and service bills securely in seconds with instant confirmation.\",\"hero_button_label\":\"Pay Now\",\"hero_button_url\":\"#\",\"hero_button_target\":\"_self\",\"bill_categories_title\":\"Bill Categories\",\"about_title\":\"About Our Bill Payment Service\",\"about_description\":\"<p>Our Bill Payment service lets you pay utility and service bills easily from one platform. No lines, no delays\\u2014just fast, secure payments with instant confirmation.<br><\\/p>\",\"about_button_label\":\"Pay Your Bill\",\"about_button_url\":\"#\",\"about_button_target\":\"_self\",\"features_title\":\"Key Features\",\"faq_title\":\"Frequently Asked Questions\",\"faq_subtitle\":\"Find quick answers to common questions about our secure crypto wallet. Can\'t find what you\'re looking for?\\u00a0Contact our Support Team.\",\"hero_image\":\"global\\/uploads\\/images\\/RolwdDAIngZk9lQ4LtIB.png\",\"about_image\":\"global\\/uploads\\/images\\/3SNg4ihsfVwT0cBJwUaw.png\",\"faq_image\":\"global\\/uploads\\/images\\/QHvGERbOOEvZ2b9ZUzuP.svg\",\"section_id\":\"[]\"}', 'static', 1, 'en', 'bill-payment', '2025-04-27 10:13:11', '2026-01-22 09:22:35'),
(62, 'default', 'P2P Trading', 'p2p-trading', '{\"title\":\"P2P Trading\",\"meta_keywords\":\"P2P Trading\",\"meta_description\":\"P2P Trading\",\"hero_title\":\"Trade Money Directly with Trusted Users\",\"hero_description\":\"Exchange money directly with verified users using secure escrow and instant settlement.\",\"hero_button_label\":\"Start Trading\",\"hero_button_url\":\"#\",\"hero_button_target\":\"_self\",\"about_title\":\"About P2P Exchange\",\"about_description\":\"<p><span>P2P (peer-to-peer) transfers let you send and receive money instantly between users. Just choose a recipient, enter an amount, and confirm securely\\u2014no banks, no delays.<\\/span><\\/p>\",\"about_button_label\":\"Transfer\",\"about_button_url\":\"#\",\"about_button_target\":\"_self\",\"features_title\":\"Powerful Features. Safer Trading.\",\"features_button_label\":\"Start Trading\",\"features_button_url\":\"#\",\"features_button_target\":\"_self\",\"howitworks_title\":\"How P2P Trading Works\",\"faq_title\":\"Frequently Asked Questions\",\"faq_subtitle\":\"Find quick answers to common questions about our secure crypto wallet. Can\'t find what you\'re looking for?\\u00a0Contact our Support Team.\",\"hero_image\":\"global\\/uploads\\/images\\/gpIGpOiern3erG0ONGWu.png\",\"faq_image\":\"global\\/uploads\\/images\\/YnXgeqJR9eEiKHeQnj0e.svg\",\"section_id\":\"[]\",\"about_image\":\"global\\/uploads\\/images\\/13LuqThNmyZeQasN4hxi.png\"}', 'static', 1, 'en', 'p2p-trading', '2025-04-27 10:13:11', '2026-01-24 05:28:27'),
(63, 'default', 'Wallets', 'wallets', '{\"meta_keywords\":\"Wallets\",\"meta_description\":\"Wallets\",\"hero_title\":\"Secure. Fast. Smart Wallet.\",\"hero_description\":\"A secure digital wallet designed for fast payments and smart money management.\",\"hero_button_text\":\"Create Your Wallet\",\"hero_button_link\":\"#\",\"hero_button_target\":\"_self\",\"highlight_title\":\"Built for Modern Payments\",\"highlight_description\":\"We provide a powerful digital wallet that lets you send, receive, and manage your money with confidence. Our platform is designed for speed, security, and everyday convenience.\",\"highlight_button_text\":\"Get Started\",\"highlight_button_link\":\"#\",\"highlight_button_target\":\"_self\",\"features_title\":\"Smart Features for Smarter Payments\",\"steps_title\":\"Get Started in 3 Simple Steps\",\"faq_title\":\"Frequently Asked Questions\",\"faq_subtitle\":\"Find quick answers to common questions about our secure crypto wallet. Can\'t find what you\'re looking for?\\u00a0Contact our Support Team.\",\"faq_image\":\"global\\/uploads\\/images\\/etEJppSyiHeZLRht90gy.png\",\"hero_image\":\"global\\/uploads\\/images\\/3ZVXndEAE42IeVUXuoA9.png\",\"highlight_image\":\"global\\/uploads\\/images\\/oFW8pIgOaibVY2MiPmgF.png\"}', 'service', 1, 'en', 'wallets', '2026-01-24 10:59:50', '2026-01-26 04:21:37'),
(64, 'default', 'Add Money', 'add-money', '{\"meta_keywords\":\"Add Money\",\"meta_description\":\"Add Money\",\"hero_title\":\"Add Money Instantly to Your Wallet\",\"hero_description\":\"Fund your account anytime using bank transfers, cards, mobile banking, or crypto \\u2014 fast, safe, and hassle-free.\",\"hero_button_text\":\"Add Funds Now\",\"hero_button_link\":\"#\",\"hero_button_target\":\"_self\",\"highlight_title\":\"Why Add Money With Us?\",\"highlight_description\":\"Easily top up your wallet in seconds. Choose from multiple secure payment options and start using your balance for trading, payments, and withdrawals instantly.\",\"highlight_button_text\":\"Get Started\",\"highlight_button_link\":\"#\",\"highlight_button_target\":\"_self\",\"features_title\":\"What We Offer\",\"steps_title\":\"Get Started in 3 Simple Steps\",\"faq_title\":\"Frequently Asked Questions\",\"faq_subtitle\":\"Find quick answers to common questions about our secure crypto wallet. Can\'t find what you\'re looking for?\\u00a0Contact our Support Team.\",\"faq_image\":\"global\\/uploads\\/images\\/wAxblCGE1RKd3LsoL9Uw.png\",\"hero_image\":\"global\\/uploads\\/images\\/M3ixYU5OvMgGKG7EWMaY.png\",\"highlight_image\":\"global\\/uploads\\/images\\/T4IJRChGm6R7qQ3TK5CS.png\"}', 'service', 1, 'en', 'add-money', '2026-01-25 04:21:03', '2026-01-26 04:21:47'),
(65, 'default', 'Make Payment', 'make-payment', '{\"meta_keywords\":\"Make Payment\",\"meta_description\":\"Make Payment\",\"hero_title\":\"Make Payments in Seconds\",\"hero_description\":\"Pay merchants, services, and users instantly using your wallet balance \\u2014 secure, fast, and effortless.\",\"hero_button_text\":\"Pay Now\",\"hero_button_link\":\"#\",\"hero_button_target\":\"_self\",\"highlight_title\":\"Powering Smart Digital Payments\",\"highlight_description\":\"We are a next-generation digital wallet and payment platform designed to make money movement simple, fast, and secure. From adding funds to making payments, trading, and withdrawals \\u2014 everything works in one powerful system.\",\"highlight_button_text\":\"Get Started\",\"highlight_button_link\":\"#\",\"highlight_button_target\":\"_self\",\"features_title\":\"Smart Features for Smarter Payments\",\"steps_title\":\"Get Started in 3 Simple Steps\",\"faq_title\":\"Frequently Asked Questions\",\"faq_subtitle\":\"Find quick answers to common questions about our secure crypto wallet. Can\'t find what you\'re looking for?\\u00a0Contact our Support Team.\",\"faq_image\":\"global\\/uploads\\/images\\/bfNwdqZY2eLERNDLks3s.png\",\"hero_image\":\"global\\/uploads\\/images\\/Utldns7pt2LzABwkykeY.png\",\"highlight_image\":\"global\\/uploads\\/images\\/D8NRI5QL7G7FNAWS5slA.png\"}', 'service', 1, 'en', 'make-payment', '2026-01-25 04:36:14', '2026-01-26 04:21:55'),
(66, 'default', 'Request Money', 'request-money', '{\"meta_keywords\":\"Request Money\",\"meta_description\":\"Request Money\",\"hero_title\":\"Request Money Easily & Securely\",\"hero_description\":\"Send payment requests and get paid instantly\\u2014no cash, no hassle.\",\"hero_button_text\":\"Request Money Now\",\"hero_button_link\":\"#\",\"hero_button_target\":\"_self\",\"highlight_title\":\"Built for Request Money\",\"highlight_description\":\"We make requesting money fast, transparent, and secure. Whether you\\u2019re splitting bills or collecting payments, Qunzo helps you get paid with confidence.\",\"highlight_button_text\":\"Get Started\",\"highlight_button_link\":\"#\",\"highlight_button_target\":\"_self\",\"features_title\":\"What We Offer\",\"steps_title\":\"Get Started in 3 Simple Steps\",\"faq_title\":\"Frequently Asked Questions\",\"faq_subtitle\":\"Find quick answers to common questions about our secure crypto wallet. Can\'t find what you\'re looking for?\\u00a0Contact our Support Team.\",\"faq_image\":\"global\\/uploads\\/images\\/n6NFXpBrGYfqBILazFMc.png\",\"hero_image\":\"global\\/uploads\\/images\\/oQkvcT2DP6PcoeAAwgUL.png\",\"highlight_image\":\"global\\/uploads\\/images\\/XkULfdktKvWT7LwHj2BS.png\"}', 'service', 1, 'en', 'request-money', '2026-01-25 04:47:51', '2026-01-26 04:22:02'),
(67, 'default', 'Transfer', 'transfer', '{\"meta_keywords\":\"Transfer\",\"meta_description\":\"Transfer\",\"hero_title\":\"Transfer Money Instantly & Securely\",\"hero_description\":\"Transfer funds instantly to friends, family, merchants, or agents using the Qunzo wallet.\",\"hero_button_text\":\"Transfer Money\",\"hero_button_link\":\"#\",\"hero_button_target\":\"_self\",\"highlight_title\":\"Built for Fast & Reliable Transfers\",\"highlight_description\":\"Qunzo makes money transfers effortless and secure. Whether it\\u2019s personal payments or business transactions, your money reaches the recipient instantly.\",\"highlight_button_text\":\"Get Started\",\"highlight_button_link\":\"#\",\"highlight_button_target\":\"_self\",\"features_title\":\"What We Offer\",\"steps_title\":\"Get Started in 3 Simple Steps\",\"faq_title\":\"Frequently Asked Questions\",\"faq_subtitle\":\"Find quick answers to common questions about our secure crypto wallet. Can\'t find what you\'re looking for?\\u00a0Contact our Support Team.\",\"faq_image\":\"global\\/uploads\\/images\\/1vgKkDCHV818fQoWHzkA.png\",\"hero_image\":\"global\\/uploads\\/images\\/OZ9BhaGZMztuNzDa0QQ3.png\",\"highlight_image\":\"global\\/uploads\\/images\\/xLjyT4TaaJTkQsk5OiFN.png\"}', 'service', 1, 'en', 'transfer', '2026-01-25 04:55:10', '2026-01-26 04:22:10'),
(68, 'default', 'Cash out', 'cash-out', '{\"meta_keywords\":\"Cash out\",\"meta_description\":\"Cash out\",\"hero_title\":\"Cash Out Your Money Anytime\",\"hero_description\":\"Easily cash out your wallet balance to agents, banks, or supported withdrawal methods.\",\"hero_button_text\":\"Cash Out Now\",\"hero_button_link\":\"#\",\"hero_button_target\":\"_self\",\"highlight_title\":\"Fast, Secure & Reliable Cash Outs\",\"highlight_description\":\"Access your money whenever you need it. With Qunzo, cashing out is simple, secure, and designed for everyday convenience.\",\"highlight_button_text\":\"Get Started\",\"highlight_button_link\":\"#\",\"highlight_button_target\":\"_self\",\"features_title\":\"What We Offer\",\"steps_title\":\"Cash Out in 3 Simple Steps\",\"faq_title\":\"Frequently Asked Questions\",\"faq_subtitle\":\"Find quick answers to common questions about our secure crypto wallet. Can\'t find what you\'re looking for?\\u00a0Contact our Support Team.\",\"faq_image\":\"global\\/uploads\\/images\\/QWjGHdeisp7FVYJj6wRW.png\",\"hero_image\":\"global\\/uploads\\/images\\/4JMaPSzSoY7peU8XASuh.png\",\"highlight_image\":\"global\\/uploads\\/images\\/2X6kkWy3bIIs573tFeTy.png\"}', 'service', 1, 'en', 'cash-out', '2026-01-25 05:21:25', '2026-01-26 04:22:20'),
(69, 'default', 'Withdraw', 'withdraw', '{\"meta_keywords\":\"Withdraw\",\"meta_description\":\"Withdraw\",\"hero_title\":\"Withdraw Funds to Your Bank Securely\",\"hero_description\":\"Withdraw money from your Qunzo wallet to your linked bank account quickly and securely.\",\"hero_button_text\":\"Withdraw Now\",\"hero_button_link\":\"#\",\"hero_button_target\":\"_self\",\"highlight_title\":\"Move your wallet balance directly\",\"highlight_description\":\"Withdraw money from your Qunzo wallet to your linked bank account quickly and securely. Enjoy transparent fees, reliable processing, and full control over your funds.\",\"highlight_button_text\":\"Get Started\",\"highlight_button_link\":\"#\",\"highlight_button_target\":\"_self\",\"features_title\":\"What We Offer\",\"steps_title\":\"Get Started in 3 Simple Steps\",\"faq_title\":\"Frequently Asked Questions\",\"faq_subtitle\":\"Find quick answers to common questions about our secure crypto wallet. Can\'t find what you\'re looking for?\\u00a0Contact our Support Team.\",\"faq_image\":\"global\\/uploads\\/images\\/DfOTFRQd5fIEzx7yP0PJ.png\",\"hero_image\":\"global\\/uploads\\/images\\/NLtbNHMhcKlacVwK1cQC.png\",\"highlight_image\":\"global\\/uploads\\/images\\/EaMC5mHzXMj0YrK8zMnv.png\"}', 'service', 1, 'en', 'withdraw', '2026-01-25 05:43:00', '2026-01-26 04:22:27'),
(70, 'default', 'Exchange', 'exchange', '{\"meta_keywords\":\"Exchange\",\"meta_description\":\"Exchange\",\"hero_title\":\"Exchange Currency Instantly & Securely\",\"hero_description\":\"Exchange money directly within your Qunzo wallet using real-time rates.\",\"hero_button_text\":\"Exchange Now\",\"hero_button_link\":\"#\",\"hero_button_target\":\"_self\",\"highlight_title\":\"Smart & Reliable Currency Exchange\",\"highlight_description\":\"Qunzo allows you to exchange currencies seamlessly inside your wallet\\u2014no external services, no delays, and complete transparency.\",\"highlight_button_text\":\"Get Started\",\"highlight_button_link\":\"#\",\"highlight_button_target\":\"_self\",\"features_title\":\"What We Offer\",\"steps_title\":\"Get Started in 3 Simple Steps\",\"faq_title\":\"Frequently Asked Questions\",\"faq_subtitle\":\"Find quick answers to common questions about our secure crypto wallet. Can\'t find what you\'re looking for?\\u00a0Contact our Support Team.\",\"faq_image\":\"global\\/uploads\\/images\\/HQB3Tns0WHDMU9TRY2ZE.png\",\"hero_image\":\"global\\/uploads\\/images\\/Krb3oSxLDuKldvwvhhFI.png\",\"highlight_image\":\"global\\/uploads\\/images\\/4ss1Wu5lA4vlaTTBGZV3.png\"}', 'service', 1, 'en', 'exchange', '2026-01-25 05:49:54', '2026-01-26 04:22:36'),
(71, 'default', 'Payment Links', 'payment-links', '{\"meta_keywords\":\"Payment Links\",\"meta_description\":\"Payment Links\",\"hero_title\":\"Create & Pay with Payment Links \\u2014 Seamlessly\",\"hero_description\":\"Create digital payment links and accept secure payments directly into your wallet.\",\"hero_button_text\":\"Create Payment Link\",\"hero_button_link\":\"#\",\"hero_button_target\":\"_self\",\"highlight_title\":\"Smart Links for Payments\",\"highlight_description\":\"Qunzo helps you create professional payment link sand receive funds securely. Track invoice status and manage payments from one place.\",\"highlight_button_text\":\"Get Started\",\"highlight_button_link\":\"#\",\"highlight_button_target\":\"_self\",\"features_title\":\"What We Offer\",\"steps_title\":\"Get Started in 3 Simple Steps\",\"faq_title\":\"Frequently Asked Questions\",\"faq_subtitle\":\"Find quick answers to common questions about our secure crypto wallet. Can\'t find what you\'re looking for?\\u00a0Contact our Support Team.\",\"faq_image\":\"global\\/uploads\\/images\\/qjP6N5z7ghhlNRv4pFiX.png\",\"hero_image\":\"global\\/uploads\\/images\\/QGPqoeY9bzCR5UaBqxG1.png\",\"highlight_image\":\"global\\/uploads\\/images\\/rptscvC0wonrWM6vQNc1.png\"}', 'service', 1, 'en', 'payment-links', '2026-01-25 06:05:44', '2026-01-26 04:22:43'),
(72, 'default', 'Gift Code', 'gift-code', '{\"meta_keywords\":\"Gift Code\",\"meta_description\":\"Gift Code\",\"hero_title\":\"Send Gift with Gift Codes\",\"hero_description\":\"Create gift codes and send money to anyone instantly.\",\"hero_button_text\":\"Create Gift Code\",\"hero_button_link\":\"#\",\"hero_button_target\":\"_self\",\"highlight_title\":\"Simple, Secure & Flexible Gifting\",\"highlight_description\":\"Qunzo gift codes let you send value without sharing wallet details. Generate, share, and track gift codes securely in just a few steps.\",\"highlight_button_text\":\"Get Started\",\"highlight_button_link\":\"#\",\"highlight_button_target\":\"_self\",\"features_title\":\"What We Offer\",\"steps_title\":\"Get Started in 3 Simple Steps\",\"faq_title\":\"Frequently Asked Questions\",\"faq_subtitle\":\"Find quick answers to common questions about our secure crypto wallet. Can\'t find what you\'re looking for?\\u00a0Contact our Support Team.\",\"faq_image\":\"global\\/uploads\\/images\\/40oGPd3dLLJkvVZb0osh.png\",\"hero_image\":\"global\\/uploads\\/images\\/iMat07TshkB7NIFUdzTd.png\",\"highlight_image\":\"global\\/uploads\\/images\\/ztNPDyHHXI8ddwytpLYr.png\"}', 'service', 1, 'en', 'gift-code', '2026-01-25 06:20:11', '2026-01-26 04:22:52'),
(96, 'default', 'Gift Cards', 'gift-cards', '{\"meta_keywords\":\"Gift Cards\",\"meta_description\":\"Gift Cards\",\"title\":\"Gift Cards\",\"hero_title\":\"Virtual Gift Cards \\u2014 Instant, Global & Secure\",\"hero_description\":\"Buy digital gift cards for top brands worldwide. Delivered instantly, no physical cards, no hassle.\",\"hero_button_label\":\"Buy Gift Card Now\",\"hero_button_url\":\"#\",\"hero_button_target\":\"_self\",\"highlight_title\":\"Why Choose Our Gift Cards\",\"highlight_description\":\"<p><span>Instant digital gift cards that are easy to send, safe to use, and perfect for any occasion.<\\/span><\\/p>\\r\\n\\r\\n<ul>\\r\\n    <li>Instant Delivery<\\/li>\\r\\n    <li>Perfect for Any Occasion<\\/li>\\r\\n    <li>Secure &amp; Easy Checkout<\\/li>\\r\\n    <li>Redeem via App or Web<\\/li>\\r\\n<\\/ul>\",\"highlight_button_text\":\"Get Started\",\"highlight_button_link\":\"#\",\"highlight_button_target\":\"_self\",\"howitworks_title\":\"Gift Card How It Works\",\"faq_title\":\"Frequently Asked Questions\",\"faq_subtitle\":\"Find quick answers to common questions about our secure crypto wallet. Can\'t find what you\'re looking for?\\u00a0Contact our Support Team.\",\"hero_image\":\"global\\/uploads\\/images\\/qeqdKCzUwQ8UXuEqxdzY.png\",\"faq_image\":\"global\\/uploads\\/images\\/K5FJchu5a3GVSjVyJ1qI.png\",\"section_id\":\"[]\",\"highlight_image\":\"global\\/uploads\\/images\\/k7dmHVaCFEVjK3Rq5veG.png\"}', 'static', 1, 'en', 'gift-cards', '2026-01-25 06:20:11', '2026-02-07 08:56:33');

-- --------------------------------------------------------

--
-- Table structure for table `page_settings`
--

CREATE TABLE `page_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `page_settings`
--

INSERT INTO `page_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'username_show', '1', '2025-04-06 04:43:02', '2025-04-06 04:43:02'),
(2, 'username_validation', '1', '2025-04-06 04:43:02', '2025-04-30 09:21:29'),
(3, 'phone_show', '1', '2025-04-06 04:43:02', '2025-04-06 04:43:02'),
(4, 'phone_validation', '1', '2025-04-06 04:43:02', '2025-04-06 05:04:11'),
(5, 'country_show', '1', '2025-04-06 04:43:02', '2025-04-09 10:52:39'),
(6, 'country_validation', '1', '2025-04-06 04:43:02', '2025-04-06 05:04:11'),
(7, 'referral_code_show', '1', '2025-04-06 04:43:02', '2025-04-06 04:43:02'),
(8, 'referral_code_validation', '0', '2025-04-06 04:43:02', '2025-04-06 04:43:02'),
(9, 'gender_show', '1', '2025-04-06 04:43:02', '2025-04-06 04:43:02'),
(10, 'gender_validation', '0', '2025-04-06 04:43:02', '2025-04-06 04:59:25'),
(11, 'merchant_username_show', '0', '2025-04-10 03:44:59', '2025-04-10 04:12:26'),
(12, 'merchant_username_validation', '0', '2025-04-10 03:44:59', '2025-04-10 03:44:59'),
(13, 'merchant_phone_show', '1', '2025-04-10 03:44:59', '2025-04-10 03:44:59'),
(14, 'merchant_phone_validation', '0', '2025-04-10 03:44:59', '2025-04-10 03:44:59'),
(15, 'merchant_country_show', '1', '2025-04-10 03:44:59', '2025-04-10 03:44:59'),
(16, 'merchant_country_validation', '0', '2025-04-10 03:44:59', '2025-04-10 03:44:59'),
(17, 'merchant_gender_show', '1', '2025-04-10 03:44:59', '2025-04-10 03:53:40'),
(18, 'merchant_gender_validation', '0', '2025-04-10 03:44:59', '2025-04-10 03:44:59'),
(19, 'agent_username_show', '1', '2025-04-10 04:53:43', '2025-04-10 04:53:43'),
(20, 'agent_username_validation', '0', '2025-04-10 04:53:43', '2025-04-10 04:53:43'),
(21, 'agent_phone_show', '1', '2025-04-10 04:53:43', '2025-04-10 04:53:43'),
(22, 'agent_phone_validation', '0', '2025-04-10 04:53:43', '2025-04-10 04:53:43'),
(23, 'agent_country_show', '1', '2025-04-10 04:53:43', '2025-04-10 04:53:43'),
(24, 'agent_country_validation', '0', '2025-04-10 04:53:43', '2025-04-10 04:53:43'),
(25, 'agent_gender_show', '1', '2025-04-10 04:53:43', '2025-04-10 04:53:43'),
(26, 'agent_gender_validation', '0', '2025-04-10 04:53:43', '2025-04-10 04:53:43');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `category`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Statistics Management', 'total-users', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(2, 'Statistics Management', 'total-agents', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(3, 'Statistics Management', 'total-merchants', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(4, 'Statistics Management', 'all-deposits', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(5, 'Statistics Management', 'all-currencies', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(6, 'Statistics Management', 'total-staff', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(7, 'Statistics Management', 'total-withdraw', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(8, 'Statistics Management', 'total-referral', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(9, 'Statistics Management', 'total-automatic-gateway', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(10, 'Statistics Management', 'total-ticket', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(11, 'Statistics Management', 'total-transfer', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(12, 'Statistics Management', 'total-cashout', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(13, 'Statistics Management', 'total-payments', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(14, 'Statistics Management', 'site-statistics-chart', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(15, 'Statistics Management', 'top-country-statistics', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(16, 'Statistics Management', 'top-browser-statistics', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(17, 'Statistics Management', 'top-os-statistics', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(18, 'Statistics Management', 'latest-users', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(19, 'Statistics Management', 'latest-merchants', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(20, 'Statistics Management', 'latest-agents', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(21, 'Customer Management', 'customer-list', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(22, 'Customer Management', 'customer-mail-send', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(23, 'Customer Management', 'customer-basic-manage', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(24, 'Customer Management', 'customer-balance-add-or-subtract', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(25, 'Customer Management', 'customer-change-password', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(26, 'Customer Management', 'all-type-status', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(27, 'Merchant Management', 'merchant-list', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(28, 'Merchant Management', 'merchant-mail-send', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(29, 'Merchant Management', 'merchant-basic-manage', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(30, 'Merchant Management', 'merchant-balance-add-or-subtract', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(31, 'Merchant Management', 'merchant-change-password', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(32, 'Merchant Management', 'merchant-all-type-status', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(33, 'Merchant Management', 'merchant-delete', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(34, 'Merchant Management', 'merchant-kyc-info', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(35, 'Agent Management', 'agent-list', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(36, 'Agent Management', 'agent-mail-send', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(37, 'Agent Management', 'agent-basic-manage', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(38, 'Agent Management', 'agent-balance-add-or-subtract', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(39, 'Agent Management', 'agent-change-password', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(40, 'Agent Management', 'agent-all-type-status', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(41, 'Agent Management', 'agent-delete', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(42, 'Agent Management', 'agent-kyc-info', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(43, 'Verification Center', 'verification-list', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(44, 'Verification Center', 'verification-action', 'admin', '2025-12-24 02:24:12', '2025-12-24 02:24:12'),
(45, 'Verification Center', 'verification-form-manage', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(46, 'Role Management', 'role-list', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(47, 'Role Management', 'role-create', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(48, 'Role Management', 'role-edit', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(49, 'Staff Management', 'staff-list', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(50, 'Staff Management', 'staff-create', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(51, 'Staff Management', 'staff-edit', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(52, 'Transaction Management', 'transaction-list', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(53, 'Transaction Management', 'admin-profits', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(57, 'Deposit Management', 'automatic-gateway-manage', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(58, 'Deposit Management', 'manual-gateway-manage', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(59, 'Deposit Management', 'deposit-list', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(60, 'Deposit Management', 'deposit-action', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(61, 'Withdraw Management', 'withdraw-list', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(62, 'Withdraw Management', 'withdraw-method-manage', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(63, 'Withdraw Management', 'withdraw-action', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(64, 'Withdraw Management', 'withdraw-schedule', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(65, 'Referral Management', 'manage-referral', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(66, 'Referral Management', 'referral-create', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(67, 'Referral Management', 'referral-edit', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(68, 'Referral Management', 'referral-delete', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(69, 'Appearance Management', 'custom-css', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(70, 'Bill Management', 'bill-service-import', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(71, 'Bill Management', 'bill-service-list', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(72, 'Bill Management', 'bill-service-edit', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(73, 'Bill Management', 'bill-convert-rate', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(74, 'Bill Management', 'all-bills', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(75, 'Bill Management', 'pending-bills', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(76, 'Bill Management', 'complete-bills', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(77, 'Bill Management', 'return-bills', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(78, 'Support Ticket Management', 'support-ticket-list', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(79, 'Support Ticket Management', 'support-ticket-action', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(80, 'Setting Management', 'site-setting', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(81, 'Setting Management', 'email-setting', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(82, 'Setting Management', 'plugin-setting', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(83, 'Setting Management', 'currencies-setting', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(84, 'Setting Management', 'language-setting', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(85, 'Setting Management', 'page-setting', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(86, 'Setting Management', 'notification-tune-setting', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(87, 'Template Management', 'template-list', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(88, 'Template Management', 'template-edit', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(89, 'Template Management', 'template-update', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(90, 'Template Management', 'template-delete', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(91, 'System Management', 'manage-cron-job', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(92, 'System Management', 'cron-job-create', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(93, 'System Management', 'cron-job-edit', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(94, 'System Management', 'cron-job-delete', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(95, 'System Management', 'cron-job-logs', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(96, 'System Management', 'cron-job-run', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(97, 'System Management', 'clear-cache', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13'),
(98, 'System Management', 'application-details', 'admin', '2025-12-24 02:24:13', '2025-12-24 02:24:13');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plugins`
--

CREATE TABLE `plugins` (
  `id` bigint UNSIGNED NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'system',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(196) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plugins`
--

INSERT INTO `plugins` (`id`, `icon`, `type`, `name`, `description`, `data`, `status`, `created_at`, `updated_at`) VALUES
(5, 'global/plugin/vonage.png', 'sms', 'Vonage', 'Vonage API (formerly Nexmo) provides cloud-based SMS API for seamless communication integration.\n\n', '{\"from\":\"8801887094529\",\"api_key\":\"d67bcc94\",\"api_secret\":\"Gu5QVwrCZRSORjOs\"}', 0, NULL, '2023-12-26 00:56:45'),
(6, 'global/plugin/twilio.png', 'sms', 'Twillo', 'Build agility into your customer engagement\r\n\r\n\r\n', '{\"twilio_sid\":\"AC9620e699606601c99d920d795d053d83\",\"twilio_auth_token\":\"7b97976578e99b93d1dd1a0f2fbf0849\",\"twilio_phone\":\"+19292426081\"}', 0, NULL, '2023-12-31 04:26:09'),
(13, 'global/plugin/firebase.png', 'notification', 'Firebase', 'Real Time Notifications For Flutter App', '{\"upload_account_json\":\"global\\/files\\/serviceAccount.json\"}', 1, NULL, '2025-11-06 09:03:59'),
(14, 'global/plugin/pusher.png', 'notification', 'Pusher', 'Pusher is used for real-time notification for the admin.', '{\"pusher_app_id\":\"2082097\",\"pusher_app_key\":\"ba4b4aa844f1b8140ad9\",\"pusher_app_secret\":\"0adc5d9eab8dfd44bd4e\",\"pusher_app_cluster\":\"ap2\"}', 1, NULL, '2025-11-24 11:33:28'),
(15, 'global/plugin/flutterwave.png', 'billing_service_provider', 'Flutterwave', 'Bill Payment APIs for Utility Payments.', '{\"secret_key\":\"FLWSECK_TEST-7784016e1b57eabd718cb287e484aa67-X\"}', 1, '2023-12-19 06:45:26', '2025-12-23 11:37:49');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Super-Admin', 'admin', '2024-11-19 22:45:34', '2024-11-19 22:45:34');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sandbox_transactions`
--

CREATE TABLE `sandbox_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `tnx` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(28,8) DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `charge` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `final_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pay_currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `callback_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `manual_field_data` json DEFAULT NULL,
  `approval_cause` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `val` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'string',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `val`, `type`, `created_at`, `updated_at`) VALUES
(1, 'site_logo', 'global/uploads/global/uploads/settings//mjxazf4dfek3ckRSFjOl.svg', 'string', '2024-11-19 22:45:36', '2025-11-01 11:28:41'),
(2, 'site_logo_height', '35', 'string', '2024-11-19 22:45:36', '2025-11-01 11:18:38'),
(3, 'site_logo_width', 'auto', 'string', '2024-11-19 22:45:36', '2025-11-01 11:18:47'),
(4, 'site_favicon', 'global/uploads/global/uploads/settings//1xOFnDDUyWS2LSBOp0pG.svg', 'string', '2024-11-19 22:45:36', '2025-11-01 10:48:04'),
(5, 'login_bg', 'global/uploads/global/uploads/settings//UBl7eVJdIqLwnwGrwg07.jpg', 'string', '2024-11-19 22:45:36', '2025-11-01 11:44:25'),
(6, 'site_admin_prefix', 'admin', 'string', '2024-11-19 22:45:36', '2025-04-16 11:58:11'),
(7, 'site_title', 'Qunzo', 'string', '2024-11-19 22:45:36', '2025-11-01 11:03:12'),
(8, 'account_number_prefix', 'DGB', 'string', '2024-11-19 22:45:36', '2024-11-21 04:58:29'),
(9, 'site_currency', 'USD', 'string', '2024-11-19 22:45:36', '2024-11-21 04:58:29'),
(10, 'currency_symbol', '$', 'string', '2024-11-19 22:45:36', '2024-11-21 04:58:29'),
(11, 'site_timezone', 'Asia/Dhaka', 'string', '2024-11-19 22:45:36', '2025-08-30 00:13:47'),
(12, 'referral_code_limit', '6', 'integer', '2024-11-19 22:45:36', '2024-11-21 04:58:29'),
(13, 'account_no_limit', '10', 'integer', '2024-11-19 22:45:36', '2024-11-21 04:58:29'),
(14, 'home_redirect', '/', 'string', '2024-11-19 22:45:36', '2025-04-10 06:42:59'),
(15, 'site_email', 'admin@qunzo.com', 'string', '2024-11-19 22:45:36', '2025-11-01 10:48:04'),
(16, 'support_email', 'support@qunzo.com', 'string', '2024-11-19 22:45:36', '2025-11-01 10:48:04'),
(17, 'referral_rules_visibility', '1', 'boolean', '2024-11-19 22:45:36', '2025-03-06 06:51:36'),
(18, 'deposit_level', '1', 'checkbox', '2024-11-19 22:45:36', '2024-11-19 22:45:36'),
(19, 'multiple_currency', '1', 'boolean', '2024-11-19 22:45:36', '2025-04-17 05:24:33'),
(20, 'transfer_status', '1', 'boolean', '2024-11-19 22:45:36', '2024-11-25 03:24:02'),
(21, 'email_verification', '1', 'boolean', '2024-11-19 22:45:36', '2025-09-01 05:47:31'),
(22, 'kyc_verification', '1', 'boolean', '2024-11-19 22:45:36', '2025-04-12 11:43:53'),
(23, 'fa_verification', '1', 'boolean', '2024-11-19 22:45:36', '2025-04-06 10:10:46'),
(24, 'otp_verification', '1', 'boolean', '2024-11-19 22:45:36', '2024-11-25 03:24:02'),
(25, 'account_creation', '1', 'boolean', '2024-11-19 22:45:36', '2024-11-25 03:24:02'),
(26, 'user_deposit', '1', 'boolean', '2024-11-19 22:45:36', '2025-04-16 06:03:46'),
(27, 'user_portfolio', '1', 'checkbox', '2024-11-19 22:45:36', '2024-11-19 22:45:36'),
(28, 'user_withdraw', '1', 'boolean', '2024-11-19 22:45:36', '2024-11-25 03:24:02'),
(29, 'user_pay_bill', '1', 'boolean', '2024-11-19 22:45:36', '2025-12-23 10:35:45'),
(30, 'sign_up_referral', '1', 'boolean', '2024-11-19 22:45:36', '2024-11-25 03:24:02'),
(31, 'referral_signup_bonus', '1', 'boolean', '2024-11-19 22:45:36', '2024-11-25 03:24:02'),
(32, 'site_animation', '0', 'boolean', '2024-11-19 22:45:36', '2025-04-28 06:03:29'),
(33, 'back_to_top', '1', 'boolean', '2024-11-19 22:45:36', '2025-04-12 05:06:27'),
(34, 'language_switcher', '1', 'boolean', '2024-11-19 22:45:36', '2025-11-02 11:27:17'),
(35, 'default_mode', 'light', 'string', '2024-11-19 22:45:36', '2025-04-26 10:28:49'),
(36, 'debug_mode', '0', 'boolean', '2024-11-19 22:45:36', '2025-05-03 04:42:13'),
(37, 'session_lifetime', '120', 'string', '2024-11-19 22:45:36', '2025-04-24 05:02:00'),
(38, 'referral_bonus', '20', 'double', '2024-11-19 22:45:36', '2024-11-21 05:56:54'),
(39, 'signup_bonus', '20', 'double', '2024-11-19 22:45:36', '2024-11-21 05:56:54'),
(40, 'transfer_min_amount', '10', 'double', '2024-11-19 22:45:36', '2024-11-21 05:56:54'),
(41, 'transfer_max_amount', '20000', 'double', '2024-11-19 22:45:36', '2024-11-21 05:56:54'),
(42, 'fund_transfer_charge', '4', 'double', '2024-11-19 22:45:36', '2024-11-21 05:56:54'),
(43, 'fund_transfer_charge_type', 'percentage', 'double', '2024-11-19 22:45:36', '2024-11-21 05:56:54'),
(44, 'withdraw_day_limit', '20', 'double', '2024-11-19 22:45:36', '2024-11-21 05:56:54'),
(45, 'kyc_deposit', '1', 'boolean', '2024-11-19 22:45:36', '2024-11-25 03:17:07'),
(46, 'kyc_fund_transfer', '1', 'boolean', '2024-11-19 22:45:36', '2024-11-25 03:21:54'),
(47, 'kyc_dps', '0', 'boolean', '2024-11-19 22:45:36', '2024-11-25 03:17:07'),
(48, 'kyc_fdr', '0', 'boolean', '2024-11-19 22:45:36', '2024-11-25 03:17:07'),
(49, 'kyc_loan', '0', 'boolean', '2024-11-19 22:45:36', '2024-11-25 03:17:07'),
(50, 'kyc_pay_bill', '1', 'boolean', '2024-11-19 22:45:36', '2024-12-01 02:45:56'),
(51, 'kyc_withdraw', '1', 'boolean', '2024-11-19 22:45:36', '2025-04-24 05:02:32'),
(52, 'deposit_passcode_status', '1', 'boolean', '2024-11-19 22:45:36', '2025-12-10 09:35:33'),
(53, 'fund_transfer_passcode_status', '0', 'checkbox', '2024-11-19 22:45:36', '2024-11-19 22:45:36'),
(54, 'dps_passcode_status', '0', 'checkbox', '2024-11-19 22:45:36', '2024-11-19 22:45:36'),
(55, 'fdr_passcode_status', '0', 'checkbox', '2024-11-19 22:45:36', '2024-11-19 22:45:36'),
(56, 'loan_passcode_status', '0', 'checkbox', '2024-11-19 22:45:36', '2024-11-19 22:45:36'),
(57, 'pay_bill_passcode_status', '0', 'checkbox', '2024-11-19 22:45:36', '2024-11-19 22:45:36'),
(58, 'withdraw_passcode_status', '1', 'boolean', '2024-11-19 22:45:36', '2025-12-10 09:35:33'),
(59, 'inactive_account_disabled', '0', 'string', '2024-11-19 22:45:36', '2025-04-24 05:02:08'),
(60, 'inactive_days', '30', 'string', '2024-11-19 22:45:36', '2025-04-24 05:01:16'),
(61, 'inactive_account_fees', '1', 'switch', '2024-11-19 22:45:36', '2024-11-19 22:45:36'),
(62, 'fee_amount', '5', 'double', '2024-11-19 22:45:36', '2024-11-19 22:45:36'),
(63, 'email_from_name', 'Qunzo', 'string', '2024-11-19 22:45:36', '2025-11-06 10:57:41'),
(64, 'email_from_address', 'qunzo@tdevs.co', 'string', '2024-11-19 22:45:36', '2025-11-06 10:57:41'),
(65, 'mailing_driver', 'smtp', 'string', '2024-11-19 22:45:36', '2025-03-04 05:54:45'),
(66, 'mail_username', '6df8bc29b11979', 'string', '2024-11-19 22:45:36', '2025-09-01 08:27:26'),
(67, 'mail_password', 'c2e0a83868b48f', 'string', '2024-11-19 22:45:36', '2025-09-01 08:27:26'),
(68, 'mail_host', 'sandbox.smtp.mailtrap.io', 'string', '2024-11-19 22:45:36', '2025-03-04 05:54:45'),
(69, 'mail_port', '587', 'integer', '2024-11-19 22:45:36', '2025-04-05 04:50:51'),
(70, 'mail_secure', 'tls', 'string', '2024-11-19 22:45:36', '2025-03-04 05:54:45'),
(71, 'suggested_regular_license_price_from', '10', 'checkbox', '2024-11-19 22:45:36', '2024-11-19 22:45:36'),
(72, 'suggested_regular_license_price_to', '15', 'checkbox', '2024-11-19 22:45:36', '2024-11-19 22:45:36'),
(73, 'suggested_extended_license_price_from', '20', 'checkbox', '2024-11-19 22:45:36', '2024-11-19 22:45:36'),
(74, 'suggested_extended_license_price_to', '50', 'checkbox', '2024-11-19 22:45:36', '2024-11-19 22:45:36'),
(75, 'maintenance_mode', '0', 'boolean', '2024-11-19 22:45:36', '2025-04-30 06:10:04'),
(76, 'secret_key', 'secret', 'string', '2024-11-19 22:45:36', '2025-04-15 11:20:34'),
(77, 'maintenance_title', 'Site is under maintenance', 'string', '2024-11-19 22:45:36', '2025-11-01 11:28:56'),
(78, 'maintenance_text', 'Sorry for interrupt! Site will live soon.', 'string', '2024-11-19 22:45:36', '2025-04-15 11:20:34'),
(79, 'gdpr_status', '1', 'boolean', '2024-11-19 22:45:36', '2024-11-21 02:33:49'),
(80, 'gdpr_text', 'Please allow us to collect data about how you use our website. We will use it to improve our website, make your browsing experience and our business decisions better.', 'string', '2024-11-19 22:45:36', '2025-03-27 03:50:12'),
(81, 'gdpr_button_label', 'Learn More', 'string', '2024-11-19 22:45:36', '2024-11-21 02:33:49'),
(82, 'gdpr_button_url', '/page/privacy-policy', 'string', '2024-11-19 22:45:36', '2025-04-24 05:16:21'),
(83, 'meta_description', 'Qunzo is an all-in-one financial solution with agent and merchant facilities.', 'string', '2024-11-19 22:45:36', '2025-12-17 10:57:38'),
(84, 'meta_keywords', 'qunzo ,money exchange, add money, bill payment', 'string', '2024-11-19 22:45:36', '2025-12-17 10:57:38'),
(85, 'affiliate_commission_charge', '4', 'text', '2024-11-19 22:45:36', '2024-11-19 22:45:36'),
(86, 'affiliate_commission_charge_type', 'percentage', 'text', '2024-11-19 22:45:36', '2024-11-19 22:45:36'),
(87, 'exchange_charge', '2', 'double', '2024-11-27 02:49:27', '2025-02-27 12:03:06'),
(88, 'exchange_charge_type', 'percentage', 'double', '2024-11-27 02:49:27', '2025-02-27 12:03:06'),
(89, 'cashout_charge', '5', 'double', '2025-02-20 03:37:57', '2025-02-27 09:14:04'),
(90, 'cashout_minimum', '100', 'double', '2025-02-20 03:37:58', '2025-02-27 09:12:02'),
(91, 'cashout_maximum', '10000', 'double', '2025-02-20 03:37:58', '2025-02-20 03:37:58'),
(92, 'cashout_daily_limit', '1000', 'double', '2025-02-20 03:37:58', '2025-02-27 10:02:29'),
(93, 'cashout_monthly_limit', '10000', 'double', '2025-02-20 03:37:58', '2025-02-27 09:44:40'),
(94, 'cashout_charge_type', 'percentage', 'double', '2025-02-20 03:40:15', '2025-02-27 09:14:54'),
(95, 'gift_charge', '10', 'double', '2025-02-22 04:20:22', '2025-02-22 04:20:51'),
(96, 'gift_charge_type', 'fixed', 'double', '2025-02-22 04:20:22', '2025-02-26 08:41:30'),
(97, 'gift_minimum', '100', 'double', '2025-02-22 04:20:22', '2025-02-22 04:20:22'),
(98, 'gift_maximum', '1000', 'double', '2025-02-22 04:20:22', '2025-02-22 04:20:39'),
(99, 'gift_daily_limit', '10', 'double', '2025-02-22 04:20:22', '2025-02-22 08:56:39'),
(100, 'invoice_charge', '2', 'double', '2025-02-22 08:29:54', '2025-02-22 08:45:24'),
(101, 'invoice_charge_type', 'fixed', 'double', '2025-02-22 08:29:54', '2025-02-22 08:45:19'),
(102, 'invoice_daily_limit', '10', 'double', '2025-02-22 08:29:54', '2025-02-22 08:30:13'),
(103, 'exchange_daily_limit', '20', 'double', '2025-02-22 08:45:12', '2025-02-22 08:45:12'),
(104, 'cashout_agent_commission', '2', 'double', '2025-02-22 09:16:37', '2025-02-22 09:16:46'),
(105, 'cashout_agent_commission_type', 'percentage', 'double', '2025-02-22 09:16:37', '2025-02-22 09:16:40'),
(106, 'api_payment_charge', '5', 'double', '2025-02-23 04:37:37', '2025-02-23 04:37:37'),
(107, 'api_payment_charge_type', 'fixed', 'double', '2025-02-23 04:37:37', '2025-02-23 04:37:37'),
(108, 'user_make_payment_charge', '5', 'double', '2025-02-24 09:11:25', '2025-02-24 09:11:25'),
(109, 'user_make_payment_charge_type', 'percentage', 'double', '2025-02-24 09:11:25', '2025-02-24 09:37:26'),
(110, 'merchant_make_payment_charge', '2', 'double', '2025-02-24 09:11:25', '2025-02-24 10:05:50'),
(111, 'merchant_make_payment_charge_type', 'fixed', 'double', '2025-02-24 09:11:25', '2025-02-24 10:05:50'),
(112, 'request_money_charge', '5', 'double', '2025-02-26 04:53:12', '2025-02-26 04:53:12'),
(113, 'request_money_charge_type', 'percentage', 'double', '2025-02-26 04:53:12', '2025-02-26 04:53:12'),
(114, 'request_money_daily_limit', '10', 'double', '2025-02-26 04:53:12', '2025-02-26 04:53:12'),
(115, 'transfer_charge', '5', 'double', '2025-02-27 04:49:05', '2025-02-27 04:49:05'),
(116, 'transfer_charge_type', 'percentage', 'double', '2025-02-27 04:49:06', '2025-02-27 04:49:06'),
(117, 'transfer_minimum', '10', 'double', '2025-02-27 04:49:06', '2025-02-27 04:49:06'),
(118, 'transfer_maximum', '1000', 'double', '2025-02-27 04:49:06', '2025-02-27 04:49:06'),
(119, 'transfer_daily_limit', '10000', 'double', '2025-02-27 04:49:06', '2025-02-27 04:49:09'),
(120, 'site_currency_decimals', '2', 'string', '2025-03-06 03:06:55', '2025-03-06 03:08:10'),
(121, 'deposit', '1', 'boolean', '2025-03-06 06:09:34', '2025-03-06 06:59:30'),
(122, 'transfer', '1', 'boolean', '2025-03-06 06:11:00', '2025-03-06 06:41:06'),
(123, 'cash_out', '1', 'boolean', '2025-03-06 06:11:02', '2025-03-06 06:11:02'),
(124, 'invoice_pay', '1', 'boolean', '2025-03-06 06:11:04', '2025-03-06 06:58:52'),
(125, 'exchange', '1', 'boolean', '2025-03-06 06:11:05', '2025-03-06 06:58:54'),
(126, 'create_gift', '1', 'boolean', '2025-03-06 06:11:06', '2025-03-06 06:58:56'),
(127, 'request_money_accept', '1', 'boolean', '2025-03-06 06:11:08', '2025-03-06 06:58:51'),
(128, 'payment', '1', 'boolean', '2025-03-06 06:11:11', '2025-03-06 06:58:50'),
(129, 'referral_rules', '[{\"icon\":\"tick\",\"rule\":\"Referrer gets a reward when the referred user makes a successful first deposit.\"},{\"icon\":\"cross\",\"rule\":\"No reward if the referred user fails KYC or cancels the deposit.\"}]', 'string', '2025-03-06 06:44:52', '2025-04-15 11:45:50'),
(130, 'preloader', '0', 'boolean', '2025-04-06 05:33:36', '2025-04-28 06:51:20'),
(131, 'kyc_cashout', '1', 'boolean', '2025-04-10 06:08:59', '2025-04-10 06:08:59'),
(132, 'kyc_exchange', '1', 'boolean', '2025-04-10 06:08:59', '2025-04-10 06:08:59'),
(133, 'kyc_payment', '1', 'boolean', '2025-04-10 06:08:59', '2025-04-10 06:08:59'),
(134, 'kyc_request_money', '1', 'boolean', '2025-04-10 06:08:59', '2025-04-10 06:08:59'),
(135, 'kyc_create_gift', '1', 'boolean', '2025-04-10 06:08:59', '2025-04-10 06:08:59'),
(136, 'kyc_invoice', '1', 'boolean', '2025-04-10 06:08:59', '2025-04-10 06:08:59'),
(137, 'kyc_gift', '1', 'boolean', '2025-04-10 06:09:33', '2025-04-10 06:09:33'),
(138, 'user_transfer', '1', 'boolean', '2025-04-10 06:14:17', '2025-04-10 06:14:17'),
(139, 'user_cashout', '1', 'boolean', '2025-04-10 06:14:17', '2025-04-10 06:14:17'),
(140, 'user_gift', '1', 'boolean', '2025-04-10 06:14:17', '2025-04-10 06:14:17'),
(141, 'user_payment', '1', 'boolean', '2025-04-10 06:14:17', '2025-04-10 06:14:17'),
(142, 'user_invoice', '1', 'boolean', '2025-04-10 06:14:17', '2025-04-17 05:06:15'),
(143, 'user_request_money', '1', 'boolean', '2025-04-10 06:14:17', '2025-04-10 06:16:42'),
(144, 'user_exchange', '1', 'boolean', '2025-04-10 06:14:17', '2025-04-10 06:19:18'),
(145, 'user_ticket', '1', 'boolean', '2025-04-10 06:14:17', '2025-04-10 06:14:17'),
(146, 'kyc_wallet', '1', 'boolean', '2025-04-10 06:30:30', '2025-04-10 09:55:02'),
(147, 'merchant_system', '1', 'boolean', '2025-04-12 05:48:35', '2025-04-17 03:48:00'),
(148, 'agent_system', '1', 'boolean', '2025-04-12 06:20:27', '2025-04-12 10:55:13'),
(149, 'preloader_logo', 'global/uploads/global/uploads/settings//nBpFAKYuAn9dDT2Meoh9.svg', 'string', '2025-04-15 10:40:25', '2025-11-01 10:48:04'),
(150, 'merchant_verification', '1', 'boolean', '2025-04-16 05:57:19', '2025-09-17 05:18:59'),
(151, 'agent_verification', '1', 'boolean', '2025-04-16 05:57:19', '2025-09-16 05:45:04'),
(152, 'cashin_charge', '1', 'double', '2025-04-17 06:12:12', '2025-04-17 06:12:12'),
(153, 'cashin_charge_type', 'percentage', 'double', '2025-04-17 06:12:12', '2025-04-17 06:12:12'),
(154, 'cashin_agent_commission', '2', 'double', '2025-04-17 06:12:12', '2025-04-17 06:12:12'),
(155, 'cashin_agent_commission_type', 'percentage', 'double', '2025-04-17 06:12:12', '2025-04-17 06:12:12'),
(156, 'cashin_minimum', '10', 'double', '2025-04-17 06:12:12', '2025-04-17 06:12:12'),
(157, 'cashin_maximum', '10000', 'double', '2025-04-17 06:12:12', '2025-04-17 06:12:12'),
(158, 'cashin_daily_limit', '1000', 'double', '2025-04-17 06:12:12', '2025-04-17 06:52:54'),
(159, 'cashin_monthly_limit', '1000', 'double', '2025-04-17 06:12:12', '2025-04-17 06:12:12'),
(160, 'payment_minimum', '1', 'double', '2025-04-27 04:05:03', '2025-04-27 04:05:07'),
(161, 'payment_maximum', '1000', 'double', '2025-04-27 04:05:03', '2025-04-27 04:05:11'),
(162, 'exchange_minimum', '10', 'double', '2025-04-27 05:50:34', '2025-04-27 05:50:34'),
(163, 'exchange_maximum', '100000', 'double', '2025-04-27 05:50:34', '2025-04-27 05:50:34'),
(164, 'request_money_minimum', '10', 'double', '2025-04-27 06:16:47', '2025-04-27 06:16:47'),
(165, 'request_money_maximum', '10000', 'double', '2025-04-27 06:16:47', '2025-04-27 06:16:47'),
(166, 'admin_sidebar_logo', 'global/uploads/global/uploads/settings//C78NswUojjscovn6b9qJ.svg', 'string', '2025-11-01 11:28:41', '2025-11-01 11:28:41'),
(167, 'transfer_money_passcode_status', '1', 'boolean', '2025-12-10 09:35:33', '2025-12-10 09:35:33'),
(168, 'make_payment_passcode_status', '1', 'boolean', '2025-12-10 09:35:33', '2025-12-10 09:35:33'),
(169, 'gift_passcode_status', '0', 'boolean', '2025-12-10 09:35:33', '2025-12-10 09:35:33'),
(170, 'cashout_passcode_status', '1', 'boolean', '2025-12-10 09:35:33', '2025-12-10 09:35:33'),
(171, 'request_money_accept_passcode_status', '0', 'boolean', '2025-12-10 09:35:33', '2025-12-10 09:35:33'),
(172, 'invoice_passcode_status', '1', 'boolean', '2025-12-10 09:35:33', '2025-12-10 09:35:33'),
(173, 'exchange_passcode_status', '1', 'boolean', '2025-12-10 09:35:33', '2025-12-10 09:35:33'),
(174, 'virtual_card', '1', 'boolean', '2025-12-23 10:35:45', '2025-12-23 10:35:45'),
(175, 'card_creation', '1', 'boolean', '2025-12-23 10:35:45', '2025-12-23 10:35:45'),
(176, 'passcode_verification', '1', 'boolean', '2025-12-23 10:35:45', '2025-12-23 10:35:45'),
(177, 'card_creation_charge', '2', 'string', '2025-12-23 10:35:53', '2025-12-23 10:35:53'),
(178, 'card_topup_charge', '2', 'string', '2025-12-23 10:35:53', '2025-12-23 10:35:53'),
(179, 'card_topup_charge_type', 'percentage', 'double', '2025-12-23 10:35:53', '2025-12-23 10:35:53'),
(180, 'min_card_topup', '10', 'double', '2025-12-23 10:35:53', '2025-12-23 10:35:53'),
(181, 'max_card_topup', '100', 'double', '2025-12-23 10:35:53', '2025-12-23 10:35:53'),
(182, 'card_creation_limit', '4', 'double', '2025-12-23 10:35:53', '2025-12-23 10:35:53');

-- --------------------------------------------------------

--
-- Table structure for table `set_tunes`
--

CREATE TABLE `set_tunes` (
  `id` bigint UNSIGNED NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tune` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `set_tunes`
--

INSERT INTO `set_tunes` (`id`, `icon`, `name`, `tune`, `status`, `created_at`, `updated_at`) VALUES
(1, 'global/tune-icon/bewitched.png', 'Bewitched', 'global/tune/bewitched.mp3', 1, NULL, '2023-05-26 11:37:38'),
(2, 'global/tune-icon/crunchy.png', 'Crunchy', 'global/tune/crunchy.mp3', 0, NULL, '2023-05-26 11:37:38'),
(3, 'global/tune-icon/expert_notification.png', 'Expert Notification', 'global/tune/expert_notification.mp3', 0, NULL, '2023-05-26 11:37:38'),
(4, 'global/tune-icon/knock_knock.png', 'knock knock', 'global/tune/knock_knock.mp3', 0, NULL, '2023-05-26 11:37:38'),
(5, 'global/tune-icon/silencer.png', 'Silencer', 'global/tune/silencer.mp3', 0, NULL, '2023-05-26 11:37:38'),
(6, 'global/tune-icon/sticky.png', 'Sticky', 'global/tune/sticky.mp3', 0, NULL, '2023-05-26 11:37:38'),
(7, 'global/tune-icon/vopvoopvooop.png', 'Vopvoopvooop', 'global/tune/vopvoopvooop.mp3', 0, NULL, '2023-05-26 11:37:38');

-- --------------------------------------------------------

--
-- Table structure for table `socials`
--

CREATE TABLE `socials` (
  `id` bigint UNSIGNED NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `socials`
--

INSERT INTO `socials` (`id`, `icon`, `url`, `position`, `created_at`, `updated_at`) VALUES
(2, 'global/uploads/socials/r4skxXdewAUhW6hJzzNI.svg', 'https://facebook.com', 2, '2025-03-24 05:47:16', '2025-03-24 05:47:16'),
(3, 'global/uploads/socials/GEt05hrQEZ6WzUIgjxvh.svg', 'https://x.com', 3, '2025-03-24 05:47:37', '2025-03-24 05:47:37'),
(4, 'global/uploads/socials/ibMKH4FAACALghe3aPuW.svg', 'https://youtube.com', 4, '2025-03-24 05:47:54', '2025-03-24 05:47:54');

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `id` bigint UNSIGNED NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE `templates` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `for` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'User',
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notification_status` tinyint(1) NOT NULL DEFAULT '1',
  `email_status` tinyint(1) NOT NULL DEFAULT '1',
  `sms_status` tinyint(1) NOT NULL DEFAULT '1',
  `sms_body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `email_body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notification_body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `short_codes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `banner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salutation` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `button_level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `footer_status` tinyint(1) NOT NULL DEFAULT '1',
  `footer_body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`id`, `name`, `code`, `for`, `icon`, `notification_status`, `email_status`, `sms_status`, `sms_body`, `email_body`, `notification_body`, `short_codes`, `banner`, `title`, `subject`, `salutation`, `button_level`, `button_link`, `footer_status`, `footer_body`, `created_at`, `updated_at`) VALUES
(1, 'User Mail Send', 'user_mail', 'User', 'mail', 1, 1, 1, 'Thanks for joining us [[site_title]]. Find out more at [[site_url]].', '[[message]]<br /> Thanks for joining us [[site_title]]<br /><br /><br />Find out more about in - [[site_url]]', 'Thanks for joining us [[site_title]]. Find out more at [[site_url]].', '[\"[[full_name]]\",\"[[site_url]]\",\"[[site_title]]\",\"[[subject]]\",\"[[message]]\"]', 'global/images/Uxp3vfYFFi4GuO95lyZn.jpg', 'Admin Mail', '[[subject]] for [[full_name]]', 'Hi [[full_name]],', 'Login Your Account', NULL, 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(2, 'Subscriber Mail Send', 'subscriber_mail', 'Subscriber', 'mail', 1, 1, 1, 'Welcome to [[site_title]]! Manage your account, trade crypto, and earn profits. Visit [[site_url]].', 'Thanks for joining our platform! ---  [[site_title]]<br /><br />[[message]]<br /><br />As a member of our platform, you can manage your account, buy or sell cryptocurrency, invest and earn profits.<br /><br />Find out more about in - [[site_url]]', 'Welcome to [[site_title]]! Manage your account, trade crypto, and earn profits. Visit [[site_url]].', '[\"[[full_name]]\",\"[[site_url]]\",\"[[site_title]]\",\"[[subject]]\",\"[[message]]\"]', NULL, 'Welcome to [[site_title]]', '[[subject]] for [[full_name]]', 'Hi [[full_name]],', 'Login Your Account', NULL, 1, 'Thanks for joining our platform! ---  [[site_title]]<br /><br />[[message]]<br /><br />As a member of our platform, you can manage your account, buy or sell cryptocurrency, invest and earn profits.<br /><br />Find out more about in - [[site_url]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(3, 'Manual Deposit Request', 'admin_manual_deposit', 'Admin', 'dollar-sign', 1, 1, 1, 'New manual deposit request of [[amount]] [[currency]]. Please review and approve.', 'A new manual deposit request has been submitted.<br /><br />\n                Amount: [[amount]] [[currency]]<br />\n                Charge: [[charge]] [[currency]]<br />\n                Wallet: [[wallet]]<br />\n                Gateway: [[gateway]]<br />\n                Requested At: [[request_at]]<br />\n                Total Amount: [[total_amount]] [[currency]]<br /><br />\n                Please review and approve it.', 'New manual deposit request of [[amount]] [[currency]]. Please review and approve.', '[\"[[amount]]\",\"[[charge]]\",\"[[wallet]]\",\"[[gateway]]\",\"[[request_at]]\",\"[[total_amount]]\",\"[[request_link]]\",\"[[site_title]]\",\"[[currency]]\"]', 'global/images/deposit_request.jpg', 'New Manual Deposit Request', 'New Deposit Request of [[amount]] [[currency]]', 'Hello Admin,', 'View Request', '[[request_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(4, 'Manual Deposit Request Approved', 'user_manual_deposit_approved', 'User', 'check-circle', 1, 1, 1, 'Your deposit request of [[amount]] [[currency]] has been approved. Funds have been credited to your account.', 'We are pleased to inform you that your deposit request has been approved.<br /><br />\n        Amount: [[amount]] [[currency]]<br />\n        Charge: [[charge]] [[currency]]<br />\n        Wallet: [[wallet]]<br />\n        Gateway: [[gateway]]<br />\n        Requested At: [[request_at]]<br />\n        Total Amount: [[total_amount]] [[currency]]<br /><br />\n        The funds have been credited to your account. Thank you for using our services!', 'Your deposit request of [[amount]] [[currency]] has been approved. Funds have been credited to your account.', '[\"[[full_name]]\",\"[[amount]]\",\"[[charge]]\",\"[[wallet]]\",\"[[gateway]]\",\"[[request_at]]\",\"[[total_amount]]\",\"[[transaction_link]]\",\"[[site_title]]\",\"[[currency]]\"]', 'global/images/deposit_approved.jpg', 'Deposit Request Approved', 'Your Deposit Request of [[amount]] [[currency]] has been Approved', 'Hi [[full_name]],', 'View Transaction', '[[transaction_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(5, 'Manual Deposit Request Rejected', 'user_manual_deposit_rejected', 'User', 'x-circle', 1, 1, 1, 'Your deposit request of [[amount]] [[currency]] has been rejected. Reason: [[rejection_reason]].', 'We regret to inform you that your deposit request has been rejected.<br /><br />\n        Amount: [[amount]] [[currency]]<br />\n        Charge: [[charge]] [[currency]]<br />\n        Wallet: [[wallet]]<br />\n        Gateway: [[gateway]]<br />\n        Requested At: [[request_at]]<br />\n        Total Amount: [[total_amount]] [[currency]]<br /><br />\n        Reason for Rejection: [[rejection_reason]]<br /><br />', 'Your deposit request of [[amount]] [[currency]] has been rejected. Reason: [[rejection_reason]].', '[\"[[full_name]]\",\"[[amount]]\",\"[[charge]]\",\"[[wallet]]\",\"[[gateway]]\",\"[[request_at]]\",\"[[total_amount]]\",\"[[rejection_reason]]\",\"[[site_title]]\",\"[[currency]]\"]', 'global/images/deposit_rejected.jpg', 'Deposit Request Rejected', 'Your Deposit Request of [[amount]] [[currency]] has been Rejected', 'Hi [[full_name]],', 'Contact Support', '[[support_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(6, 'Withdraw Request', 'admin_withdraw_request', 'Admin', 'arrow-up', 1, 1, 1, 'New withdrawal request of [[amount]] [[currency]]. Please review and approve.', 'A new withdrawal request has been submitted.<br /><br />\n                Amount: [[amount]] [[currency]]<br />\n                Charge: [[charge]] [[currency]]<br />\n                Wallet: [[wallet]]<br />\n                Gateway: [[gateway]]<br />\n                Requested At: [[request_at]]<br />\n                Total Amount: [[total_amount]] [[currency]]<br /><br />\n                Please review and approve it.', 'New withdrawal request of [[amount]] [[currency]]. Please review and approve.', '[\"[[amount]]\",\"[[charge]]\",\"[[wallet]]\",\"[[gateway]]\",\"[[request_at]]\",\"[[total_amount]]\",\"[[request_link]]\",\"[[site_title]]\",\"[[currency]]\"]', 'global/images/withdraw_request.jpg', 'New Withdraw Request', 'Withdraw Request of [[amount]] [[currency]]', 'Hello Admin,', 'View Request', '[[request_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(7, 'Ticket Reply', 'admin_ticket_reply', 'Admin', 'message-circle', 1, 1, 1, 'New reply received for ticket: [[title]]. Message: [[message]].', 'A new reply has been received for the support ticket.<br /><br />\n                Ticket Title: [[title]]<br />\n                Message: [[message]]<br /><br />\n                Click the button below to view and respond.', 'New reply received for ticket: [[title]]. Message: [[message]].', '[\"[[title]]\",\"[[message]]\",\"[[reply_link]]\",\"[[site_title]]\"]', 'global/images/ticket_reply.jpg', 'New Ticket Reply', 'New Reply for Ticket: [[title]]', 'Hello Admin,', 'View Ticket', '[[reply_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(8, 'Invoice Payment Received', 'user_invoice_payment', 'User', 'file-text', 1, 1, 1, 'Payment received for Invoice #[[invoice_number]]. Amount: [[amount]] [[currency]]. Thank you!', 'We have received your payment for Invoice #[[invoice_number]].<br /><br />Amount: [[amount]] [[currency]]<br />Charge: [[charge]] [[currency]]<br />Total: [[total_amount]] [[currency]]<br /><br />Thank you for your payment!', 'Payment received for Invoice #[[invoice_number]]. Amount: [[amount]] [[currency]]. Thank you!', '[\"[[full_name]]\",\"[[invoice_number]]\",\"[[amount]]\",\"[[charge]]\",\"[[total_amount]]\",\"[[invoice_link]]\",\"[[site_title]]\",\"[[currency]]\"]', 'global/images/invoice_payment.jpg', 'Invoice Payment Received', 'Payment received for Invoice #[[invoice_number]]', 'Hi [[full_name]],', 'View Invoice', '[[invoice_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(9, 'Request Money', 'user_request_money', 'User', 'dollar-sign', 1, 1, 1, 'You received a money request from [[sender_name]]. Amount: [[amount]] [[currency]].', 'You have received a money request from [[sender_name]].<br /><br />Amount: [[amount]] [[currency]]<br />Charge: [[charge]] [[currency]]<br />Total: [[total_amount]] [[currency]]<br />Sender Note: [[sender_note]]<br />Sender Wallet: [[sender_wallet]]<br />Sender Account No: [[sender_account_no]]', 'You received a money request from [[sender_name]]. Amount: [[amount]] [[currency]].', '[\"[[full_name]]\",\"[[amount]]\",\"[[charge]]\",\"[[total_amount]]\",\"[[sender_name]]\",\"[[sender_note]]\",\"[[sender_wallet]]\",\"[[sender_account_no]]\",\"[[request_money_link]]\",\"[[site_title]]\",\"[[currency]]\"]', 'global/images/request_money.jpg', 'Money Request Received', 'You received a money request from [[sender_name]]', 'Hi [[full_name]],', 'View Request', '[[request_money_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(10, 'Gift Redeemed', 'user_gift_redeemed', 'User', 'gift', 1, 1, 1, 'Gift redeemed by [[redeemer_name]]. Amount: [[amount]] [[currency]]. Gift Code: [[gift_code]].', 'A gift has been redeemed successfully.<br /><br />Redeemer Name: [[redeemer_name]]<br />Redeemer Account No: [[redeemer_account_no]]<br />Amount: [[amount]] [[currency]]<br />Gift Code: [[gift_code]]<br />Redeemed At: [[redeemed_at]]', 'Gift redeemed by [[redeemer_name]]. Amount: [[amount]] [[currency]]. Gift Code: [[gift_code]].', '[\"[[full_name]]\",\"[[redeemer_name]]\",\"[[redeemer_account_no]]\",\"[[amount]]\",\"[[gift_code]]\",\"[[redeemed_at]]\",\"[[gift_redeem_link]]\",\"[[site_title]]\",\"[[currency]]\"]', 'global/images/gift_redeemed.jpg', 'Gift Successfully Redeemed', 'Gift Redeemed by [[redeemer_name]]', 'Hi [[full_name]],', 'View Details', '[[gift_redeem_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(11, 'Money Received', 'user_receive_money', 'User', 'arrow-down', 1, 1, 1, 'You have received [[amount]] [[currency]] from [[sender_name]]. Check your account.', 'You have received a money transfer.<br /><br />\n        Amount: [[amount]] [[currency]]<br />\n        Sender Name: [[sender_name]]<br />\n        Sender Account No: [[sender_account_no]]<br /><br />\n        The funds have been successfully credited to your account.', 'You have received [[amount]] [[currency]] from [[sender_name]].', '[\"[[full_name]]\",\"[[amount]]\",\"[[currency]]\",\"[[sender_name]]\",\"[[sender_account_no]]\",\"[[transaction_link]]\",\"[[site_title]]\"]', 'global/images/receive_money.jpg', 'Money Received Successfully', 'You have received money from [[sender_name]]', 'Hi [[full_name]],', 'View Transaction', '[[transaction_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(12, 'Referral Joining', 'user_referral_join', 'User', 'users', 1, 1, 1, 'A new referral, [[referred_name]], has joined. Joined at: [[joined_at]].', 'You have received a referral bonus.<br /><br />Referred Name: [[referred_name]]<br />Referred Account No: [[referred_account_no]]<br />Joined At: [[joined_at]]', 'Your referral [[referred_name]] has successfully joined. Joined at: [[joined_at]].', '[\"[[full_name]]\",\"[[referred_name]]\",\"[[referred_account_no]]\",\"[[joined_at]]\",\"[[referral_link]]\",\"[[site_title]]\"]', 'global/images/referral_join.jpg', 'Referral Joining', 'Your referral [[referred_name]] has successfully joined', 'Hi [[full_name]],', 'View Referral', '[[referral_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(13, 'Ticket Reply', 'user_ticket_reply', 'User', 'message-circle', 1, 1, 1, 'New reply received for ticket: [[title]]. Message: [[message]].', 'A new reply has been received on your support ticket \"<b>[[title]]</b>\".<br /><br />Message: [[message]].<br /><br />Click the button below to view the reply.', 'New reply received for ticket: [[title]]. Message: [[message]].', '[\"[[full_name]]\",\"[[title]]\",\"[[message]]\",\"[[reply_link]]\",\"[[site_title]]\"]', 'global/images/ticket_reply.jpg', 'New Ticket Reply', 'Reply received for Ticket: [[title]]', 'Hi [[full_name]],', 'View Ticket', '[[reply_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(14, 'Payment', 'merchant_payment', 'Merchant', 'credit-card', 1, 1, 1, 'New payment of [[amount]] received via [[gateway]].', 'A new payment of <b>[[amount]]</b> has been received.<br /><br />\n                        Wallet: [[wallet]]<br />\n                        Gateway: [[gateway]]<br />\n                        Charge: [[charge]]<br />\n                        Total Amount: [[total_amount]]<br /><br />\n                        Payment Date: [[payment_at]]<br /><br />\n                        Payment ID: [[payment_id]]<br /><br />\n                        Customer: [[user_name]] ([[user_account_no]])<br /><br />\n                        Please verify and process accordingly.', 'New payment of [[amount]] received via [[gateway]].', '[\"[[merchant_name]]\",\"[[amount]]\",\"[[charge]]\",\"[[total_amount]]\",\"[[wallet]]\",\"[[gateway]]\",\"[[payment_at]]\",\"[[payment_id]]\",\"[[user_name]]\",\"[[user_account_no]]\",\"[[site_title]]\"]', 'global/images/payment.jpg', 'New Payment Received', 'Payment Received: [[payment_id]]', 'Hi [[merchant_name]],', NULL, NULL, 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(15, 'Ticket Reply', 'merchant_ticket_reply', 'Merchant', 'message-circle', 1, 1, 1, 'New reply received for ticket: [[title]]. Message: [[message]].', 'A new reply has been received on a support ticket \"<b>[[title]]</b>\".<br /><br />Message: [[message]].<br /><br />Click the button below to view the reply.', 'New reply received for ticket: [[title]]. Message: [[message]].', '[\"[[merchant_name]]\",\"[[title]]\",\"[[message]]\",\"[[reply_link]]\",\"[[site_title]]\"]', 'global/images/ticket_reply.jpg', 'New Ticket Reply', 'Reply received for Ticket: [[title]]', 'Hi [[merchant_name]],', 'View Ticket', '[[reply_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(16, 'Withdraw Request Approved', 'withdraw_approved', 'User', 'check-circle', 1, 1, 1, 'Your withdrawal request of [[amount]] [[currency]] has been approved. Funds have been transferred.', 'We are pleased to inform you that your withdrawal request has been approved.<br /><br />\n        Amount: [[amount]] [[currency]]<br />\n        Charge: [[charge]] [[currency]]<br />\n        Wallet: [[wallet]]<br />\n        Gateway: [[gateway]]<br />\n        Requested At: [[request_at]]<br />\n        Total Amount: [[total_amount]] [[currency]]<br /><br />\n        The funds have been successfully transferred. Thank you for using our services!', 'Your withdrawal request of [[amount]] [[currency]] has been approved. Funds have been transferred.', '[\"[[full_name]]\",\"[[amount]]\",\"[[charge]]\",\"[[wallet]]\",\"[[gateway]]\",\"[[request_at]]\",\"[[total_amount]]\",\"[[transaction_link]]\",\"[[site_title]]\",\"[[currency]]\"]', 'global/images/withdraw_approved.jpg', 'Withdraw Request Approved', 'Your Withdraw Request of [[amount]] [[currency]] has been Approved', 'Hi [[full_name]],', 'View Transaction', '[[transaction_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(17, 'Withdraw Request Rejected', 'withdraw_rejected', 'User', 'x-circle', 1, 1, 1, 'Your withdrawal request of [[amount]] [[currency]] has been rejected. Reason: [[rejection_reason]].', 'We regret to inform you that your withdrawal request has been rejected.<br /><br />\n        Amount: [[amount]] [[currency]]<br />\n        Charge: [[charge]] [[currency]]<br />\n        Wallet: [[wallet]]<br />\n        Gateway: [[gateway]]<br />\n        Requested At: [[request_at]]<br />\n        Total Amount: [[total_amount]] [[currency]]<br /><br />\n        Reason for Rejection: [[rejection_reason]]<br /><br />', 'Your withdrawal request of [[amount]] [[currency]] has been rejected. Reason: [[rejection_reason]].', '[\"[[full_name]]\",\"[[amount]]\",\"[[charge]]\",\"[[wallet]]\",\"[[gateway]]\",\"[[request_at]]\",\"[[total_amount]]\",\"[[rejection_reason]]\",\"[[site_title]]\",\"[[currency]]\"]', 'global/images/withdraw_rejected.jpg', 'Withdraw Request Rejected', 'Your Withdraw Request of [[amount]] [[currency]] has been Rejected', 'Hi [[full_name]],', 'Contact Support', '[[support_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(18, 'Cash In Successful', 'user_cash_in', 'User', 'arrow-down-circle', 1, 1, 1, 'Cash-in successful! Amount: [[amount]] [[currency]], Wallet: [[wallet]], Total received: [[total_amount]] [[currency]].', 'Your cash-in request has been successfully processed.<br /><br />\n    Amount: [[amount]] [[currency]]<br />\n    Charge: [[charge]] [[currency]]<br />\n    Total Amount: [[total_amount]] [[currency]]<br />\n    Wallet: [[wallet]]<br />\n    Agent Name: [[agent_name]]<br />\n    Agent Account No: [[agent_account_no]]<br /><br />\n    Click the button below to view your transaction details.', 'You have successfully cashed in [[amount]] [[currency]] to [[wallet]]. Total received: [[total_amount]] [[currency]].', '[\"[[full_name]]\",\"[[amount]]\",\"[[charge]]\",\"[[total_amount]]\",\"[[wallet]]\",\"[[agent_name]]\",\"[[agent_account_no]]\",\"[[transaction_link]]\",\"[[site_title]]\",\"[[currency]]\"]', 'global/images/cash_in.jpg', 'Cash In Successful', 'You have successfully cashed in [[amount]] [[currency]]', 'Hi [[full_name]],', 'View Transaction', '[[transaction_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(19, 'Agent Commission Earned', 'agent_commission', 'Agent', 'dollar-sign', 1, 1, 1, 'You have received a commission of [[amount]]. Wallet: [[wallet]], Transaction ID: [[txn_id]].', 'You have earned a new commission.<br /><br />\n    <b>Amount:</b> [[amount]]<br />\n    <b>Wallet:</b> [[wallet]]<br />\n    <b>Transaction ID:</b> [[txn_id]]<br />\n    <b>Commission Date:</b> [[commission_at]]<br /><br />\n    Click the button below to view details.', 'You earned a commission of [[amount]]. Wallet: [[wallet]], Transaction ID: [[txn_id]].', '[\"[[full_name]]\",\"[[amount]]\",\"[[wallet]]\",\"[[commission_at]]\",\"[[txn_id]]\",\"[[transaction_link]]\",\"[[site_title]]\"]', 'global/images/agent_commission.jpg', 'Commission Earned', 'You have received a commission of [[amount]]', 'Hi [[full_name]],', 'View Commission', '[[transaction_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(20, 'Ticket Reply', 'agent_ticket_reply', 'Agent', 'message-circle', 1, 1, 1, 'New reply received for ticket: [[title]]. Message: [[message]].', 'A new reply has been received on a support ticket \"<b>[[title]]</b>\".<br /><br />\n    Message: [[message]].<br /><br />\n    Click the button below to view the reply.', 'New reply received for ticket: [[title]]. Message: [[message]].', '[\"[[agent_name]]\",\"[[title]]\",\"[[message]]\",\"[[reply_link]]\",\"[[site_title]]\"]', 'global/images/ticket_reply.jpg', 'New Ticket Reply', 'Reply received for Ticket: [[title]]', 'Hi [[agent_name]],', 'View Ticket', '[[reply_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(21, 'KYC Request', 'admin_kyc_request', 'Admin', 'check-circle', 1, 1, 1, 'New KYC request received from [[full_name]]. Review now.', 'A new KYC verification request has been submitted.<br /><br />\n    <b>Full Name:</b> [[full_name]]<br />\n    <b>Email:</b> [[email]]<br />\n    <b>KYC Type:</b> [[kyc_type]]<br /><br />\n    Click the button below to review the request.', 'New KYC request received from [[full_name]]. Click to review.', '[\"[[full_name]]\",\"[[email]]\",\"[[kyc_type]]\",\"[[kyc_review_link]]\",\"[[site_title]]\"]', 'global/images/kyc_request.jpg', 'New KYC Request from [[full_name]]', 'KYC request received from [[full_name]]', 'Hello Admin,', 'Review KYC', '[[kyc_review_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(22, 'KYC Action', 'kyc_action', 'User', 'check-circle', 1, 1, 1, 'Your KYC request is [[status]].', 'Your KYC verification request has been [[status]].<br /><br />\n    If you have any questions or need further assistance, please contact support.<br /><br />\n    Click the button below to view your KYC status.', 'Your KYC request has been [[status]]. Click to view.', '[\"[[full_name]]\",\"[[status]]\",\"[[kyc_status_link]]\",\"[[site_title]]\"]', 'global/images/kyc_action.jpg', 'Your KYC request is [[status]]', 'Your KYC request status update', 'Hi [[full_name]],', 'View KYC Status', '[[kyc_status_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(23, 'Forgot Password', 'forgot_password', 'User', 'key-round', 1, 1, 1, 'Reset your password here: [[token]] - [[site_title]]', 'We received a request to reset your password.<br /><br />\n    To reset your password, please click the button below or use the link provided.<br /><br />\n    If you didn’t request this, you can safely ignore this email.<br /><br />\n    Link: <a href=\"[[token]]\">[[token]]</a><br /><br />\n    Visit our site for more info: <a href=\"[[site_url]]\">[[site_url]]</a>', 'A password reset was requested. Click the link to proceed: [[token]]', '[\"[[token]]\",\"[[site_title]]\",\"[[site_url]]\"]', 'global/images/forgot_password.jpg', 'Reset Your Password', 'Reset Your Password - [[site_title]]', 'Hello,', 'Reset Password', '[[token]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(24, 'Email Verification', 'email_verification', 'User', 'check-circle', 1, 1, 1, 'Please verify your email address here: [[token]] - [[site_title]]', 'Hello!<br /><br />\n        Please click the button below to verify your email address.<br /><br />\n        If you didn’t request this, you can safely ignore this email.<br /><br />\n        <a href=\"[[token]]\">Verify Email Address</a><br /><br />\n        Visit our site for more info: <a href=\"[[site_url]]\">[[site_url]]</a>', 'Please verify your email address. Click the link to proceed: [[token]]', '[\"[[token]]\",\"[[full_name]]\",\"[[site_title]]\",\"[[site_url]]\"]', NULL, 'Verify Email Address', 'Verify Email Address - [[site_title]]', 'Hi [[full_name]],', 'Verify Email Address', '[[token]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(25, 'Contact Mail Send', 'contact_mail', 'Admin', 'mail', 0, 1, 0, NULL, 'Thanks for joining our platform! --- [[site_title]]<br /><br />\n[[message]]<br />\n[[full_name]]<br />\n[[email]]<br /><br />\nAs a member of our platform, you can mange your account, buy or sell cryptocurrency, invest and earn profits.<br /><br />\nFind out more about in - [[site_url]]', NULL, '[\"[[site_url]]\",\"[[site_title]]\",\"[[full_name]]\",\"[[email]]\",\"[[subject]]\",\"[[message]]\"]', NULL, 'Welcome to [[site_title]]', '[[subject]] for [[full_name]]', 'Hi [[full_name]],', 'Login Your Account', NULL, 1, 'Thanks for joining our platform! --- [[site_title]]<br /><br />\n[[message]]<br /><br /><br />\nAs a member of our platform, you can mange your account, buy or sell cryptocurrency, invest and earn profits.<br /><br />\nFind out more about in - [[site_url]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(26, 'App Forgot Password OTP', 'forgot_password_otp', 'User', 'key-round', 1, 1, 1, 'Reset your password here: [[token]] - [[site_title]]', 'We received a request to reset your password.<br /><br />\n        To reset your password, please click the button below or use the link provided.<br /><br />\n        If you didn’t request this, you can safely ignore this email.<br /><br />\n        Link: <a href=\"[[token]]\">[[token]]</a><br /><br />\n        Visit our site for more info: <a href=\"[[site_url]]\">[[site_url]]</a>', 'A password reset was requested. Click the link to proceed: [[token]]', '[\"[[token]]\",\"[[site_title]]\",\"[[site_url]]\"]', 'global/images/forgot_password.jpg', 'Reset Your Password', 'Reset Your Password - [[site_title]]', 'Hello,', 'Reset Password', NULL, 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(27, 'Email Verification', 'app_email_verification', 'User', 'check-circle', 1, 1, 1, 'Please verify your email address here: OTP: [[token]] - [[site_title]]', 'Hello!<br /><br />\n        Please use the otp below to verify your email address.<br /><br />\n        If you didn’t request this, you can safely ignore this email.<br /><br />\n        Use the given otp to verify your email address. <br>Your otp is <b>[[token]]</b><br /><br />\n        Visit our site for more info: <a href=\"[[site_url]]\">[[site_url]]</a>', 'Please verify your email address. Click the link to proceed: [[token]]', '[\"[[token]]\",\"[[full_name]]\",\"[[site_title]]\",\"[[site_url]]\"]', NULL, 'Verify Email Address', 'Verify Email Address - [[site_title]]', 'Hi [[full_name]],', NULL, NULL, 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(28, 'Ticket Closed', 'user_ticket_closed', 'User', 'check-circle', 1, 1, 1, 'Your ticket: [[title]] has been closed.', 'Your support ticket \"<b>[[title]]</b>\" has been closed.<br /><br />\n        Click the button below to view the ticket details.', 'Your ticket: [[title]] has been closed.', '[\"[[full_name]]\",\"[[title]]\",\"[[ticket_link]]\",\"[[site_title]]\"]', 'global/images/ticket_closed.jpg', 'Ticket Closed', 'Your Ticket: [[title]] Has Been Closed', 'Hi [[user_name]],', 'View Ticket', '[[ticket_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(29, 'New Ticket Created', 'admin_new_ticket', 'Admin', 'ticket', 1, 1, 1, 'New ticket created: [[title]]. Message: [[message]].', 'A new support ticket has been created.<br /><br />\n        Ticket Title: [[title]]<br />\n        Message: [[message]]<br /><br />\n        Click the button below to view and respond.', 'New ticket created: [[title]]. Message: [[message]].', '[\"[[title]]\",\"[[message]]\",\"[[ticket_link]]\",\"[[site_title]]\"]', 'global/images/new_ticket.jpg', 'New Ticket Created', 'New Ticket: [[title]]', 'Hello Admin,', 'View Ticket', '[[ticket_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(30, 'Request Money Accepted', 'user_request_money_accepted', 'User', 'check-circle', 1, 1, 1, 'Your request for [[amount]] [[currency]] has been accepted. [[site_title]]', 'Good news! Your money request of <b>[[amount]] [[currency]]</b> has been accepted.<br /><br />\n    The amount has been successfully added to your wallet.<br /><br />\n    Transaction ID: <b>[[trx_id]]</b><br />\n    Date: <b>[[date]]</b><br /><br />\n    You can view full transaction details in your dashboard.<br /><br />\n    Visit our site: <a href=\"[[site_url]]\">[[site_url]]</a>', 'Your money request of [[amount]] [[currency]] has been accepted successfully.', '[\"[[full_name]]\",\"[[amount]]\",\"[[currency]]\",\"[[trx_id]]\",\"[[date]]\",\"[[site_title]]\",\"[[site_url]]\",\"[[sender_name]]\",\"[[sender_note]]\",\"[[sender_wallet]]\",\"[[sender_account_no]]\",\"[[request_money_link]]\"]', 'global/images/request_money_accepted.jpg', 'Request Money Accepted', 'Your Money Request Has Been Accepted - [[site_title]]', 'Hi [[full_name]],', 'View Transaction', '[[transaction_link]]', 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(31, 'Agent Cashout Received', 'agent_cashout_received', 'Agent', 'dollar-sign', 1, 1, 1, 'Cash received from [[user_name]] for [[amount]] [[currency]]. Total: [[total_amount]] [[currency]].', 'You have received a cash from a user.<br /><br />\n    <b>User Name:</b> [[user_name]]<br />\n    <b>User Account No:</b> [[user_account_no]]<br />\n    <b>Amount:</b> [[amount]] [[currency]]<br />\n    <b>Charge:</b> [[charge]] [[currency]]<br />\n    <b>Total Amount:</b> [[total_amount]] [[currency]]<br />\n    <b>Wallet:</b> [[wallet]]<br />\n    <b>Transaction ID:</b> [[txn_id]]<br />\n    <b>Date:</b> [[date]]<br /><br />\n    Please provide the cash to the user.', 'Cash received from [[user_name]] for [[amount]] [[currency]].', '[\"[[full_name]]\",\"[[user_name]]\",\"[[user_account_no]]\",\"[[amount]]\",\"[[currency]]\",\"[[charge]]\",\"[[total_amount]]\",\"[[wallet]]\",\"[[txn_id]]\",\"[[date]]\",\"[[site_title]]\"]', 'global/images/agent_cashout.jpg', 'Cashout Request Received', 'Cash Received from [[user_name]] - [[site_title]]', 'Hi [[full_name]],', NULL, NULL, 1, 'Regards,<br />[[site_title]]', '2025-12-06 23:45:02', '2025-12-06 23:45:02'),
(32, 'Bill Pay', 'bill_pay', 'Admin', 'credit-card', 1, 1, 1, '[[user_name]] \'s \"[[service_name]]\" Pay bill completed.', '[[user_name]] \'s \"[[service_name]]\" Pay bill completed.<br /><br />Amount: [[amount]]<br />Charge: [[charge]]', '[[user_name]] \'s \"[[service_name]]\" Pay bill completed.', '[\"[[user_name]]\",\"[[service_name]]\",\"[[amount]]\",\"[[charge]]\"]', NULL, '[[user_name]] \'s \"[[service_name]]\" Pay bill completed.', '[[user_name]] \'s \"[[service_name]]\" Pay bill completed.', 'Hello Admin,', NULL, NULL, 1, 'Regards,', '2025-12-06 23:45:02', '2025-12-06 23:45:02');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `name`, `designation`, `picture`, `message`, `created_at`, `updated_at`) VALUES
(2, 'Sarah Ahmed', 'Business Owner', 'global/uploads/testimonials/J5n4kkxIjW54grrY2zuH.png', 'Qunzo App makes digital payments unbelievably simple. I can transfer money, pay bills, and withdraw anytime without any hassle.', '2025-12-09 06:51:06', '2025-12-09 06:57:44'),
(3, 'Jonathan Lee', 'Software Engineer', 'global/uploads/testimonials/XRDTdNHbVPcQ5fBpV42U.png', 'The app is fast, secure, and super reliable. Qunzo has become my go-to solution for managing daily financial transactions.', '2025-12-09 06:51:06', '2025-12-09 06:57:57'),
(4, 'Maria Gomez', 'Freelancer', 'global/uploads/testimonials/AOJiM8VoSLzimiowA7O7.png', 'I love how smooth everything works in Qunzo . From sending money to merchants to checking my wallet — everything is instant.', '2025-12-09 06:51:06', '2025-12-09 06:58:14'),
(5, 'David Khan', 'Agency Manager', 'global/uploads/testimonials/IIzOf1Y1LLx9GAivIobH.png', 'Qunzo App helped my business go fully digital. Accepting payments and managing customers is now easier than ever.', '2025-12-09 06:51:06', '2025-12-09 06:58:30'),
(6, 'Emily Carter', 'Marketing Specialist', 'global/uploads/testimonials/AETr79KucoI9kwZBbGLa.png', 'The user interface is clean and easy to understand. Even first-time users can handle transactions effortlessly with Qunzo.', '2025-12-09 06:51:06', '2025-12-09 06:58:43');

-- --------------------------------------------------------

--
-- Table structure for table `themes`
--

CREATE TABLE `themes` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('landing','site') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'landing',
  `status` tinyint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `themes`
--

INSERT INTO `themes` (`id`, `name`, `type`, `status`, `created_at`, `updated_at`) VALUES
(1, 'default', 'site', 1, '2024-11-19 22:45:36', '2025-05-03 09:59:14');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `attachments` json DEFAULT NULL,
  `priority` enum('low','medium','high') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'low',
  `status` enum('open','closed','pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'open',
  `is_resolved` tinyint(1) DEFAULT '0',
  `is_locked` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `from_user_id` bigint UNSIGNED DEFAULT NULL,
  `from_model` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'User',
  `target_id` bigint UNSIGNED DEFAULT NULL,
  `invoice_id` bigint UNSIGNED DEFAULT NULL,
  `target_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wallet_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_level` tinyint(1) DEFAULT '0',
  `tnx` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(28,8) NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `charge` decimal(28,8) NOT NULL,
  `final_amount` decimal(28,8) NOT NULL,
  `method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pay_currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pay_amount` decimal(28,8) DEFAULT NULL,
  `callback_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `manual_field_data` json DEFAULT NULL,
  `approval_cause` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `role` enum('User','Agent','Merchant') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'User',
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `kyc` tinyint DEFAULT '4',
  `phone_verified` tinyint DEFAULT '0',
  `otp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `balance` decimal(28,8) DEFAULT '0.00000000',
  `passcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('male','female','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `close_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ref_id` int DEFAULT NULL,
  `referral_code` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `google2fa_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_fa` tinyint DEFAULT '0',
  `withdraw_status` tinyint DEFAULT '1',
  `otp_status` tinyint DEFAULT '1',
  `deposit_status` tinyint DEFAULT '1',
  `transfer_status` tinyint DEFAULT '1',
  `referral_status` tinyint DEFAULT '1',
  `payment_status` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `current_step` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'completed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_devices`
--

CREATE TABLE `user_devices` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `device_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fcm_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_kycs`
--

CREATE TABLE `user_kycs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `kyc_id` bigint UNSIGNED DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_valid` tinyint DEFAULT '0',
  `status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_navigations`
--

CREATE TABLE `user_navigations` (
  `id` bigint UNSIGNED NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visible_to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` int DEFAULT '0',
  `translation` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_wallets`
--

CREATE TABLE `user_wallets` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `currency_id` bigint UNSIGNED DEFAULT NULL,
  `balance` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawal_schedules`
--

CREATE TABLE `withdrawal_schedules` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `withdrawal_schedules`
--

INSERT INTO `withdrawal_schedules` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Sunday', 1, NULL, '2025-05-03 05:55:12'),
(2, 'Monday', 1, NULL, '2025-05-03 05:55:12'),
(3, 'Tuesday', 1, NULL, '2025-05-03 05:55:12'),
(4, 'Wednesday', 1, NULL, '2025-05-03 05:55:12'),
(5, 'Thursday', 1, NULL, '2025-05-03 05:55:12'),
(6, 'Friday', 1, NULL, '2025-05-03 05:55:12'),
(7, 'Saturday', 1, NULL, '2025-05-03 05:55:12');

-- --------------------------------------------------------

--
-- Table structure for table `withdraw_accounts`
--

CREATE TABLE `withdraw_accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `user_wallet_id` bigint UNSIGNED NOT NULL,
  `withdraw_method_id` bigint UNSIGNED DEFAULT NULL,
  `method_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credentials` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdraw_methods`
--

CREATE TABLE `withdraw_methods` (
  `id` bigint UNSIGNED NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'manual',
  `gateway_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rate` decimal(28,8) DEFAULT NULL,
  `required_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `required_time_format` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `charge` decimal(28,8) DEFAULT NULL,
  `charge_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_withdraw` decimal(28,8) DEFAULT NULL,
  `max_withdraw` decimal(28,8) DEFAULT NULL,
  `fields` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

--
-- Indexes for table `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bill_services`
--
ALTER TABLE `bill_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cron_jobs`
--
ALTER TABLE `cron_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cron_job_logs`
--
ALTER TABLE `cron_job_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `custom_csses`
--
ALTER TABLE `custom_csses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deposit_methods`
--
ALTER TABLE `deposit_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `gateways`
--
ALTER TABLE `gateways`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gateways_gateway_code_unique` (`gateway_code`);

--
-- Indexes for table `gifts`
--
ALTER TABLE `gifts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gifts_code_unique` (`code`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoices_number_unique` (`number`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kycs`
--
ALTER TABLE `kycs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `landing_contents`
--
ALTER TABLE `landing_contents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `landing_pages`
--
ALTER TABLE `landing_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `level_referrals`
--
ALTER TABLE `level_referrals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_activities`
--
ALTER TABLE `login_activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `megamenu_items`
--
ALTER TABLE `megamenu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `merchants`
--
ALTER TABLE `merchants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `money_requests`
--
ALTER TABLE `money_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `navigations`
--
ALTER TABLE `navigations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page_settings`
--
ALTER TABLE `page_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_settings_key_unique` (`key`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `plugins`
--
ALTER TABLE `plugins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sandbox_transactions`
--
ALTER TABLE `sandbox_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_name_unique` (`name`);

--
-- Indexes for table `set_tunes`
--
ALTER TABLE `set_tunes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `socials`
--
ALTER TABLE `socials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subscribers_email_unique` (`email`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `templates_code_unique` (`code`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `themes`
--
ALTER TABLE `themes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_account_number_unique` (`account_number`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_devices`
--
ALTER TABLE `user_devices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_kycs`
--
ALTER TABLE `user_kycs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_navigations`
--
ALTER TABLE `user_navigations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_wallets`
--
ALTER TABLE `user_wallets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdrawal_schedules`
--
ALTER TABLE `withdrawal_schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdraw_accounts`
--
ALTER TABLE `withdraw_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdraw_methods`
--
ALTER TABLE `withdraw_methods`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `agents`
--
ALTER TABLE `agents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bill_services`
--
ALTER TABLE `bill_services`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cron_jobs`
--
ALTER TABLE `cron_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cron_job_logs`
--
ALTER TABLE `cron_job_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custom_csses`
--
ALTER TABLE `custom_csses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `deposit_methods`
--
ALTER TABLE `deposit_methods`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gateways`
--
ALTER TABLE `gateways`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `gifts`
--
ALTER TABLE `gifts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kycs`
--
ALTER TABLE `kycs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `landing_contents`
--
ALTER TABLE `landing_contents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=650;

--
-- AUTO_INCREMENT for table `landing_pages`
--
ALTER TABLE `landing_pages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `level_referrals`
--
ALTER TABLE `level_referrals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `login_activities`
--
ALTER TABLE `login_activities`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `megamenu_items`
--
ALTER TABLE `megamenu_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `merchants`
--
ALTER TABLE `merchants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `money_requests`
--
ALTER TABLE `money_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `navigations`
--
ALTER TABLE `navigations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `page_settings`
--
ALTER TABLE `page_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plugins`
--
ALTER TABLE `plugins`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sandbox_transactions`
--
ALTER TABLE `sandbox_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=186;

--
-- AUTO_INCREMENT for table `set_tunes`
--
ALTER TABLE `set_tunes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `socials`
--
ALTER TABLE `socials`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `themes`
--
ALTER TABLE `themes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_devices`
--
ALTER TABLE `user_devices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_kycs`
--
ALTER TABLE `user_kycs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_navigations`
--
ALTER TABLE `user_navigations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_wallets`
--
ALTER TABLE `user_wallets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdrawal_schedules`
--
ALTER TABLE `withdrawal_schedules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `withdraw_accounts`
--
ALTER TABLE `withdraw_accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `withdraw_methods`
--
ALTER TABLE `withdraw_methods`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
