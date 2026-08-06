# Phase 6 Report: Appointment Queue Management

## Overview
Phase 6 implements a complete Queue Management module for the Appointment Queue System. This module provides comprehensive queue management functionality including automatic token generation, priority-based ordering, queue status tracking, and doctor-specific queue views. The module integrates seamlessly with the existing Appointment module to automatically create queue entries when appointments are marked as "Checked In".

## Implementation Date
August 6, 2026

## Database Changes

### New Tables Created

#### 1. queue Table (`018_queue.sql`)
- **Purpose**: Store queue entries linked to appointments
- **Fields**:
  - `id` - Primary key
  - `appointment_id` - Foreign key to appointments table (UNIQUE)
  - `doctor_id` - Foreign key to users table
  - `queue_date` - Date of queue entry
  - `token_no` - Token number (Q-001 format)
  - `queue_status` - ENUM: 'Waiting', 'Called', 'With Doctor', 'Completed', 'Skipped', 'Cancelled'
  - `called_at` - Timestamp when patient was called
  - `started_at` - Timestamp when consultation started
  - `completed_at` - Timestamp when consultation completed
  - `remarks` - Additional notes
  - `created_by` - User who created the queue entry
  - `updated_by` - User who last updated the queue entry
  - `created_at` - Creation timestamp
  - `updated_at` - Last update timestamp
- **Indexes**: appointment_id, doctor_id, queue_date, queue_status, token_no, doctor_date_token, doctor_date_status, created_by
- **Foreign Keys**: Added in separate migration (019_queue_fk.sql)

#### 2. Permissions (`020_queue_permissions.sql`)
- **New Permissions Added**:
  - `queue.view` - View queue records
  - `queue.manage` - Manage queue operations
  - `queue.call` - Call patients from queue
  - `queue.complete` - Complete queue entries
  - `queue.skip` - Skip queue entries
  - `queue.cancel` - Cancel queue entries
- **Role Assignments**:
  - Admin: All queue permissions
  - Doctor: view, manage, call, complete, skip
  - Receptionist: view, manage, call, skip

## Files Created

### Models
1. **app/models/Queue.php** (418 lines)
   - `generateTokenNo($doctorId, $queueDate)` - Auto-generates token numbers (Q-001 format)
   - `createFromAppointment($appointmentId)` - Creates queue entry from appointment
   - `getAllQueue($doctorId, $queueDate)` - Get all queue entries with relations
   - `getQueueByStatus($status, $doctorId)` - Get queue entries by status
   - `getWaitingQueue($doctorId)` - Get waiting patients
   - `getCalledQueue($doctorId)` - Get called patients
   - `getWithDoctorQueue($doctorId)` - Get patients with doctor
   - `getTodayQueue($doctorId)` - Get today's queue
   - `searchQueue($searchTerm, $filters)` - Search with filters
   - `isValidStatusTransition($currentStatus, $newStatus)` - Validate status transitions
   - `updateStatus($queueId, $newStatus)` - Update queue status with timestamps
   - `callNext($doctorId)` - Call next patient in queue
   - `callSpecific($queueId)` - Call specific patient
   - `startConsultation($queueId)` - Start consultation
   - `completeQueue($queueId)` - Complete queue entry
   - `skipQueue($queueId)` - Skip queue entry
   - `cancelQueue($queueId)` - Cancel queue entry
   - `getQueueWithDetails($queueId)` - Get queue with full details
   - `getQueueStats($doctorId)` - Get queue statistics
   - `getWaitingCount($doctorId)` - Get waiting count
   - `getWithDoctorCount($doctorId)` - Get with doctor count
   - `getCompletedTodayCount($doctorId)` - Get completed today count
   - `getSkippedTodayCount($doctorId)` - Get skipped today count
   - `findByAppointmentId($appointmentId)` - Find queue by appointment
   - `checkQueueExists($appointmentId)` - Check if queue exists for appointment
   - `count($where)` - Count queue entries

### Controllers
2. **app/controllers/QueueController.php** (378 lines)
   - `index()` - List queue with search and filters
   - `callNext()` - Call next patient in queue
   - `callSpecific($id)` - Call specific patient
   - `startConsultation($id)` - Start consultation
   - `completeQueue($id)` - Complete queue entry
   - `skipQueue($id)` - Skip queue entry
   - `cancelQueue($id)` - Cancel queue entry
   - `show($id)` - Display queue details
   - `getDoctorIdForCurrentUser()` - Get doctor ID for current user (private)
   - Features:
     - Doctor-specific queue views
     - Super admin sees all queues
     - Permission checks on all methods
     - Audit logging for all operations
     - Status transition validation
     - Priority-based calling logic

### Views
3. **app/views/queue/index.php** (283 lines)
   - Queue list with search functionality
   - Dashboard stats cards (Waiting, With Doctor, Completed Today, Skipped Today)
   - Filter by doctor, date, status, priority
   - Status badges with color coding
   - Priority badges with color coding
   - Action buttons based on permissions and status
   - "Call Next" button with doctor selection
   - Responsive Bootstrap 5 table
   - Token number display

4. **app/views/queue/show.php** (288 lines)
   - Detailed queue view
   - Complete queue information
   - Patient contact information
   - Queue timeline (Created, Called, Started, Completed)
   - Status display with color coding
   - Action buttons based on permissions and status
   - System information (created/updated by)
   - Back navigation
   - Custom timeline styling

### Database Migrations
5. **database/migrations/018_queue.sql** (25 lines)
   - Queue table creation
   - All field definitions
   - Indexes for performance
   - Engine and charset settings

6. **database/migrations/019_queue_fk.sql** (16 lines)
   - Foreign key constraints
   - Cascade delete/update rules
   - Appointment, doctor, created_by, updated_by relationships

7. **database/migrations/020_queue_permissions.sql** (32 lines)
   - Permission definitions
   - Role-permission assignments
   - Insert with IGNORE for re-run safety

## Files Modified

### Controllers
1. **app/controllers/AppointmentController.php**
   - Added Queue model import
   - Modified `updateStatus()` method to auto-create queue entry when status is "Checked In"
   - Queue creation with audit logging
   - Prevents duplicate queue entries

2. **app/controllers/DashboardController.php**
   - Added Queue, Patient, and Appointment model imports
   - Modified `index()` method to get queue statistics
   - Doctor-specific queue stats
   - Added stats to view data

### Core Model
3. **app/core/Model.php**
   - Added `count($where)` method for counting records
   - Used by Patient, Appointment, and Queue models

### Models
4. **app/models/Patient.php**
   - Added `count($where)` method

5. **app/models/Appointment.php**
   - Added `count($where)` method

6. **app/models/Queue.php**
   - Added `count($where)` method

### Routes
7. **routes/web.php**
   - Added queue management routes with permission middleware:
     - GET `/queue` - List queue
     - POST `/queue/call-next` - Call next patient
     - GET `/queue/call/{id}` - Call specific patient
     - GET `/queue/start/{id}` - Start consultation
     - GET `/queue/complete/{id}` - Complete queue
     - GET `/queue/skip/{id}` - Skip queue
     - GET `/queue/cancel/{id}` - Cancel queue
     - GET `/queue/show/{id}` - View queue details

### Sidebar
8. **app/views/partials/sidebar.php**
   - Updated Queue menu item to link to actual route
   - Added active state detection for queue pages
   - Permission check for display

### Dashboard View
9. **app/views/dashboard/index.php**
   - Updated stats cards with real data
   - Added queue-specific stats row (Completed Today, Skipped Today, Called)
   - Made quick action buttons functional
   - Added "View Queue" quick action button

## Key Features Implemented

### 1. Token Number Generation
- Automatic generation in Q-001 format
- Per doctor, per day numbering
- Starts from Q-001 each day for each doctor
- Unique constraint in database
- Format: Q-XXX (3-digit zero-padded number)

### 2. Queue Auto-Creation
- Automatically creates queue entry when appointment status is "Checked In"
- Prevents duplicate queue entries
- Generates token automatically
- Links to appointment and doctor
- Audit logging for queue creation

### 3. Status Management
- 6 status options:
  - Waiting (Yellow badge)
  - Called (Blue badge)
  - With Doctor (Green badge)
  - Completed (Primary badge)
  - Skipped (Secondary badge)
  - Cancelled (Danger badge)
- Status transition validation
- Automatic timestamp updates:
  - called_at when status becomes "Called"
  - started_at when status becomes "With Doctor"
  - completed_at when status becomes "Completed"

### 4. Status Transition Validation
- Valid transitions:
  - Waiting → Called, Cancelled
  - Called → With Doctor, Skipped, Cancelled
  - With Doctor → Completed, Cancelled
  - Completed → (no transitions)
  - Skipped → Called, Cancelled
  - Cancelled → (no transitions)
- Prevents invalid status changes
- Error messages for invalid transitions

### 5. Priority-Based Ordering
- Queue sorted by priority first:
  - Emergency (1st)
  - Urgent (2nd)
  - Normal (3rd)
- Within same priority, sorted by token number
- Ensures critical patients are seen first
- Consistent ordering across all views

### 6. Queue Actions
- **Call Next**: Calls next waiting patient based on priority and token
- **Call Specific**: Calls a specific patient from waiting list
- **Start Consultation**: Moves patient from "Called" to "With Doctor"
- **Complete**: Marks consultation as completed
- **Skip**: Skips patient (can be re-called later)
- **Cancel**: Cancels queue entry
- All actions audit logged
- Permission-based access control

### 7. Doctor-Specific Views
- Doctors see only their own queue
- Super admin sees all queues
- Receptionist can see all queues with limited actions
- Role-based access control
- Automatic doctor ID detection

### 8. Search and Filtering
- Search by:
  - Token number
  - Appointment number
  - Patient name
  - Patient code
  - Doctor name
- Filter by:
  - Doctor
  - Date
  - Status
  - Priority
- Combined search and filter support

### 9. Dashboard Statistics
- Patients Waiting count
- With Doctor count
- Completed Today count
- Skipped Today count
- Called count
- Real-time data
- Doctor-specific stats for doctors

### 10. Queue Timeline
- Visual timeline of queue progress
- Shows:
  - Queue Created timestamp
  - Patient Called timestamp
  - Consultation Started timestamp
  - Completed timestamp
- Color-coded markers
- Professional timeline display

### 11. Permission-Based Access Control
- 6 queue-specific permissions
- Route-level permission checks
- View-level permission checks
- Button-level permission checks
- Role-based assignments:
  - Admin: Full access
  - Doctor: view, manage, call, complete, skip
  - Receptionist: view, manage, call, skip

### 12. Audit Logging
- Logs all queue operations:
  - queue_created
  - patient_called
  - consultation_started
  - queue_completed
  - queue_skipped
  - queue_cancelled
- Records old and new values
- Tracks user who performed action

## Routes Added

| Method | Path | Controller Method | Permission Required |
|--------|------|-------------------|---------------------|
| GET | /queue | index | queue.view |
| POST | /queue/call-next | callNext | queue.call |
| GET | /queue/call/{id} | callSpecific | queue.call |
| GET | /queue/start/{id} | startConsultation | queue.manage |
| GET | /queue/complete/{id} | completeQueue | queue.complete |
| GET | /queue/skip/{id} | skipQueue | queue.skip |
| GET | /queue/cancel/{id} | cancelQueue | queue.cancel |
| GET | /queue/show/{id} | show | queue.view |

## Database Tables

### queue
- Primary Key: id
- Foreign Keys:
  - appointment_id → appointments.id (CASCADE, UNIQUE)
  - doctor_id → users.id (CASCADE)
  - created_by → users.id (SET NULL)
  - updated_by → users.id (SET NULL)
- Timestamps: created_at, updated_at
- Status Timestamps: called_at, started_at, completed_at

### permissions (Updated)
- Added 6 new queue permissions
- Assigned to admin, doctor, receptionist roles

### role_permissions (Updated)
- Added queue permissions to role-permission mappings

## Completed Features

✅ Database schema for queue
✅ Queue model with all methods
✅ QueueController with queue management operations
✅ Queue list view with search and filters
✅ Queue detail view with timeline
✅ Automatic token generation (Q-001 format)
✅ Token generation per doctor per day
✅ Auto-create queue on appointment "Checked In"
✅ Permission-based access control
✅ Audit logging for all operations
✅ Status management with color coding
✅ Status transition validation
✅ Priority-based queue ordering
✅ Queue actions (Call, Start, Complete, Skip, Cancel)
✅ Doctor-specific queue views
✅ Search and filtering capabilities
✅ Dashboard statistics widgets
✅ Queue timeline display
✅ Form validation and error handling
✅ Responsive Bootstrap 5 UI
✅ Route protection with middleware
✅ Sidebar integration
✅ Dashboard integration
✅ Database migrations
✅ Model count methods for statistics

## Pending Features

❌ Pagination for queue list
❌ Real-time queue updates (WebSocket)
❌ Queue sound notification on call
❌ SMS notification for queue calls
❌ Display screen for waiting area
❌ Voice announcement system
❌ Queue estimation time
❌ Bulk queue operations
❌ Queue history and analytics
❌ Queue performance reports
❌ Patient feedback collection
❌ Queue notes and attachments
❌ Queue transfer between doctors
❌ Emergency queue override
❌ Queue management from mobile app
❌ Queue statistics by time period
❌ Doctor performance metrics
❌ Average wait time calculation
❌ Peak hours analysis

## Testing Status

The application has been deployed and is accessible at http://localhost:8080. The following should be tested:

1. **Queue Creation**
   - Create appointment and mark as "Checked In"
   - Verify automatic queue entry creation
   - Verify token generation (Q-001 format)
   - Test duplicate queue prevention

2. **Queue Viewing**
   - View queue list
   - Test search functionality
   - Test filters (doctor, date, status, priority)
   - View queue details
   - Test queue timeline display

3. **Queue Actions**
   - Call next patient
   - Call specific patient
   - Start consultation
   - Complete consultation
   - Skip patient
   - Cancel queue entry
   - Test status transitions

4. **Priority Ordering**
   - Create patients with different priorities
   - Verify Emergency patients appear first
   - Verify Urgent patients appear second
   - Verify Normal patients appear last
   - Test token ordering within same priority

5. **Doctor-Specific Views**
   - Login as doctor user
   - Verify only own queue is visible
   - Test queue actions
   - Login as admin
   - Verify all queues are visible

6. **Permission Testing**
   - Test admin access (full permissions)
   - Test doctor access (view, manage, call, complete, skip)
   - Test receptionist access (view, manage, call, skip)
   - Verify permission-based button visibility

7. **Dashboard Testing**
   - Verify queue statistics display
   - Test real-time data updates
   - Verify doctor-specific stats for doctors
   - Test quick action buttons

8. **UI Testing**
   - Verify responsive design
   - Test status badge colors
   - Test priority badge colors
   - Verify form validation display
   - Test navigation
   - Verify timeline display

## Notes

- All existing functionality remains intact
- No modifications to authentication, layout, or assets
- Uses existing helper functions and middleware
- Follows established MVC architecture
- Consistent with existing code style
- Database migrations are re-run safe
- Permission system integrated with existing roles
- Audit logging integrated with existing audit system
- Queue creation integrated with appointment module
- Dashboard statistics integrated with existing dashboard
- Model count method added to base Model class for reuse

## Migration Files

- `018_queue.sql` - Creates queue table
- `019_queue_fk.sql` - Adds foreign key constraints
- `020_queue_permissions.sql` - Adds permissions and role assignments

All migrations have been successfully executed.

## Integration Points

### Appointment Module Integration
- Queue entries automatically created when appointment status becomes "Checked In"
- Prevents duplicate queue entries
- Links queue to appointment via appointment_id
- Syncs doctor and date from appointment

### Dashboard Integration
- Queue statistics displayed on dashboard
- Real-time data updates
- Doctor-specific statistics for doctors
- Quick action button to view queue

### Permission System Integration
- 6 new permissions added
- Role-based assignments
- Integrated with existing permission middleware
- Button-level permission checks

### Audit System Integration
- All queue operations logged
- Status changes tracked
- User actions recorded
- Old and new values captured

## Conclusion

Phase 6 - Appointment Queue Management has been successfully implemented. The module provides a comprehensive queue management system with automatic token generation, priority-based ordering, status tracking, and doctor-specific views. The implementation follows the existing architecture and integrates seamlessly with the appointment module, dashboard, permission system, and audit logging. The system ensures efficient patient flow management while maintaining data integrity and security through permission-based access control.