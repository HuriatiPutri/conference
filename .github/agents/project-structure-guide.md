# Conference Management - Project Structure Guide

## Overview

This guide helps AI agents and developers understand the project structure, coding standards, and patterns used in the Conference Management Application. Follow this guide when creating new features or modifying existing code.

---

## Technology Stack

### Backend

- **Framework:** Laravel 10+ (PHP 8.2+)
- **Database:** PostgreSQL
- **ORM:** Eloquent
- **PDF Generation:** DomPDF (barryvdh/laravel-dompdf)
- **Excel Export:** Maatwebsite/Laravel-Excel
- **Authentication:** Laravel Session-based Auth
- **API Integration:** PayPal SDK

### Frontend

- **Framework:** React 18 with TypeScript
- **UI Library:** Mantine UI v7
- **Data Tables:** PrimeReact DataTable
- **SPA Framework:** Inertia.js
- **Build Tool:** Vite
- **Icons:** Tabler Icons
- **Routing:** Ziggy (Laravel routes in JavaScript)

### Database Features

- Foreign key constraints
- Soft deletes on most tables
- Audit fields (created_by, updated_by)
- Timestamps on all tables

---

## Project Structure

```
converence-v2/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/              # Admin panel controllers
│   │   │   │   ├── ConferencesController.php
│   │   │   │   ├── AudiencesController.php
│   │   │   │   ├── LettersOfApprovalController.php
│   │   │   │   ├── LoaVolumeManagementController.php
│   │   │   │   ├── KeynoteManagementController.php
│   │   │   │   └── ParallelSessionManagementController.php
│   │   │   ├── AuthController.php
│   │   │   ├── RegistrationController.php
│   │   │   └── ...
│   │   ├── Middleware/
│   │   ├── Requests/               # Form request validation
│   │   └── Resources/              # API resources
│   ├── Models/
│   │   ├── Conference.php
│   │   ├── Audience.php
│   │   ├── LoaVolume.php
│   │   ├── KeyNote.php
│   │   ├── ParallelSession.php
│   │   ├── Room.php
│   │   └── User.php
│   ├── Services/                   # Business logic services
│   │   ├── PayPalService.php
│   │   └── MockPayPalService.php
│   └── Exports/                    # Excel export classes
│       └── AudienceExport.php
├── database/
│   ├── migrations/                 # Database migrations
│   ├── seeders/                    # Database seeders
│   └── factories/                  # Model factories
├── resources/
│   ├── js/
│   │   ├── Pages/                  # Inertia pages
│   │   │   ├── Admin/
│   │   │   │   ├── Conferences/
│   │   │   │   │   ├── Index.tsx
│   │   │   │   │   ├── Create.tsx
│   │   │   │   │   ├── Edit.tsx
│   │   │   │   │   └── Show.tsx
│   │   │   │   ├── Audiences/
│   │   │   │   ├── LettersOfApproval/
│   │   │   │   ├── LoaVolumes/
│   │   │   │   ├── Keynotes/
│   │   │   │   ├── ParallelSessions/
│   │   │   │   └── Dashboard/
│   │   │   └── Registration/       # Public pages
│   │   ├── Components/
│   │   │   ├── Elements/
│   │   │   │   ├── Navbar/
│   │   │   │   │   └── Navigation.tsx
│   │   │   │   └── LinkGroup/
│   │   │   └── Modals/
│   │   ├── Layout/
│   │   │   └── MainLayout.tsx
│   │   ├── Constants/              # Constants and enums
│   │   ├── types/                  # TypeScript types
│   │   └── utils.ts                # Utility functions
│   ├── views/                      # Blade templates (for PDFs, emails)
│   │   ├── app.blade.php
│   │   ├── letters-of-approval/
│   │   ├── receipt/
│   │   └── emails/
│   └── css/
│       └── app.css
├── routes/
│   └── web.php                     # All routes defined here
├── config/                         # Configuration files
├── public/                         # Public assets
└── storage/                        # File storage
    └── app/
        └── public/                 # Public storage (symlinked)
```

---

## Naming Conventions

### Backend (Laravel)

#### Controllers

- **Admin Controllers:** `{Entity}Controller.php` or `{Entity}ManagementController.php`
  - Examples: `ConferencesController.php`, `LoaVolumeManagementController.php`
- **Public Controllers:** `{Action}Controller.php`
  - Examples: `RegistrationController.php`, `KeynoteController.php`
- **Location:** `app/Http/Controllers/Admin/` for admin controllers

#### Models

- **Singular noun, PascalCase:** `Conference`, `Audience`, `LoaVolume`
- **Location:** `app/Models/`
- **Must use traits:** `HasFactory`, `SoftDeletes` (if applicable)

#### Migrations

- **Format:** `YYYY_MM_DD_HHMMSS_description.php`
- **Naming:** `create_{table}_table.php` or `add_{field}_to_{table}.php`
- **Examples:**
  - `2025_11_15_055943_create_loa_volume_table.php`
  - `2025_11_15_060000_modify_audience_table_change_loa_volume_field.php`

#### Routes

- **Admin routes:** Prefix with `/admin/` or group under admin
- **Resource routes:** Use `Route::resource()` for CRUD
- **Route names:** Use dot notation `admin.loa-volumes.index`
- **Examples:**
  ```php
  Route::prefix('admin')->name('admin.')->group(function () {
      Route::resource('loa-volumes', LoaVolumeManagementController::class);
  });
  ```

### Frontend (React + TypeScript)

#### Components

- **PascalCase:** `Navigation.tsx`, `MainLayout.tsx`
- **Location:** `resources/js/Components/`

#### Pages

- **PascalCase:** `Index.tsx`, `Create.tsx`, `Edit.tsx`, `Show.tsx`
- **Location:** `resources/js/Pages/Admin/{Module}/`
- **Structure:** Each module has its own folder

#### Types

- **Interfaces:** PascalCase with descriptive names
- **Location:** `resources/js/types/index.d.ts` or inline
- **Examples:** `Audience`, `Conference`, `LoaVolume`, `PaginatedData<T>`

#### Constants

- **UPPER_SNAKE_CASE:** `PAYMENT_METHOD`, `PRESENTATION_TYPE`
- **Location:** `resources/js/Constants/index.ts`

---

## Database Schema Patterns

### Standard Table Structure

```sql
CREATE TABLE entity_name (
    id BIGSERIAL PRIMARY KEY,
    -- entity fields
    created_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    updated_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMP NULL  -- for soft deletes
);
```

### Relationships

- **Foreign keys:** Use `{entity}_id` format
- **Cascading:** Use `ON DELETE SET NULL` or `ON DELETE CASCADE` appropriately
- **Indexes:** Add indexes on foreign keys and frequently queried fields

### Audit Fields

All tables should have:

- `created_by` - Foreign key to users table
- `updated_by` - Foreign key to users table
- `created_at` - Timestamp
- `updated_at` - Timestamp
- `deleted_at` - Timestamp (nullable, for soft deletes)

---

## Model Patterns

### Standard Model Setup

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EntityName extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'table_name';

    protected $fillable = [
        'field1',
        'field2',
        'created_by',
        'updated_by',
    ];

    protected $dates = ['deleted_at'];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Other relationships...
}
```

### Common Relationships

- `belongsTo` - Many-to-one (e.g., Audience belongs to Conference)
- `hasMany` - One-to-many (e.g., Conference has many Audiences)
- `belongsToMany` - Many-to-many (with pivot table)

---

## Controller Patterns

### Admin CRUD Controller

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EntityName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as RequestFacade;
use Inertia\Inertia;
use Inertia\Response;

class EntityManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $filters = RequestFacade::only('search');
        $perPage = RequestFacade::input('per_page', 15);
        $search = RequestFacade::input('search', '');

        $query = EntityName::query()->with(['relationships']);

        if (!empty($search)) {
            $query->where('field', 'ILIKE', "%{$search}%");
        }

        $entities = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends(RequestFacade::all());

        return Inertia::render('Admin/Entities/Index', [
            'entities' => $entities,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Entities/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'field' => 'required|string|max:255',
        ]);

        EntityName::create([
            'field' => $request->field,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.entities.index')
            ->with('success', 'Entity created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EntityName $entity): Response
    {
        $entity->load(['relationships']);

        return Inertia::render('Admin/Entities/Show', [
            'entity' => $entity,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EntityName $entity): Response
    {
        return Inertia::render('Admin/Entities/Edit', [
            'entity' => $entity,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EntityName $entity)
    {
        $request->validate([
            'field' => 'required|string|max:255',
        ]);

        $entity->update([
            'field' => $request->field,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.entities.index')
            ->with('success', 'Entity updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EntityName $entity)
    {
        $entity->delete();

        return redirect()->back()
            ->with('success', 'Entity deleted successfully.');
    }
}
```

---

## Frontend Patterns

### Page Component Structure

```typescript
import { router, usePage } from '@inertiajs/react';
import { Button, Card, Container, Group, Stack, Text, Title } from '@mantine/core';
import { Column } from 'primereact/column';
import { DataTable } from 'primereact/datatable';
import React, { useState } from 'react';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';
import { PaginatedData } from '../../../types';

interface Entity {
  id: number;
  field: string;
  created_at: string;
}

function EntityIndex() {
  const { entities } = usePage<{
    entities: PaginatedData<Entity>;
  }>().props;

  const { data, meta } = entities;

  const handleView = (entity: Entity) => {
    router.get(route('admin.entities.show', entity.id));
  };

  return (
    <MainLayout>
      <Container size="xl">
        <Stack gap="lg">
          <Group justify="space-between">
            <Title order={2}>Entity Management</Title>
            <Button onClick={() => router.get(route('admin.entities.create'))}>
              Add Entity
            </Button>
          </Group>

          <Card withBorder>
            <DataTable
              value={data}
              paginator
              rows={meta.per_page}
              totalRecords={meta.total}
            >
              <Column field="field" header="Field" />
              <Column header="Actions" body={(row) => (
                <Button onClick={() => handleView(row)}>View</Button>
              )} />
            </DataTable>
          </Card>
        </Stack>
      </Container>
    </MainLayout>
  );
}

export default EntityIndex;
```

### Search with Debouncing

```typescript
const [globalFilterValue, setGlobalFilterValue] = useState(() => {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get('search') || '';
});

const [searchTimeout, setSearchTimeout] = useState<number | null>(null);
const searchInputRef = useRef<HTMLInputElement>(null);

const onGlobalFilterChange = (e: React.ChangeEvent<HTMLInputElement>) => {
  const value = e.target.value;
  setGlobalFilterValue(value);

  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }

  const newTimeout = setTimeout(() => {
    handleFilterChange(value);
  }, 500);

  setSearchTimeout(newTimeout);
};
```

---

## Validation Patterns

### Backend Validation

```php
$request->validate([
    'field' => 'required|string|max:255',
    'email' => 'required|email|unique:table,email',
    'file' => 'required|file|mimes:pdf|max:5120',
    'foreign_id' => 'required|exists:other_table,id',
], [
    'field.required' => 'Custom error message',
]);
```

### Frontend Form Handling

```typescript
const { data, setData, post, processing, errors } = useForm({
  field: '',
});

const handleSubmit = (e: React.FormEvent) => {
  e.preventDefault();
  post(route('admin.entities.store'), {
    onSuccess: () => {
      notifications.show({
        message: 'Created successfully!',
        color: 'green',
      });
    },
  });
};
```

---

## Common Patterns

### Pagination

- Default: 15 records per page
- Options: 15, 25, 50, 100
- Server-side pagination with `paginate()`
- URL parameters: `?page=1&per_page=15&search=term`

### Search

- Multi-field search using `ILIKE` (case-insensitive)
- Debounced input (500ms delay)
- Auto-focus on search input
- Preserve search in URL

### Filters

- Dropdown filters for relationships (conference, status, etc.)
- URL parameter preservation
- Apply/Clear filter buttons
- Combine with search

### Soft Deletes

- Use `SoftDeletes` trait
- `deleted_at` timestamp field
- Query methods: `withTrashed()`, `onlyTrashed()`
- Restore functionality if needed

### File Uploads

- Store in `storage/app/public/`
- Create symlink: `php artisan storage:link`
- Validation: file type, max size
- Path stored in database

---

## UI/UX Standards

### Colors

- **Primary (Blue):** Main actions
- **Green:** Success, approved
- **Yellow:** Warning, pending
- **Red:** Error, destructive
- **Gray:** Secondary, neutral

### Components

- **Cards:** `<Card withBorder>` for content containers
- **Buttons:** Consistent styling with Mantine
- **Tables:** PrimeReact DataTable for lists
- **Forms:** Mantine form components
- **Badges:** For status display

### Responsive Grid

```typescript
<Grid>
  <Grid.Col span={{ base: 12, md: 6, lg: 4 }}>
    {/* Content */}
  </Grid.Col>
</Grid>
```

### Icons

- Use Tabler Icons
- Common: `IconPlus`, `IconEdit`, `IconTrash`, `IconEye`, `IconDownload`

---

## Best Practices

### Security

- Always validate input on backend
- Use parameterized queries (Eloquent handles this)
- Protect routes with middleware
- Validate foreign keys with `exists` rule
- Sanitize user input

### Performance

- Use eager loading (`with()`) to prevent N+1 queries
- Index foreign keys and frequently queried fields
- Implement pagination on large datasets
- Debounce search inputs

### Code Quality

- Follow PSR-12 coding standards (PHP)
- Use TypeScript for type safety
- Write descriptive commit messages
- Comment complex logic
- Keep functions small and focused

### Testing

- Write tests for critical functionality
- Test validation rules
- Test relationships
- Test file uploads

---

## Creating a New Feature

### Step-by-Step Process

1. **Create Migration**

   ```bash
   php artisan make:migration create_{table}_table
   ```

2. **Create Model**

   ```bash
   php artisan make:model {ModelName}
   ```

   - Add fillable fields
   - Add relationships
   - Use SoftDeletes if needed

3. **Create Controller**

   ```bash
   php artisan make:controller Admin/{ControllerName} --resource
   ```

   - Implement CRUD methods
   - Add validation
   - Set created_by/updated_by

4. **Add Routes**
   - Add to `routes/web.php`
   - Use resource routes or custom routes
   - Group under admin if needed

5. **Create Frontend Pages**
   - Create folder in `resources/js/Pages/Admin/{Module}/`
   - Create Index.tsx, Create.tsx, Edit.tsx, Show.tsx
   - Follow existing patterns

6. **Update Navigation**
   - Add menu item in `Navigation.tsx`
   - Use appropriate icon
   - Set correct route

7. **Test**
   - Test CRUD operations
   - Test validation
   - Test search and filters
   - Test responsive design

---

## Common Commands

```bash
# Run migrations
php artisan migrate

# Create migration
php artisan make:migration {name}

# Create model
php artisan make:model {Name}

# Create controller
php artisan make:controller {Name}

# Create seeder
php artisan make:seeder {Name}

# Run seeder
php artisan db:seed --class={Name}

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Storage link
php artisan storage:link

# Run dev server
php artisan serve

# Build frontend
npm run dev
npm run build
```

---

## Resources

- **Laravel Docs:** https://laravel.com/docs
- **React Docs:** https://react.dev
- **Mantine UI:** https://mantine.dev
- **PrimeReact:** https://primereact.org
- **Inertia.js:** https://inertiajs.com
- **TypeScript:** https://www.typescriptlang.org

---

## Feature Documentation

Detailed feature documentation with sequence diagrams is available in:

- `.github/docs/feature/` - Individual feature documentation
- `.github/docs/app-overview.md` - Application overview

---

**Always refer to existing code for patterns and conventions before creating new features!**
