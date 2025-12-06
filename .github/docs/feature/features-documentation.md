# Conference Management Feature Documentation

## 1. Conference Management

### Description

Admin can create, view, edit, and manage conferences including conference details, settings, rooms, and certificates.

### Key Features

- CRUD operations for conferences
- Upload conference cover/poster
- Configure registration dates, fees, and location
- Manage multiple rooms for each conference
- Upload certificate templates
- Soft delete with restore capability

### Sequence Diagram

```mermaid
sequenceDiagram
    participant Admin
    participant WebApp
    participant Backend
    participant DB
    participant Storage

    Admin->>WebApp: Navigate to Conferences
    WebApp->>Backend: GET /conferences
    Backend->>DB: Query conferences
    DB-->>Backend: Conference list
    Backend-->>WebApp: Render conference list

    Admin->>WebApp: Click "Add New Conference"
    WebApp->>Backend: GET /conferences/create
    Backend-->>WebApp: Render create form

    Admin->>WebApp: Fill conference details + upload poster
    WebApp->>Backend: POST /conferences (with file)
    Backend->>Storage: Save poster image
    Backend->>DB: Insert conference data
    DB-->>Backend: Conference created
    Backend-->>WebApp: Success + redirect
    WebApp-->>Admin: Show success notification

    Admin->>WebApp: Click "Edit" on conference
    WebApp->>Backend: GET /conferences/{id}/edit
    Backend->>DB: Get conference
    DB-->>Backend: Conference data
    Backend-->>WebApp: Render edit form

    Admin->>WebApp: Update conference + upload new poster
    WebApp->>Backend: POST /conferences/{id} (with file)
    Backend->>Storage: Save new poster
    Backend->>DB: Update conference
    DB-->>Backend: Updated
    Backend-->>WebApp: Success + redirect
    WebApp-->>Admin: Show success notification
```

### User Flow

1. Admin logs in
2. Navigate to Conferences menu
3. View list of all conferences with search/filter
4. Create new conference with details (name, date, location, fees, rooms)
5. Upload conference poster
6. Configure registration dates
7. Edit/update conference as needed
8. Delete or restore conferences (soft delete)

---

## 2. Audience Registration & Management

### Description

Public users can register for conferences, while admins can view, manage, and verify audience registrations and payments.

### Key Features

- Public registration form
- Payment integration (PayPal, Bank Transfer)
- Payment status management
- Search and filter audiences
- Export audience data to Excel
- Download payment receipts
- View audience details and papers

### Sequence Diagram

```mermaid
sequenceDiagram
    participant User
    participant WebApp
    participant Backend
    participant DB
    participant PayPal
    participant Email

    User->>WebApp: Open conference detail page
    WebApp->>Backend: GET /detail/{conference}
    Backend->>DB: Get conference
    DB-->>Backend: Conference data
    Backend-->>WebApp: Render conference detail

    User->>WebApp: Click "Register"
    WebApp->>Backend: GET /registration/{conference}
    Backend-->>WebApp: Render registration form

    User->>WebApp: Fill registration form + upload paper
    WebApp->>Backend: POST /registration/{conference}
    Backend->>DB: Create audience record
    Backend->>Email: Send confirmation email
    DB-->>Backend: Audience created
    Backend-->>WebApp: Redirect to payment page

    User->>WebApp: Select PayPal payment
    WebApp->>Backend: POST /registration/{conference}/payment
    Backend->>PayPal: Create payment order
    PayPal-->>Backend: Order ID + approval URL
    Backend-->>WebApp: Redirect to PayPal
    User->>PayPal: Complete payment
    PayPal->>Backend: Return to callback URL
    Backend->>DB: Update payment status to "paid"
    Backend->>Email: Send payment confirmation
    DB-->>Backend: Updated
    Backend-->>WebApp: Redirect to success page
    WebApp-->>User: Show success message

    Note over Admin,DB: Admin Side
    Admin->>WebApp: Navigate to Audiences
    WebApp->>Backend: GET /audiences (with filters)
    Backend->>DB: Query audiences
    DB-->>Backend: Audience list
    Backend-->>WebApp: Render audience table
    Admin->>WebApp: Update payment status
    WebApp->>Backend: PATCH /audiences/{id}/payment-status
    Backend->>DB: Update status
    Backend->>Email: Send status email
    DB-->>Backend: Updated
    Backend-->>WebApp: Success
```

### User Flow

1. User visits conference detail page
2. Click register button
3. Fill registration form (name, institution, paper title, etc.)
4. Upload full paper (PDF)
5. Select presentation type and payment method
6. Process payment via PayPal or bank transfer
7. Receive confirmation email
8. Admin verifies payment (for bank transfer)
9. Admin can export data or download receipts

---

## 3. Letter of Approval (LoA) Management

### Description

Admin can generate and manage Letters of Approval for accepted papers, including assigning LoA volumes and downloading PDF letters.

### Key Features

- List eligible audiences (paid + paper submitted)
- Assign LoA volume to audience
- Input author names for LoA
- Generate PDF letter
- Download individual or bulk LoA
- Filter by conference and LoA volume
- Search by participant details

### Sequence Diagram

```mermaid
sequenceDiagram
    participant Admin
    participant WebApp
    participant Backend
    participant DB
    participant PDFGen

    Admin->>WebApp: Navigate to Letters of Approval
    WebApp->>Backend: GET /letters-of-approval (with filters)
    Backend->>DB: Query audiences (paid + paper submitted)
    DB-->>Backend: Eligible audiences + LoA volumes
    Backend-->>WebApp: Render LoA list

    Admin->>WebApp: Click "Download LoA" for audience
    WebApp->>Backend: GET /letters-of-approval/{audience}/download-form
    Backend->>DB: Get audience + available LoA volumes
    DB-->>Backend: Audience + volumes
    Backend-->>WebApp: Render LoA form

    Admin->>WebApp: Enter authors + select LoA volume
    WebApp->>Backend: POST /letters-of-approval/{audience}/update-info
    Backend->>DB: Update audience.loa_authors & loa_volume_id
    DB-->>Backend: Updated
    Backend-->>WebApp: Success message

    Admin->>WebApp: Click "Download PDF"
    WebApp->>Backend: GET /letters-of-approval/{audience}/download
    Backend->>DB: Get audience + conference + LoA volume
    DB-->>Backend: Complete data
    Backend->>PDFGen: Generate LoA PDF (DomPDF)
    PDFGen-->>Backend: PDF file
    Backend-->>WebApp: Stream PDF file
    WebApp-->>Admin: Download LoA PDF

    Admin->>WebApp: Select multiple audiences + bulk download
    WebApp->>Backend: POST /letters-of-approval/bulk-download
    Backend->>DB: Get selected audiences
    Backend->>PDFGen: Generate multiple PDFs
    PDFGen-->>Backend: ZIP file with PDFs
    Backend-->>WebApp: Download ZIP
    WebApp-->>Admin: Download bulk LoA ZIP
```

### User Flow

1. Admin logs in
2. Navigate to Letters of Approval
3. View list of eligible participants (paid + paper submitted)
4. Filter by conference or LoA volume
5. Click "Download Form" for a participant
6. Enter list of authors
7. Select LoA volume from dropdown
8. Submit and approve LoA info
9. Download LoA PDF
10. Optionally bulk download multiple LoA

---

## 4. LoA Volume Management

### Description

Admin can manage LoA volumes as master data, which can be assigned to audiences for their Letters of Approval.

### Key Features

- CRUD operations for LoA volumes
- Search volumes
- View audience count per volume
- View list of audiences assigned to each volume
- Unique volume validation
- Audit trail (created_by, updated_by)

### Sequence Diagram

```mermaid
sequenceDiagram
    participant Admin
    participant WebApp
    participant Backend
    participant DB

    Admin->>WebApp: Navigate to Master Data > LoA Volume
    WebApp->>Backend: GET /admin/loa-volumes
    Backend->>DB: Query LoA volumes with audience count
    DB-->>Backend: Volumes + audiences_count
    Backend-->>WebApp: Render volume list

    Admin->>WebApp: Click "Add LoA Volume"
    WebApp->>Backend: GET /admin/loa-volumes/create
    Backend-->>WebApp: Render create form

    Admin->>WebApp: Enter volume name (e.g., "Vol. 10 No. 6")
    WebApp->>Backend: POST /admin/loa-volumes
    Backend->>DB: Check unique volume
    alt Volume already exists
        DB-->>Backend: Validation error
        Backend-->>WebApp: Show error "Volume already exists"
    else Volume is unique
        DB-->>Backend: Insert volume
        Backend-->>WebApp: Success + redirect
        WebApp-->>Admin: Show success notification
    end

    Admin->>WebApp: Click "View" on volume
    WebApp->>Backend: GET /admin/loa-volumes/{id}
    Backend->>DB: Get volume + assigned audiences
    DB-->>Backend: Volume data + audiences list
    Backend-->>WebApp: Render volume detail + audience table
    WebApp-->>Admin: Show volume info + audiences

    Admin->>WebApp: Click "Edit" on volume
    WebApp->>Backend: GET /admin/loa-volumes/{id}/edit
    Backend->>DB: Get volume
    DB-->>Backend: Volume data
    Backend-->>WebApp: Render edit form

    Admin->>WebApp: Update volume name
    WebApp->>Backend: PUT /admin/loa-volumes/{id}
    Backend->>DB: Check unique (except current)
    DB-->>Backend: Validation passed
    Backend->>DB: Update volume
    DB-->>Backend: Updated
    Backend-->>WebApp: Success + redirect
```

### User Flow

1. Admin logs in
2. Navigate to Master Data > LoA Volume
3. View list of volumes with audience count
4. Create new volume with unique name
5. Edit existing volume
6. View volume details to see assigned audiences
7. Delete volume if no audiences assigned
8. Search volumes by name

---

## 5. Keynote & Parallel Session Management

### Description

Admin can manage keynote speakers and parallel session submissions from participants.

### Key Features

- View all keynote registrations
- View all parallel session registrations
- Search and filter by conference
- View presenter details and paper info
- Delete submissions
- Export data

### Sequence Diagram

```mermaid
sequenceDiagram
    participant User
    participant WebApp
    participant Backend
    participant DB

    User->>WebApp: Open keynote registration form
    WebApp->>Backend: GET /keynote/{conference}
    Backend->>DB: Get conference + rooms
    DB-->>Backend: Conference data
    Backend-->>WebApp: Render keynote form

    User->>WebApp: Fill keynote details + upload abstract
    WebApp->>Backend: POST /keynote/{conference}
    Backend->>DB: Create keynote record
    DB-->>Backend: Keynote created
    Backend-->>WebApp: Redirect to success
    WebApp-->>User: Show success message

    Note over Admin,DB: Admin Side
    Admin->>WebApp: Navigate to Keynotes
    WebApp->>Backend: GET /keynotes (with filters)
    Backend->>DB: Query keynotes
    DB-->>Backend: Keynote list
    Backend-->>WebApp: Render keynote table

    Admin->>WebApp: Click "View" keynote
    WebApp->>Backend: GET /keynotes/{id}
    Backend->>DB: Get keynote details
    DB-->>Backend: Keynote data
    Backend-->>WebApp: Render keynote detail

    Admin->>WebApp: Click "Delete" keynote
    WebApp->>Backend: DELETE /keynotes/{id}
    Backend->>DB: Soft delete keynote
    DB-->>Backend: Deleted
    Backend-->>WebApp: Success
```

### User Flow (Keynote)

1. User visits conference page
2. Click "Register as Keynote Speaker"
3. Fill form (name, affiliation, bio, abstract)
4. Upload abstract file
5. Select room and time slot
6. Submit form
7. Admin reviews keynote submissions
8. Admin can view, filter, search, or delete

### User Flow (Parallel Session)

- Similar to keynote
- Includes paper title and participant email
- Room assignment
- Admin management via parallel sessions page

---

## 6. Dashboard & Statistics

### Description

Admin dashboard showing overview statistics and recent activities.

### Key Features

- Total conferences count
- Total audiences count
- Total revenue
- Payment status breakdown
- Recent registrations
- Charts and graphs

### Sequence Diagram

```mermaid
sequenceDiagram
    participant Admin
    participant WebApp
    participant Backend
    participant DB

    Admin->>WebApp: Login
    WebApp->>Backend: POST /login
    Backend->>DB: Verify credentials
    DB-->>Backend: User authenticated
    Backend-->>WebApp: Redirect to dashboard

    WebApp->>Backend: GET /dashboard
    Backend->>DB: Aggregate statistics
    DB-->>Backend: Total conferences
    DB-->>Backend: Total audiences
    DB-->>Backend: Payment summary
    DB-->>Backend: Recent registrations
    Backend-->>WebApp: Render dashboard with stats
    WebApp-->>Admin: Display dashboard
```

### User Flow

1. Admin logs in with credentials
2. Redirected to dashboard automatically
3. View summary cards (conferences, audiences, revenue)
4. View payment status breakdown
5. Navigate to specific features from dashboard menu

---

## Common Features Across All Modules

### Search & Filter

- Real-time search with debouncing (500ms delay)
- Auto-focus on search input
- Filter by conference, status, payment method, etc.
- URL parameter persistence

### Pagination

- Server-side pagination (15, 25, 50, 100 per page)
- PrimeReact DataTable component
- Page navigation controls
- Total records display

### Validation

- Backend: Laravel validation rules
- Frontend: Form error display with Mantine
- Real-time feedback
- Unique constraints enforcement

### UI/UX Consistency

- Mantine UI components
- Card-based layouts
- Badge status indicators
- Action buttons (View, Edit, Delete)
- Success/error notifications
- Responsive design

---

## Tech Implementation Notes

### Backend (Laravel)

- Controllers in `app/Http/Controllers/Admin/`
- Models in `app/Models/`
- Migrations in `database/migrations/`
- Routes in `routes/web.php`
- Eloquent relationships for data integrity

### Frontend (React + TypeScript)

- Pages in `resources/js/Pages/Admin/`
- Components in `resources/js/Components/`
- Inertia.js for SPA experience
- Mantine UI for components
- PrimeReact for data tables

### Database

- Foreign key constraints
- Soft deletes on most tables
- Audit fields (created_by, updated_by)
- Proper indexing for performance
