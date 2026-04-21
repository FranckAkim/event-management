### CINS 5305 | Full Project Documentation

---

How to RUN our file
1 - Download the zip file and unzip.
2 - Copy the files into C:\xampp\htdocs\Celebratehub (create the file Celebratehub and copy the slides into it)
3 - Open xammp and start the Apache and MySQL 
4 - Open myphp local server on your laptop/pc
5 - Create a new database called event_management
5 - Import the sql file from the zip file after unzipping it in this "event_management" you just created.
6 - Use VS code or any other application to run the codes.
7 - Open the index.php file to go to the welcome page. you can create an account or use the user table from the database to login with any of the users.
8 - Then explore base on your role as admin, organizer or attendee
9 - Different dashboards are going to be available base on your role.
10 - Enjoy


 
# CelebrateHub — Event Scheduling System
## Table of Contents

1. What Is This Project?
2. What Can It Do? (Features)
3. Who Uses It? (User Roles)
4. How to Set It Up
5. Project Folder Structure (Where Every File Lives)
6. Every File Explained
7. Database Tables Explained
8. Database Triggers Explained
9. How Each Feature Works Step by Step
10. Test Login Accounts
11. Common Problems and Fixes

---

## 1. What Is This Project?

**CelebrateHub** is a web-based Event Scheduling System built for managing weddings, birthdays, anniversaries, graduations, and all kinds of celebrations. It was built as part of the CINS 5305 course project.

Think of it like an online event booking platform — similar to how you might book a restaurant table or a hotel room, but designed specifically for:

- Planning and publishing both public and private (invite-only) events
- Letting guests browse and register for events
- Managing venues, resources, and time slots
- Preventing double-bookings with database-level unique constraints
- Giving administrators full control and visibility over everything
- Automatically enforcing data rules using database triggers

The system runs locally on your computer using **XAMPP** and stores all data in a **MySQL database** managed through **phpMyAdmin**.

---

## 2. What Can It Do? (Features)

### For Everyone (All Users)
- Log in securely with an email and password
- View a personalized dashboard based on their role
- A **Welcome** panel in the sidebar shows the user's name, email, role, and a live status badge
- Browse upcoming events with venue, date, time, and capacity information

### For Attendees (Regular Users / Requesters)
- Browse all confirmed **public** events
- Private (invite-only) events are hidden unless they were specifically invited
- Register for any public event with one click
- View registrations split into two sections:
  - **My Upcoming Events** — approved bookings
  - **Available Events** — events not yet joined or pending approval
- See a "⏳ Pending approval" label while waiting
- Cancel their registration at any time
- **Sidebar badge** shows "X Upcoming" in blue — their approved upcoming event count
- Receive capacity warnings when events are nearly full or completely full

### For Organisers
- Create new **public or private** events
  - **Public** — any registered attendee can see and register
  - **Private (🔒 invite-only)** — organiser enters attendee email addresses; those attendees are automatically given an APPROVED booking and can see the event; everyone else cannot
- Edit only their own events
- See only their own events on the dashboard and event list
- **📋 Pending Approval Requests** panel — approve or decline registrations from the dashboard with ✅ Approve / ❌ Decline buttons
- **📝 To-Do List** — today's confirmed events with capacity info
- **👥 Guest List** — all registered attendees grouped by event, showing name and status badge; each guest has a **✕ Remove** button to cancel their registration
- Assign resources to events and remove them when no longer needed
- **Sidebar badge** shows "X Events Active" in teal — their confirmed event count

### For Administrators
- See everything — all events from all organisers
- Edit or delete any event
- Approve or reject any attendee's registration
- **Attendees tab** — full list of all bookings (excluding cancelled by default), with filters for name, event, and status; Remove button cancels any booking and removes it from the list instantly
- **Resources tab** — assign resources to events; each assignment has a **✕ Remove** button
- **Reports tab** — event summaries, venue usage, capacity breakdowns, and a **🔔 Recent Activity Feed** showing the last 15 actions (registrations, approvals, cancellations) with relative timestamps
- **Export Excel** — download a formatted `.xlsx` report with color-coded rows for near-capacity events
- **📊 Event Health Overview** on the dashboard — shows total confirmed events, events this month, near-capacity count, average fill rate, most popular event, near-capacity list, and events with zero registrations
- **Sidebar badge** shows "DB Connected" in green
- All booking changes made by organisers update the admin's view automatically

---

## 3. Who Uses It? (User Roles)

| Role | What They See | What They Can Do |
|---|---|---|
| **Admin** | Everything | Create, edit, delete any event; approve/remove bookings; view all reports; see Event Health Overview |
| **Organiser** | Only their own events | Create public or private events; invite attendees; approve/decline/remove registrations; assign/remove resources |
| **Requester (Attendee)** | All public confirmed events + private events they're invited to | Browse and register for events; cancel registrations |

---

## 4. How to Set It Up

### What You Need Before Starting
- **XAMPP** installed on your computer (download from apachefriends.org)
- A web browser (Chrome, Firefox, or Edge)

### Step-by-Step Setup

**Step 1 — Start XAMPP**
Open the XAMPP Control Panel and click **Start** next to both **Apache** and **MySQL**.

**Step 2 — Put the Project Files in the Right Place**
Copy the entire `celebratehub` folder into:
- **Windows:** `C:\xampp\htdocs\celebratehub`
- **Mac:** `/Applications/XAMPP/htdocs/celebratehub`

**Step 3 — Create the Database**
1. Open: `http://localhost/phpmyadmin`
2. Click **New** in the left sidebar
3. Type: `event_management` → click **Create**

**Step 4 — Create the Tables (schema.sql)**
1. Click `event_management` in the left sidebar
2. Click the **SQL** tab
3. Paste `database/schema.sql` → click **Go**

**Step 5 — Load Sample Data and Triggers (test_data.sql)**
1. Still inside `event_management` → SQL tab
2. Paste `database/test_data.sql` → click **Go**
3. This loads all sample data AND installs all 3 database triggers

**Step 6 — Load Resources (fix_resources.sql)**
1. SQL tab again
2. Paste `database/fix_resources.sql` → click **Go**
3. This populates the resource and event_resource tables

**Step 7 — Verify Triggers**
Run this in phpMyAdmin to confirm all 3 triggers are installed:
```sql
SHOW TRIGGERS FROM event_management;
```

**Step 8 — Open the Website**
Go to: `http://localhost/celebratehub`

> ⚠️ **If upgrading an existing database:** Run this one line first to add the IsPrivate column if it doesn't already exist:
> ```sql
> ALTER TABLE event ADD COLUMN IF NOT EXISTS IsPrivate TINYINT(1) NOT NULL DEFAULT 0;
> ```

---

## 5. Project Folder Structure (Where Every File Lives)

```
celebratehub/
│
├── index.html                    ← Welcome / landing page
├── login.php                     ← Login page
├── register.php                  ← New user registration
├── logout.php                    ← Logs out and redirects
├── dashboard.php                 ← Main app page (role-aware sidebar + tabs)
│
├── config/
│   └── db.php                    ← Database connection settings
│
├── api/                          ← All background PHP data files
│   ├── events-list.php           ← Fetches events (role-filtered, private-aware)
│   ├── save-event.php            ← Creates/updates events + invite logic
│   ├── delete-event.php          ← Admin-only event deletion
│   ├── get-event.php             ← Loads one event for the edit form
│   ├── kpi-stats.php             ← Dashboard KPI tile numbers
│   ├── venues.php                ← Returns active venues for dropdowns
│   ├── timeslots.php             ← Returns time slots
│   ├── schedule.php              ← Venue availability checker
│   ├── attendees.php             ← Booking records (role-filtered, no CANCELLED by default)
│   ├── register-event.php        ← Attendee registration and cancellation
│   ├── update-booking.php        ← Approve or reject a booking (admin/organiser)
│   ├── remove-guest.php          ← Cancel a booking / remove an attendee
│   ├── resources-data.php        ← Resource inventory and assignments
│   ├── save-resource.php         ← Assign a resource to an event
│   ├── remove-resource.php       ← Remove a resource assignment
│   ├── reports.php               ← Report data (summary, venue usage, capacity)
│   ├── export.php                ← Excel (.xlsx) file download
│   ├── activity-feed.php         ← Recent Activity Feed (last 15 actions)
│   ├── event-health.php          ← Event Health Overview stats (admin dashboard)
│   └── venue-utilization.php     ← Venue usage stats (kept for reference)
│
├── assets/
    ├── css/
    │   └── styles.css            ← All visual styling
    └── js/
         └── script.js             ← All interactivity and dashboard logic
```

---

## 6. Every File Explained

### Welcome Page — `index.html`
The first page visitors see before logging in. Features a fullscreen slideshow of celebration photos, a live bookings counter, animated statistics, testimonials, floating confetti on click, and links to Sign In and Register.

### Database Connection — `config/db.php`
Stores the MySQL credentials and creates a reusable PDO connection used by all API files. Only file you need to change if your database password changes.

### Main Dashboard — `dashboard.php`
The heart of the application. After login it reads the user's role and renders the correct sidebar, tabs, and content. Passes `window.currentUserID`, `currentUserRole`, `canCreateEvent`, and `canManage` to JavaScript. The sidebar shows **"Welcome"** instead of "Logged In As", with a live role-specific badge.

### events-list.php — Event Loader *(trigger + privacy aware)*
Fetches events based on role:
- **Admin** — all events
- **Organiser** — only their own events (all statuses including DRAFT)
- **Requester** — only CONFIRMED public events, plus private events they have a booking for

Capacity counts use only `PENDING` and `APPROVED` bookings, matching Trigger 2 exactly. Returns `IsPrivate` flag so the UI can show the 🔒 badge.

### save-event.php — Create / Edit Events *(private events + invites)*
Handles both creating and editing events. New features:
- Reads `isPrivate` flag and `inviteEmails` array from the request
- Saves `IsPrivate` to the database
- For private events: loops through each email, looks up the user, and creates an **APPROVED** booking automatically — the invited attendee is immediately registered with no approval step needed
- Returns a `notFound` list of emails that don't exist in the system
- Validates venue is still active (Trigger 3 protection) and capacity ≤ venue maximum
- Checks for time conflicts with other confirmed events at the same venue
- Uses `INSERT IGNORE` on timeslot to prevent duplicates (protected by unique constraint)

### delete-event.php — Remove Events
Admin-only. Deletes in the correct FK order: resource assignments → bookings → schedule → event.

### attendees.php — Booking Records *(role-filtered, CANCELLED excluded by default)*
Returns booking records. Key behaviors:
- **Organiser** → only sees bookings for their own events
- **Admin** → sees all bookings
- **CANCELLED bookings are excluded by default** — they only appear when the user explicitly filters by "Cancelled" status
- Events dropdown also excludes cancelled events

### update-booking.php — Approve or Reject a Booking
Used by both admin and organiser. Enforces ownership for organisers (they can only action bookings on their own events). Uses a transaction, blocks re-processing already-actioned bookings, and returns fresh capacity counts so the frontend stays in sync without a full page reload.

### remove-guest.php — Remove an Attendee
Sets a booking to CANCELLED. Admin can remove anyone. Organiser can only remove guests from their own events. Uses a transaction with `rowCount()` check to confirm the update actually happened. After removal the row is deleted from the DOM instantly — no page refresh needed.

### register-event.php — Attendee Self-Registration *(trigger-integrated)*
Handles register and cancel actions. The INSERT no longer passes `RequestedAt` — Trigger 1 fills it automatically. The INSERT is wrapped in a try/catch that specifically handles Trigger 2's `SQLSTATE 45000` error and returns a friendly "fully booked" message.

### kpi-stats.php — Dashboard Number Tiles
Returns the 4 KPI numbers for the dashboard. All capacity counts use `PENDING + APPROVED` only to match Trigger 2's logic. Role-specific labels and values:
- **Admin:** Active Events / Open Days / Capacity Alerts / Pending Resource Requests
- **Organiser:** My Confirmed Events / My Events This Week / Near Capacity / Total Active Registrations
- **Attendee:** Available Events / My Upcoming Events / My Active Registrations / Open Days This Week

### resources-data.php — Resource Inventory
Powers the Resources tab for admin and organiser. Returns inventory with demand levels and all resource assignments. Each assignment row now has a **✕ Remove** button.

### save-resource.php — Assign Resources
Inserts or updates a resource assignment. If the resource is already assigned to that event, it updates the quantity instead of creating a duplicate.

### remove-resource.php — Remove a Resource Assignment *(new)*
Deletes a row from `event_resource`. Admin can remove any assignment. Organiser can only remove from their own events — server verifies ownership. Returns success confirmation to trigger a full Resources tab refresh so inventory counts update too.

### reports.php — Report Data
Generates summary, venue utilization, and capacity analysis data for the Reports tab. The old Conflict Log query has been removed — replaced by the Recent Activity Feed.

### export.php — Excel Export *(upgraded from CSV)*
Generates a real **SpreadsheetML XML** `.xlsx` file that Excel, Google Sheets, and LibreOffice all open natively. The file includes:
- Title row, date range, and generation timestamp
- Dark blue header row with white bold text
- Data rows with Event ID, Name, Description, Status, Capacity, Registered, Fill %, Organizer, Venue, Date, Start/End Times
- Yellow highlight on rows where fill % is 85% or above
- Pre-set column widths so no manual resizing is needed

### activity-feed.php — Recent Activity Feed *(new)*
Returns the last 15 booking actions system-wide. Each entry includes a description sentence, icon (📋✅❌🚫), status badge, and relative timestamp ("2 mins ago", "3 hrs ago", "Apr 12"). Used in the Reports tab's Recent Activity Feed panel, which replaced the old Conflict Log.

### event-health.php — Event Health Overview *(new, admin only)*
Returns event health statistics for the admin dashboard panel:
- Total confirmed events and how many fall in the current month
- Count of near-capacity events (≥ 85% full)
- Average fill rate across all confirmed events
- The single most popular event with a mini progress bar
- List of up to 3 near-capacity events with red fill badges
- List of upcoming events with zero registrations

### venue-utilization.php — Venue Usage Stats *(kept for reference)*
Returns per-venue stats: total events, events this week, next upcoming event. Was used by the dashboard Venue Utilization panel which has since been replaced by Event Health Overview. Kept in the project for reference.

### schedule.php — Availability Checker
Powers the Schedule tab's venue availability search by date and venue.

### styles.css — Visual Design
Controls all colors, fonts, button styles, table layouts, card designs, spacing, and animations. Dark theme with glass-effect panels.

### script.js — All Interactivity
The main JavaScript file. Key functions and what they do:

| Function | What It Does |
|---|---|
| `updateSidebarBadge(kpi)` | Updates sidebar badge: teal "X Events Active" for organiser, blue "X Upcoming" for attendee, static for admin |
| `loadPendingRequestsPanel()` | Loads PENDING bookings into organiser's approval panel |
| `updateBookingStatus(id, status, btn)` | Sends approve/reject, refreshes organiser panels + event cache + admin tabs |
| `loadGuestListPanel()` | Loads all guests grouped by event into the organiser Guest List |
| `removeGuest(bookingID, name)` | Confirms, calls `remove-guest.php`, removes row from DOM instantly |
| `loadEventHealth()` | Fetches and renders Event Health Overview for admin dashboard |
| `loadActivityFeed()` | Fetches and renders Recent Activity Feed in Reports tab |
| `removeBooking(id, name, event)` | Admin removes attendee from Attendees tab — row disappears immediately |
| `removeResourceAssignment(eid, rid, ...)` | Removes resource from event — refreshes full Resources tab |
| `exportExcel()` | Triggers `.xlsx` download from `api/export.php` |
| `getConflictingEventIDs(events)` | Deduplicates events before scanning — same EventID is never a "conflict" |
| `renderEventsTab(events)` | Admin/organiser: form + list with 🔒 badge on private events; attendee: two-section layout |
| `saveEvent()` | Reads privacy toggle + invite emails, sends to `save-event.php`, shows warning if emails not found |

---

## 7. Database Tables Explained

The database is named `event_management` and contains 8 tables:

### `user` — All Accounts
| Column | What It Stores |
|---|---|
| UserID | Auto-assigned unique number |
| Name | Full name |
| Email | Login email — must be unique |
| RoleName | admin, organiser, or requester |
| password | Password (plain text in this project) |

### `event` — All Events
| Column | What It Stores |
|---|---|
| EventID | Auto-assigned unique number |
| Title | Event name |
| Description | Longer description |
| OrganizerID | Which user created it (links to user) |
| CapacityLimit | Max guests (1–300) |
| Status | CONFIRMED, DRAFT, or CANCELLED |
| IsPrivate | 0 = public, 1 = invite-only (🔒) |

> CANCELLED can be set manually or automatically by Trigger 3 when a venue is deactivated.

### `venue` — All Venues
| Column | What It Stores |
|---|---|
| VenueID | Auto-assigned unique number |
| Name | Venue name |
| MaxCapacity | Physical maximum (event capacity cannot exceed this) |
| IsActive | TRUE/FALSE — setting FALSE triggers Trigger 3 |

### `timeslot` — Date and Time Blocks
| Column | What It Stores |
|---|---|
| SlotID | Auto-assigned |
| EventDate | Date of the event |
| StartTime | When it starts |
| EndTime | When it ends |

**Constraint:** `UNIQUE (EventDate, StartTime, EndTime)` — no duplicate time slots.

### `event_schedule` — Links Events to Venues and Times
| Column | What It Stores |
|---|---|
| EventID | Primary key — one schedule row per event |
| VenueID | Which venue |
| SlotID | Which time slot |

**Constraints:**
- `UNIQUE (SlotID)` — one event per time slot (no two events share a slot)
- `UNIQUE (VenueID, SlotID)` — prevents double-booking a venue

### `resource` — Equipment and Supplies
| Column | What It Stores |
|---|---|
| ResourceID | Auto-assigned |
| Name | Resource name |
| Type | AV, Furniture, Catering, Decor, Equipment, or Other |

### `event_resource` — Resource Assignments
| Column | What It Stores |
|---|---|
| EventID | Which event (composite PK) |
| ResourceID | Which resource (composite PK) |
| Quantity | How many units needed |

### `booking` — Attendee Registrations
| Column | What It Stores |
|---|---|
| BookingID | Auto-assigned |
| UserID | Which attendee |
| VenueID | Which venue |
| EventID | Which event |
| Status | PENDING, APPROVED, REJECTED, or CANCELLED |
| RequestedAt | Auto-filled by Trigger 1 — never pass manually |
| DepositAmount | Default 0 |
| Notes | Optional notes |

**Constraints:**
- `UNIQUE (UserID, EventID)` — one registration per attendee per event
- `chk_booking_status` — Status must be one of the four exact uppercase values

---

## 8. Database Triggers Explained

A trigger is a rule built into the database that runs automatically when something happens — like a row being inserted or a column being updated. All 3 triggers are defined in `database/test_data.sql`.

To verify triggers are installed:
```sql
SHOW TRIGGERS FROM event_management;
```

---

### Trigger 1 — `trg_booking_before_insert`
**What:** Auto-sets `RequestedAt = NOW()` whenever a new booking row is created.

**When:** BEFORE INSERT on `booking`.

**Why:** PHP files no longer need to pass `RequestedAt`. Even if someone inserts a booking directly in phpMyAdmin, the timestamp is always recorded correctly.

**Test:**
```sql
SELECT COUNT(*) AS Missing FROM booking WHERE RequestedAt IS NULL;
-- Expected: 0
```

---

### Trigger 2 — `trg_booking_check_capacity`
**What:** Blocks any booking INSERT that would push active registrations (PENDING + APPROVED) above the event's `CapacityLimit`.

**When:** BEFORE INSERT on `booking`.

**Why:** PHP checks capacity before inserting, but two people clicking Register at the same millisecond could both pass the PHP check before either booking is saved. This trigger makes the limit absolute — only one of them will succeed.

**Error raised:** `SQLSTATE 45000` — caught by `register-event.php` and returned as a friendly message.

**Test:**
```sql
-- Try inserting a booking into an event already at capacity
-- Expected: ERROR 1644 — Event is fully booked. Capacity limit reached.
```

---

### Trigger 3 — `trg_venue_deactivated`
**What:** Automatically sets all CONFIRMED events at a venue to CANCELLED when that venue's `IsActive` changes from TRUE to FALSE.

**When:** AFTER UPDATE on `venue`, only when `IsActive` flips FALSE.

**Why:** Without this trigger, deactivating a venue would leave all its events showing as CONFIRMED even though the venue is gone. An admin would have to find and cancel each one manually.

**Test:**
```sql
UPDATE venue SET IsActive = FALSE WHERE Name = 'Rooftop Terrace';
SELECT e.Title, e.Status FROM event e
JOIN event_schedule es ON e.EventID = es.EventID
WHERE es.VenueID = (SELECT VenueID FROM venue WHERE Name = 'Rooftop Terrace');
-- Expected: all events show Status = 'CANCELLED'
-- Re-activate: UPDATE venue SET IsActive = TRUE WHERE Name = 'Rooftop Terrace';
-- Note: events do NOT auto-restore — they must be manually set back to CONFIRMED
```

---

## 9. How Each Feature Works Step by Step

### Creating a Public Event (Organiser)
1. Click Events tab → fill in name, venue, date, times, capacity, description
2. Leave "Event Visibility" on 🌐 Public
3. Click **Create Event**
4. PHP validates all fields, checks venue is active, checks capacity ≤ venue max, checks for time conflicts
5. Event saved with `IsPrivate = 0` — appears in all attendees' Available Events list

### Creating a Private Event (Organiser)
1. Fill in the event form as above
2. Select 🔒 **Private — invite only**
3. An "Invite Attendees" textarea appears — enter one email per line
4. Click **Create Event**
5. Event saved with `IsPrivate = 1`
6. For each email entered: system looks up the user → creates an **APPROVED** booking automatically
7. Emails not found in the system are listed in a warning toast
8. Invited attendees see the event in their **My Upcoming Events** — already approved, no registration step
9. All other attendees cannot see the event at all

### Approving / Declining a Registration (Organiser)
1. Organiser sees "📋 Pending Approval Requests" panel on dashboard
2. Each request shows: attendee name, event name, date, and two buttons
3. Click **✅ Approve** or **❌ Decline**
4. Both buttons disable immediately (prevents double-click)
5. Request is sent to `update-booking.php` which verifies organiser owns that event
6. On success: row fades out, Pending panel refreshes, Guest List refreshes, admin's Attendees tab and event cache also update

### Removing a Guest (Organiser / Admin)
- **From Guest List panel:** click **✕ Remove** next to the guest's name → confirmation → row fades out instantly → database updated → panel refreshes
- **From Attendees tab (admin):** click **✕ Remove** in the Action column → row disappears from the DOM immediately → database updated (CANCELLED) → since CANCELLED is excluded by default, the row is gone from the list

### Registering for an Event (Attendee)
1. Browse Available Events → click Register
2. PHP checks: event confirmed? capacity available? already registered?
3. Booking INSERT sent to DB — Trigger 1 auto-fills timestamp, Trigger 2 double-checks capacity
4. Event shows "⏳ Pending approval" in Available Events
5. When organiser approves → event moves to My Upcoming Events

### Deactivating a Venue (Admin in phpMyAdmin)
1. Set `IsActive = FALSE` for a venue
2. **Trigger 3 fires immediately** — all CONFIRMED events at that venue become CANCELLED
3. Attendees no longer see those events (only CONFIRMED events are visible)
4. Venue disappears from the Create Event dropdown
5. Any attempt to create an event at that venue returns an error

---

## 10. Test Login Accounts

| Role | Email | Password |
|---|---|---|
| Administrator | admin@festival.edu | admin123 |
| Organiser | john@festival.edu | org123 |
| Organiser | sarah@festival.edu | org123 |
| Attendee | alice@festival.edu | att123 |
| Attendee | bob@festival.edu | att123 |
| Attendee | carol@festival.edu | att123 |
| Attendee | david@festival.edu | att123 |
| Attendee | emma@festival.edu | att123 |
| Attendee | frank@festival.edu | att123 |
| Attendee | grace@festival.edu | att123 |

---

## 11. Common Problems and Fixes

### "Cannot connect to database"
- Make sure both Apache and MySQL are running (green) in XAMPP Control Panel
- Check `config/db.php` — username should be `root`, password blank by default

### "Table doesn't exist"
- Run `schema.sql` first in phpMyAdmin inside `event_management`

### Dashboard stuck on "Loading dashboard..."
- Open browser DevTools (F12 → Console) and check for red errors
- Most likely cause: PHP session conflict. All API files use `if (session_status() === PHP_SESSION_NONE) { session_start(); }` — if you have an older version of any file that uses plain `session_start()`, it will corrupt the JSON response

### "CONFIRMED is not defined" JavaScript error
- This means a status string is missing quotes in `script.js`
- Replace your `script.js` with the latest version

### "Event is fully booked — capacity limit reached"
- Trigger 2 blocked the INSERT because the event is at capacity
- This is intentional and correct behavior

### Private event not hiding from other attendees
- Check that `IsPrivate` column exists: `DESCRIBE event;`
- If missing, run: `ALTER TABLE event ADD COLUMN IF NOT EXISTS IsPrivate TINYINT(1) NOT NULL DEFAULT 0;`

### Triggers not working
- Run: `SHOW TRIGGERS FROM event_management;`
- If empty, re-run `test_data.sql` — triggers are at the top of that file
- Make sure you ran the file from inside `event_management` (breadcrumb must show `Database: event_management`)

### Venue events not cancelling after deactivation
- Confirm Trigger 3 exists (see above)
- The trigger only fires when `IsActive` changes from TRUE to FALSE
- Reactivating a venue does NOT restore cancelled events — they must be manually set back to CONFIRMED

### Remove button does nothing (attendees or guest list)
- Ensure `remove-guest.php` is in your `api/` folder
- Check the browser console for a 404 error on the API call

### Excel export opens as plain text
- The file format is SpreadsheetML XML — it opens correctly in Excel, Google Sheets, and LibreOffice
- If it opens as text, right-click the file → Open With → Excel

### Resource assignments table shows wrong column count
- Ensure you are using the latest `script.js` — the Resources table now has 8 columns including the Action column

### Duplicate events on dashboard
- Run `fix_duplicates.sql` in phpMyAdmin to clean any leftover duplicate `event_schedule` rows
- The schema now has `UNIQUE (SlotID)` and `UNIQUE (VenueID, SlotID)` on `event_schedule` to prevent this permanently

### "Integrity constraint violation: chk_booking_status"
- The booking Status must be exactly: `PENDING`, `APPROVED`, `REJECTED`, or `CANCELLED` (all uppercase)

---

*CelebrateHub — CINS 5305 Event Scheduling System*
*Built with PHP, MySQL, vanilla JavaScript, and a lot of ♥ for every celebration.*