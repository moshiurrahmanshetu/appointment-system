-- Add foreign keys for patients table (separate migration for re-run safety)
-- Check if foreign key exists before adding
SET @exists = (
    SELECT COUNT(*)
    FROM information_schema.table_constraints
    WHERE constraint_schema = DATABASE()
    AND table_name = 'patients'
    AND constraint_name = 'fk_patients_user'
);

SET @sql = IF(@exists = 0,
    'ALTER TABLE `patients` ADD CONSTRAINT `fk_patients_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE',
    'SELECT "Foreign key fk_patients_user already exists" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @exists = (
    SELECT COUNT(*)
    FROM information_schema.table_constraints
    WHERE constraint_schema = DATABASE()
    AND table_name = 'patients'
    AND constraint_name = 'fk_patients_created_by'
);

SET @sql = IF(@exists = 0,
    'ALTER TABLE `patients` ADD CONSTRAINT `fk_patients_created_by` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE',
    'SELECT "Foreign key fk_patients_created_by already exists" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;