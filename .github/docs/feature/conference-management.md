# Conference Management Feature

## Description

Admin can create, view, edit, and manage conferences including conference details, settings, rooms, and certificates.

## Key Features

- CRUD operations for conferences
- Upload conference cover/poster
- Configure registration dates, fees, and location
- Manage multiple rooms for each conference
- Upload certificate templates
- Soft delete with restore capability

## Sequence Diagram

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

## User Flow

1. Admin logs in
2. Navigate to Conferences menu
3. View list of all conferences with search/filter
4. Create new conference with details (name, date, location, fees, rooms)
5. Upload conference poster
6. Configure registration dates
7. Edit/update conference as needed
8. Delete or restore conferences (soft delete)

## Technical Implementation

### Backend

- **Controller:** `app/Http/Controllers/Admin/ConferencesController.php`
- **Model:** `app/Models/Conference.php`
- **Routes:** `/conferences` (resourceful routes)
- **Validation:** Name, date, fees required; poster must be image

### Frontend

- **Pages:** `resources/js/Pages/Admin/Conferences/`
  - `Index.tsx` - List view with search/filter
  - `Create.tsx` - Create form
  - `Edit.tsx` - Edit form
  - `Show.tsx` - Detail view
- **Components:** Card layout, file upload, date picker, fee inputs

### Database

- **Table:** `conferences`
- **Key Fields:** id, name, description, initial, date, city, country, fees (online/onsite/participant), cover_poster_path
- **Relationships:**
  - `hasMany(Audience)` - audiences registered
  - `hasMany(Room)` - conference rooms
- **Soft Deletes:** Yes

## Common Operations

### Create Conference

```php
POST /conferences
- name (required)
- description
- initial (required)
- date (required)
- registration_start_date
- registration_end_date
- city, country
- online_fee, onsite_fee, participant_fee
- cover_poster_path (file upload)
```

### Update Conference

```php
POST /conferences/{id}
- Same fields as create
- Method spoofing: _method=PUT
```

### Delete Conference

```php
DELETE /conferences/{id}
- Soft delete (deleted_at timestamp)
```

### Restore Conference

```php
PUT /conferences/{id}/restore
- Restore soft-deleted conference
```
