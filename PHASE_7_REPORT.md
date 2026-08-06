# Phase 7 Report: Doctor Consultation

## Overview
Phase 7 implements a complete Doctor Consultation module for the Appointment Queue System. This module provides comprehensive consultation management functionality including automatic consultation number generation, medical information recording, patient consultation history, and seamless integration with the queue and appointment workflows. The module ensures that consultations can only be started when a patient's queue status is "With Doctor" and automatically completes the queue and appointment when the consultation is completed.

## Implementation Date
August 6, 2026

## Database Changes

### New Tables Created

#### 1. consultations Table (`021_consultations.sql`)
- **Purpose**: Store consultation records with medical information
- **Fields**:
  - `id` - Primary key
  - `consultation_no` - Unique consultation number (CON-000001 format)
  - `appointment_id` - Foreign key to appointments table
  - `queue_id` - Foreign key to queue table
  - `patient_id` - Foreign key to patients table
  - `doctor_id` - Foreign key to users table
  - `visit_type` - ENUM: 'New', 'Follow-up'
  - `chief_complaint` - Patient's chief complaint
  - `history` - Medical history
  - `physical_examination` - Physical examination findings
  - `diagnosis` - Diagnosis
  - `doctor_notes` - Additional doctor notes
  - `follow_up_required` - ENUM: 'Yes', 'No'
  - `follow_up_date` - Follow-up appointment date
  - `consultation_status` - ENUM: 'Draft', 'Completed'
  - `created_by` - User who created the consultation
  - `updated_by` - User who last updated the consultation
  - `deleted_at` - Soft delete timestamp
  - `created_at` - Creation timestamp
  - `updated_at` - Last update timestamp
- **Indexes**: consultation_no, appointment_id, queue_id, patient_id, doctor_id, consultation_status, follow_up_date, deleted_at, created_by, patient_doctor_date
- **Foreign Keys**: Added in separate migration (022_consultations_fk.sql)

#### 2. Permissions (`023_consultation_permissions.sql`)
- **New Permissions Added**:
  - `consultation.view` - View consultation records
  - `consultation.create` - Create new consultations
  - `consultation.edit` - Edit consultation information
  - `consultation.delete` - Soft delete consultation records
  - `consultation.complete` - Complete consultations
- **Role Assignments**:
  - Admin: All consultation permissions
  - Doctor: view, create, edit, complete
  - Receptionist: view only

## Files Created

### Models
1. **app/models/Consultation.php** (337 lines)
   - `generateConsultationNo()` - Auto-generates consultation numbers (CON-000001 format)
   - `findByConsultationNo($consultationNo)` - Find consultation by consultation number
   - `getAllConsultations($onlyActive, $includeDeleted)` - Get all consultations with relations
   - `getConsultationsByPatient($patientId)` - Get patient's consultation history
   - `getConsultationsByDoctor($doctorId)` - Get consultations for specific doctor
   - `getTodayConsultations($doctorId)` - Get today's consultations
   - `getFollowUpConsultations($date)` - Get follow-up consultations for specific date
   - `searchConsultations($searchTerm, $filters)` - Search with filters
   - `softDelete($consultationId)` - Soft delete consultation
   - `restore($consultationId)` - Restore deleted consultation
   - `completeConsultation($consultationId)` - Complete consultation
   - `checkConsultationNoAvailability($consultationNo, $excludeId)` - Check consultation number uniqueness
   - `checkConsultationExistsForQueue($queueId)` - Check if consultation exists for queue
   - `checkConsultationExistsForAppointment($appointmentId)` - Check if consultation exists for appointment
   - `getConsultationWithDetails($consultationId)` - Get consultation with full details
   - `getPatientConsultationHistory($patientId, $limit)` - Get patient's consultation history
   - `getConsultationStats($doctorId)` - Get consultation statistics
   - `count($where)` - Count consultation entries

### Controllers
2. **app/controllers/ConsultationController.php** (559 lines)
   - `index()` - List consultations with search and filters
   - `create()` - Show consultation creation form with queue/appointment data
   - `store()` - Create new consultation with validation
   - `show($id)` - Display consultation details with history
   - `edit($id)` - Show consultation edit form
   - `update($id)` - Update consultation with auto-completion workflow
   - `delete($id)` - Soft delete consultation
   - `complete($id)` - Complete consultation with auto-workflow
   - `getDoctorIdForCurrentUser()` - Get doctor ID for current user (private)
   - Features:
     - Doctor-specific consultation views
     - Super admin sees all consultations
     - Permission checks on all methods
     - Audit logging for all operations
     - Queue status validation for consultation creation
     - Duplicate consultation prevention
     - Auto-complete queue and appointment on consultation completion
     - Previous consultation history display

### Views
3. **app/views/consultations/index.php** (250 lines)
   - Consultation list with search functionality
   - Dashboard stats cards (Draft, Completed, Today, Follow-up Today)
   - Filter by doctor, status, visit type
   - Status badges with color coding
   - Action buttons based on permissions and status
   - Follow-up date display
   - Responsive Bootstrap 5 table

4. **app/views/consultations/create.php** (247 lines)
   - Consultation creation form with tabbed layout
   - Patient summary card with key information
   - Queue/Appointment information card
   - Medical Information tab: doctor, visit type, chief complaint, history, physical examination, diagnosis, doctor notes
   - Follow-up tab: follow-up required, follow-up date
   - Guidelines sidebar
   - Quick actions for patient/appointment viewing
   - Validation error display

5. **app/views/consultations/edit.php** (201 lines)
   - Consultation edit form with tabbed layout
   - Read-only consultation information card
   - Medical Information tab with all fields
   - Follow-up tab with status change option
   - Complete consultation status with permission check
   - Patient information sidebar
   - Validation and error handling

6. **app/views/consultations/show.php** (317 lines)
   - Detailed consultation view
   - Complete consultation information
   - Medical information section
   - Previous consultation history table
   - Patient contact information
   - Visit information (appointment, queue, priority)
   - Status display with color coding
   - Action buttons based on permissions and status
   - System information (created/updated by)
   - Back navigation

### Database Migrations
7. **database/migrations/021_consultations.sql** (33 lines)
   - Consultations table creation
   - All field definitions
   - Indexes for performance
   - Engine and charset settings

8. **database/migrations/022_consultations_fk.sql** (24 lines)
   - Foreign key constraints
   - Cascade delete/update rules
   - Appointment, queue, patient, doctor, created_by, updated_by relationships

9. **database/migrations/023_consultation_permissions.sql** (31 lines)
   - Permission definitions
   - Role-permission assignments
   - Insert with IGNORE for re-run safety

## Files Modified

### Controllers
1. **app/controllers/QueueController.php**
   - Modified `startConsultation()` method to redirect to consultation creation page
   - Instead of just updating queue status, now redirects to `/consultations/create?queue_id={id}`
   - Enables seamless workflow from queue to consultation

2. **app/controllers/DashboardController.php**
   - Added Consultation model import
   - Modified `index()` method to get consultation statistics
   - Doctor-specific consultation stats
   - Added stats to view data

### Routes
3. **routes/web.php**
   - Added consultation management routes with permission middleware:
     - GET `/consultations` - List consultations
     - GET `/consultations/create` - Create form
     - POST `/consultations` - Store consultation
     - GET `/consultations/show/{id}` - View consultation
     - GET `/consultations/edit/{id}` - Edit form
     - POST `/consultations/update/{id}` - Update consultation
     - GET `/consultations/delete/{id}` - Delete consultation
     - GET `/consultations/complete/{id}` - Complete consultation

### Sidebar
4. **app/views/partials/sidebar.php**
   - Added Consultations menu item with link to actual route
   - Added active state detection for consultation pages
   - Permission check for display

### Dashboard View
5. **app/views/dashboard/index.php**
   - Added consultation statistics row (Today's Consultations, Completed Today, Follow-up Today)
   - Added "View Consultations" quick action button
   - Real-time data display

## Key Features Implemented

### 1. Consultation Number Generation
- Automatic generation in CON-000001 format
- Increments sequentially from last consultation
- Unique constraint in database
- Format: CON-XXXXXX (6-digit zero-padded number)

### 2. Queue-Consultation Workflow Integration
- Consultations can only be created from queue entries with status "With Doctor"
- QueueController redirects to consultation creation when starting consultation
- Prevents duplicate consultation creation for same queue
- Validation ensures proper workflow compliance

### 3. Auto-Completion Workflow
- When consultation is marked as "Completed":
  - Queue automatically becomes "Completed"
  - Appointment automatically becomes "Completed"
  - All changes audit logged
  - Ensures data consistency across modules
- Prevents manual status management errors

### 4. Status Management
- 2 status options:
  - Draft (Yellow badge) - Work in progress
  - Completed (Green badge) - Finalized consultation
- Status transition validation
- Permission-based status changes
- Completed consultations require special permission to edit

### 5. Medical Information Recording
- Comprehensive medical data capture:
  - Chief Complaint
  - Medical History
  - Physical Examination
  - Diagnosis
  - Doctor Notes
- Text fields for detailed information
- Organized in tabbed interface
- Professional medical form layout

### 6. Follow-up Management
- Follow-up required field (Yes/No)
- Follow-up date picker
- Follow-up tracking
- Dashboard shows follow-up count for today
- Helps manage patient follow-up schedules

### 7. Patient Consultation History
- Previous consultations displayed in consultation detail view
- Shows:
  - Consultation No
  - Date
  - Doctor
  - Diagnosis (truncated)
  - Quick action to view
- Configurable limit (default: 10)
- Helps doctors understand patient history

### 8. Tabbed Interface
- Professional tabbed layout for consultation forms
- Medical Information tab:
  - Doctor selection
  - Visit type
  - Chief complaint
  - History
  - Physical examination
  - Diagnosis
  - Doctor notes
- Follow-up tab:
  - Follow-up required
  - Follow-up date
- Clean organization of form fields

### 9. Search and Filtering
- Search by:
  - Consultation number
  - Patient name
  - Patient code
  - Patient phone
  - Doctor name
  - Diagnosis
- Filter by:
  - Doctor
  - Patient
  - Status
  - Visit type
- Combined search and filter support

### 10. Doctor-Specific Views
- Doctors see only their own consultations
- Super admin sees all consultations
- Receptionist can view all consultations with limited actions
- Role-based access control
- Automatic doctor ID detection

### 11. Dashboard Statistics
- Today's Consultations count
- Completed Today count
- Follow-up Today count
- Draft count
- Real-time data
- Doctor-specific stats for doctors

### 12. Validation
- Patient required and must exist
- Doctor required and must exist
- Prevents duplicate consultation for same queue
- Prevents duplicate consultation for same appointment
- Queue status validation (must be "With Doctor")
- Prevents editing completed consultations without permission

### 13. Permission-Based Access Control
- 5 consultation-specific permissions
- Route-level permission checks
- View-level permission checks
- Button-level permission checks
- Role-based assignments:
  - Admin: Full access
  - Doctor: view, create, edit, complete
  - Receptionist: view only

### 14. Audit Logging
- Logs all consultation operations:
  - consultation_started
  - consultation_updated
  - consultation_completed
  - consultation_deleted
  - queue_completed (when auto-completed)
  - appointment_status_changed (when auto-completed)
- Records old and new values
- Tracks user who performed action

## Routes Added

| Method | Path | Controller Method | Permission Required |
|--------|------|-------------------|---------------------|
| GET | /consultations | index | consultation.view |
| GET | /consultations/create | create | consultation.create |
| POST | /consultations | store | consultation.create |
| GET | /consultations/show/{id} | show | consultation.view |
| GET | /consultations/edit/{id} | edit | consultation.edit |
| POST | /consultations/update/{id} | update | consultation.edit |
| GET | /consultations/delete/{id} | delete | consultation.delete |
| GET | /consultations/complete/{id} | complete | consultation.complete |

## Database Tables

### consultations
- Primary Key: id
- Foreign Keys:
  - appointment_id → appointments.id (SET NULL)
  - queue_id → queue.id (SET NULL)
  - patient_id → patients.id (CASCADE)
  - doctor_id → users.id (CASCADE)
  - created_by → users.id (SET NULL)
  - updated_by → users.id (SET NULL)
- Soft Delete: deleted_at
- Timestamps: created_at, updated_at

### permissions (Updated)
- Added 5 new consultation permissions
- Assigned to admin, doctor, receptionist roles

### role_permissions (Updated)
- Added consultation permissions to role-permission mappings

## Completed Features

✅ Database schema for consultations
✅ Consultation model with all methods
✅ ConsultationController with CRUD operations
✅ Consultation list view with search and filters
✅ Consultation create view with tabs
✅ Consultation edit view with tabs
✅ Consultation show view with history
✅ Automatic consultation number generation (CON-000001)
✅ Queue-Consultation workflow integration
✅ Auto-complete queue and appointment on consultation completion
✅ Permission-based access control
✅ Audit logging for all operations
✅ Status management with color coding
✅ Status transition validation
✅ Medical information recording
✅ Follow-up management
✅ Patient consultation history display
✅ Tabbed interface for forms
✅ Search and filtering capabilities
✅ Doctor-specific consultation views
✅ Dashboard statistics widgets
✅ Form validation and error handling
✅ Responsive Bootstrap 5 UI
✅ Route protection with middleware
✅ Sidebar integration
✅ Dashboard integration
✅ Database migrations
✅ Duplicate consultation prevention
✅ Queue status validation

## Pending Features

❌ Pagination for consultation list
❌ Consultation templates
❌ Medical records attachments
❌ Prescription management
❌ Lab results integration
❌ Imaging reports integration
❌ Consultation notes with formatting
❌ Voice-to-text for notes
❌ Consultation duration tracking
❌ Consultation statistics by period
❌ Doctor performance metrics
❌ Patient health trends
❌ Consultation reports generation
❌ Export consultation to PDF
❌ Consultation sharing with other doctors
❌ Treatment plan management
❌ Medication prescription system
❌ Allergy tracking
❌ Vital signs recording
❌ Clinical decision support
❌ Consultation reminders
❌ Referral system

## Testing Status

The application has been deployed and is accessible at http://localhost:8080. The following should be tested:

1. **Consultation Creation from Queue**
   - Navigate to queue management
   - Call patient to bring to "With Doctor" status
   - Start consultation to redirect to consultation creation
   - Verify patient and appointment information pre-filled
   - Create consultation with medical information
   - Verify consultation number generation

2. **Consultation Creation Standalone**
   - Create consultation without queue
   - Select patient and doctor manually
   - Fill in medical information
   - Set follow-up if required
   - Verify consultation creation

3. **Consultation Viewing**
   - View consultation list
   - Test search functionality
   - Test filters (doctor, status, visit type)
   - View consultation details
   - Verify patient consultation history display

4. **Consultation Editing**
   - Edit consultation information
   - Test tabbed interface
   - Update medical information
   - Change follow-up requirements
   - Verify validation

5. **Consultation Completion**
   - Complete consultation from edit form
   - Complete consultation from complete action
   - Verify queue becomes "Completed"
   - Verify appointment becomes "Completed"
   - Check audit logs for all changes

6. **Consultation Deletion**
   - Soft delete consultation
   - Verify data integrity
   - Check audit logs

7. **Doctor-Specific Views**
   - Login as doctor user
   - Verify only own consultations are visible
   - Test consultation actions
   - Login as admin
   - Verify all consultations are visible

8. **Permission Testing**
   - Test admin access (full permissions)
   - Test doctor access (view, create, edit, complete)
   - Test receptionist access (view only)
   - Verify permission-based button visibility

9. **Dashboard Testing**
   - Verify consultation statistics display
   - Test real-time data updates
   - Verify doctor-specific stats for doctors
   - Test quick action buttons

10. **Workflow Testing**
    - Test complete workflow: Appointment → Queue → Consultation
    - Verify queue status must be "With Doctor" to start consultation
    - Verify auto-completion of queue and appointment
    - Test duplicate prevention

11. **UI Testing**
    - Verify responsive design
    - Test status badge colors
    - Test tabbed interface navigation
    - Verify form validation display
    - Test consultation history table
    - Verify follow-up date picker

## Notes

- All existing functionality remains intact
- No modifications to authentication, layout, or assets
- Uses existing helper functions and middleware
- Follows established MVC architecture
- Consistent with existing code style
- Database migrations are re-run safe
- Permission system integrated with existing roles
- Audit logging integrated with existing audit system
- Queue workflow integrated with existing queue module
- Appointment workflow integrated with existing appointment module
- Dashboard statistics integrated with existing dashboard
- Workflow ensures data consistency across modules

## Migration Files

- `021_consultations.sql` - Creates consultations table
- `022_consultations_fk.sql` - Adds foreign key constraints
- `023_consultation_permissions.sql` - Adds permissions and role assignments

All migrations have been successfully executed.

## Integration Points

### Queue Module Integration
- Consultations can only be created from queue entries with "With Doctor" status
- QueueController redirects to consultation creation
- Prevents duplicate consultations for same queue
- Auto-completes queue when consultation is completed
- Maintains queue workflow integrity

### Appointment Module Integration
- Consultations linked to appointments
- Auto-completes appointment when consultation is completed
- Provides appointment information in consultation view
- Maintains appointment workflow integrity

### Patient Module Integration
- Consultations linked to patients
- Provides patient information in consultation forms
- Shows patient consultation history
- Enables comprehensive patient medical records

### Dashboard Integration
- Consultation statistics displayed on dashboard
- Real-time data updates
- Doctor-specific statistics for doctors
- Quick action button to view consultations

### Permission System Integration
- 5 new permissions added
- Role-based assignments
- Integrated with existing permission middleware
- Button-level permission checks

### Audit System Integration
- All consultation operations logged
- Status changes tracked
- User actions recorded
- Old and new values captured
- Auto-completion events logged

## Workflow Diagram

```
Appointment Created
    ↓
Appointment Status: "Confirmed"
    ↓
Patient Arrives → Appointment Status: "Checked In"
    ↓
Queue Auto-Created (Token: Q-001)
    ↓
Queue Status: "Waiting"
    ↓
Queue Status: "Called"
    ↓
Queue Status: "With Doctor"
    ↓
Start Consultation → Consultation Created (CON-000001)
    ↓
Fill Medical Information
    ↓
Complete Consultation
    ↓
Queue Status: "Completed"
    ↓
Appointment Status: "Completed"
```

## Conclusion

Phase 7 - Doctor Consultation has been successfully implemented. The module provides a comprehensive consultation management system with automatic number generation, medical information recording, patient consultation history, and seamless integration with the queue and appointment workflows. The implementation follows the existing architecture and integrates seamlessly with the appointment, queue, patient, dashboard, permission system, and audit logging. The system ensures proper workflow compliance through status validation and automatic completion of related modules, maintaining data integrity and security through permission-based access control.