-- Add foreign keys for appointments table (separate migration for re-run safety)
ALTER TABLE `appointments` 
ADD CONSTRAINT `fk_appointments_patient` 
FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `appointments` 
ADD CONSTRAINT `fk_appointments_doctor` 
FOREIGN KEY (`doctor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `appointments` 
ADD CONSTRAINT `fk_appointments_created_by` 
FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `appointments` 
ADD CONSTRAINT `fk_appointments_updated_by` 
FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;