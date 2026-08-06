-- Add foreign keys for queue table (separate migration for re-run safety)
ALTER TABLE `queue` 
ADD CONSTRAINT `fk_queue_appointment` 
FOREIGN KEY (`appointment_id`) REFERENCES `appointments`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `queue` 
ADD CONSTRAINT `fk_queue_doctor` 
FOREIGN KEY (`doctor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `queue` 
ADD CONSTRAINT `fk_queue_created_by` 
FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `queue` 
ADD CONSTRAINT `fk_queue_updated_by` 
FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;