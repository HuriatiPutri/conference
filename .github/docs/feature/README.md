# Feature Documentation Index

Welcome to the Conference Management Application feature documentation. Each feature is documented in detail with sequence diagrams, user flows, and technical implementation notes.

## Individual Feature Documentation

### Core Features

1. **[Conference Management](./conference-management.md)**
   - CRUD operations for conferences
   - Upload conference poster and certificates
   - Configure registration dates and fees
   - Manage conference rooms

2. **[Audience Registration & Management](./audience-management.md)**
   - Public registration form
   - Payment integration (PayPal, Bank Transfer)
   - Admin verification and management
   - Export and receipt generation

3. **[Letter of Approval (LoA) Management](./loa-management.md)**
   - Generate PDF letters for accepted papers
   - Assign LoA volumes to audiences
   - Input author information
   - Bulk download capabilities

4. **[LoA Volume Management](./loa-volume-management.md)**
   - Master data for LoA volumes
   - View audiences assigned to each volume
   - Unique volume validation
   - Audit trail tracking

5. **[Keynote & Parallel Session Management](./keynote-parallel-session-management.md)**
   - Manage keynote speaker registrations
   - Manage parallel session submissions
   - Search and filter capabilities
   - Room assignment

6. **[Dashboard & Authentication](./dashboard-authentication.md)**
   - Admin dashboard with statistics
   - Login/logout functionality
   - Summary cards and metrics
   - Navigation menu

### Cross-Cutting Concerns

7. **[Common Patterns & UI/UX Standards](./common-patterns.md)**
   - Search functionality
   - Pagination patterns
   - Filters and sorting
   - Form patterns
   - Validation
   - Status badges
   - File uploads
   - Soft deletes
   - Responsive design

## Quick Reference

### Technology Stack

- **Backend:** Laravel (PHP)
- **Frontend:** React (TypeScript), Mantine UI, PrimeReact
- **Database:** MySQL/PostgreSQL
- **PDF Generation:** DomPDF
- **Payment:** PayPal API
- **Session:** Inertia.js SPA

### Project Structure

```
app/
├── Http/Controllers/Admin/    # Admin controllers
├── Models/                     # Eloquent models
database/
├── migrations/                 # Database migrations
resources/
├── js/
│   ├── Pages/Admin/           # Admin pages (React)
│   ├── Components/            # Reusable components
│   └── Layout/                # Layout components
routes/
└── web.php                    # Application routes
```

### Common Routes

- `/dashboard` - Admin dashboard
- `/conferences` - Conference management
- `/audiences` - Audience management
- `/letters-of-approval` - LoA management
- `/admin/loa-volumes` - LoA volume management
- `/keynotes` - Keynote management
- `/parallel-sessions` - Parallel session management

### Key Models & Relationships

```
Conference
├── hasMany(Audience)
├── hasMany(Room)

Audience
├── belongsTo(Conference)
├── belongsTo(LoaVolume)
├── hasMany(KeyNote)
├── hasMany(ParallelSession)

LoaVolume
├── hasMany(Audience)
├── belongsTo(User, 'created_by')
├── belongsTo(User, 'updated_by')

Room
├── belongsTo(Conference)
├── hasMany(KeyNote)
├── hasMany(ParallelSession)
```

## How to Use This Documentation

### For New Developers

1. Start with [Common Patterns](./common-patterns.md) to understand UI/UX standards
2. Read [Dashboard & Authentication](./dashboard-authentication.md) to understand the login flow
3. Review individual feature docs based on your assigned tasks
4. Refer to sequence diagrams to understand data flow

### For Feature Development

1. Review the existing feature documentation for similar functionality
2. Follow the established patterns and conventions
3. Use the same UI components and layouts
4. Ensure proper validation on both frontend and backend
5. Add tests if required

### For Bug Fixes

1. Locate the relevant feature documentation
2. Review the sequence diagram to understand the flow
3. Check the technical implementation section
4. Verify validation rules and error handling

### For Code Review

1. Compare implementation against documented patterns
2. Verify consistency with UI/UX standards
3. Check for proper error handling and validation
4. Ensure responsive design is maintained

## Contributing to Documentation

When adding new features:

1. Create a new markdown file in `.github/docs/feature/`
2. Follow the template structure from existing docs
3. Include sequence diagram using Mermaid syntax
4. Document user flow step-by-step
5. Add technical implementation details
6. Update this index file with a link to your new doc

## Need Help?

- **Project README:** See `/README.md` for setup instructions
- **Agent Instructions:** See `/.github/agents/instruction.md` for AI-assisted development
- **Application Overview:** See `/.github/docs/app-overview.md` for high-level architecture

## Changelog

- **2025-12-06:** Created comprehensive feature documentation
- **2025-11-15:** Added LoA Volume management feature
- **2025-11-15:** Integrated LoA Volume with audience management
- **2025-11-03:** Added settings and end date description
