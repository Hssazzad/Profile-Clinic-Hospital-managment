-- Add Surgery Template System Tables to u972011074_vzeTw database

-- Create medicines table
CREATE TABLE IF NOT EXISTS `medicines` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('Injection','Tablet','Capsule','Syrup','Ointment','Drops','Inhaler','Other') NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `strength` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `medicines_type_is_active_index` (`type`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create surgery_templates table
CREATE TABLE IF NOT EXISTS `surgery_templates` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_name` varchar(255) NOT NULL,
  `rx_admission` json DEFAULT NULL,
  `pre_op_orders` json DEFAULT NULL,
  `post_op_orders` json DEFAULT NULL,
  `investigations` json DEFAULT NULL,
  `advices` json DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `surgery_templates_template_name_is_active_index` (`template_name`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample medicines
INSERT IGNORE INTO `medicines` (`name`, `type`, `company_name`, `strength`, `created_at`, `updated_at`) VALUES
('Paracetamol', 'Tablet', 'Beximco Pharma', '500mg', NOW(), NOW()),
('Amoxicillin', 'Capsule', 'Square Pharma', '500mg', NOW(), NOW()),
('Normal Saline', 'Injection', 'Beximco Pharma', '500ml', NOW(), NOW()),
('Ceftriaxone', 'Injection', 'Incepta Pharma', '1gm', NOW(), NOW()),
('Omeprazole', 'Capsule', 'Square Pharma', '20mg', NOW(), NOW());

-- Insert a sample surgery template
INSERT IGNORE INTO `surgery_templates` (`template_name`, `rx_admission`, `pre_op_orders`, `post_op_orders`, `investigations`, `advices`, `created_at`, `updated_at`) VALUES
('C-Section Template', 
'[{"type":"Injection","medicine_id":3,"company_name":"Beximco Pharma","dosage":"500ml","frequency":"Once daily","duration":"1 day","instructions":"IV infusion"}]',
'["NPO after midnight", "IV line setup", "Pre-op antibiotics", "Consent signed"]',
'["Vital signs monitoring", "Pain management", "Wound care", "Breastfeeding support"]',
'["CBC", "Blood group & Rh", "Urine R/E", "USG pelvis", "HIV test", "HbsAg"]',
'["Rest for 6 weeks", "Avoid heavy lifting", "Follow up after 7 days", "Medication compliance"]',
NOW(), NOW());

SELECT 'Surgery Template System tables created successfully!' as message;
