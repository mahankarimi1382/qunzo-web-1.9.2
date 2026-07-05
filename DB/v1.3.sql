ALTER TABLE `users` CHANGE `passcode` `passcode` VARCHAR(255) NOT NULL DEFAULT '0';
-- 
-- Permissions for new features
--
INSERT INTO `permissions`
(`category`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
('Transaction Management', 'admin-profits', 'admin', '2025-12-22 21:40:17', '2025-12-22 21:40:17'),
('Bill Management', 'bill-service-import', 'admin', '2025-12-22 21:40:17', '2025-12-22 21:40:17'),
('Bill Management', 'bill-service-list', 'admin', '2025-12-22 21:40:17', '2025-12-22 21:40:17'),
('Bill Management', 'bill-service-edit', 'admin', '2025-12-22 21:40:17', '2025-12-22 21:40:17'),
('Bill Management', 'bill-convert-rate', 'admin', '2025-12-22 21:40:17', '2025-12-22 21:40:17'),
('Bill Management', 'all-bills', 'admin', '2025-12-22 21:40:17', '2025-12-22 21:40:17'),
('Bill Management', 'pending-bills', 'admin', '2025-12-22 21:40:17', '2025-12-22 21:40:17'),
('Bill Management', 'complete-bills', 'admin', '2025-12-22 21:40:17', '2025-12-22 21:40:17'),
('Bill Management', 'return-bills', 'admin', '2025-12-22 21:40:17', '2025-12-22 21:40:17');