-- Add foreign keys for patients table (separate migration for re-run safety)
ALTER TABLE `patients` 
ADD CONSTRAINT `fk_patients_user` 
FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `patients` 
ADD CONSTRAINT `fk_patients_created_by` 
FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;