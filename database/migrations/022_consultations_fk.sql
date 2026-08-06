-- Add foreign keys for consultations table (separate migration for re-run safety)
ALTER TABLE `consultations` 
ADD CONSTRAINT `fk_consultations_appointment` 
FOREIGN KEY (`appointment_id`) REFERENCES `appointments`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `consultations` 
ADD CONSTRAINT `fk_consultations_queue` 
FOREIGN KEY (`queue_id`) REFERENCES `queue`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `consultations` 
ADD CONSTRAINT `fk_consultations_patient` 
FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `consultations` 
ADD CONSTRAINT `fk_consultations_doctor` 
FOREIGN KEY (`doctor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `consultations` 
ADD CONSTRAINT `fk_consultations_created_by` 
FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `consultations` 
ADD CONSTRAINT `fk_consultations_updated_by` 
FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;