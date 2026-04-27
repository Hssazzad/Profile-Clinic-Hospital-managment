ALTER TABLE `bill_types`
ADD COLUMN `main_code` INT NULL AFTER `color`;

UPDATE `bill_types`
SET `main_code` = NULL
WHERE `slug` IN ('doctor_visit', 'investigation', 'full_bill');

-- Example bill type mappings to existing configMain codes.
-- Uncomment and adjust if these slugs already exist or if you want to insert new bill types.
--
-- INSERT INTO `bill_types`
-- (`slug`, `name`, `icon`, `color`, `main_code`, `requires_doctor`, `requires_category`, `free_text_items`, `is_active`, `sort_order`, `created_at`)
-- VALUES
-- ('admission', 'Admission', 'fa-microscope', '#059669', 100, 0, 1, 0, 1, 3, NOW()),
-- ('xray', 'Xray', 'fa-microscope', '#059669', 101, 0, 1, 0, 1, 4, NOW()),
-- ('ultrasono', 'Altrashono', 'fa-microscope', '#059669', 103, 0, 1, 0, 1, 5, NOW()),
-- ('radiology_imaging', 'Radiology & Imaging', 'fa-microscope', '#059669', 104, 0, 1, 0, 1, 6, NOW());
