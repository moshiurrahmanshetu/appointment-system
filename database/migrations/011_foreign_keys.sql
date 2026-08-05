-- Add foreign keys (simple version - may fail on re-run which is acceptable)
ALTER TABLE `audit_logs` 
ADD CONSTRAINT `fk_audit_logs_user` 
FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `users` 
ADD CONSTRAINT `fk_users_created_by` 
FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;