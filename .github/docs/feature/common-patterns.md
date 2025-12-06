# Common Features & UI/UX Patterns

## Overview

This document describes common features, patterns, and UI/UX standards used across all modules in the Conference Management Application.

---

## 1. Search Functionality

### Pattern

All list pages implement consistent search functionality with the following characteristics:

### Features

- **Real-time Search:** Updates results as user types
- **Debouncing:** 500ms delay to prevent excessive server requests
- **Auto-focus:** Search input automatically focused when there's a search term
- **URL Persistence:** Search term saved in URL parameters
- **Multi-field Search:** Searches across multiple relevant fields

### Implementation

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

useEffect(() => {
  if (globalFilterValue && searchInputRef.current) {
    searchInputRef.current.focus();
    const length = globalFilterValue.length;
    searchInputRef.current.setSelectionRange(length, length);
  }
}, [globalFilterValue]);
```

### Search Fields by Module

- **Audiences:** Name, email, institution, paper title, conference
- **LoA:** Name, email, institution, paper title
- **Keynotes:** Speaker name, email, affiliation, title, conference
- **Parallel Sessions:** Presenter name, paper title, email, room, conference
- **LoA Volumes:** Volume name

---

## 2. Pagination

### Pattern

Server-side pagination with consistent options and controls.

### Features

- **Page Size Options:** 15, 25, 50, 100 records per page
- **Default:** 15 records per page
- **Navigation:** First, Previous, Next, Last page buttons
- **Current Page Display:** Shows current range and total
- **URL Parameters:** Page number and per_page preserved in URL

### Implementation

```typescript
const onPage = (event: DataTableStateEvent) => {
  const params = new URLSearchParams();
  if (globalFilterValue.trim()) params.append('search', globalFilterValue.trim());
  params.append('page', (event.first! / event.rows! + 1).toString());
  params.append('per_page', event.rows!.toString());

  router.get(
    `${window.location.pathname}?${params.toString()}`,
    {},
    {
      preserveState: true,
      preserveScroll: true,
    }
  );
};
```

### PrimeReact DataTable Config

```typescript
<DataTable
  lazy
  paginator
  first={(meta.current_page - 1) * meta.per_page}
  rows={meta.per_page}
  totalRecords={meta.total}
  onPage={onPage}
  rowsPerPageOptions={[15, 25, 50]}
  paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
  currentPageReportTemplate="{first} to {last} of {totalRecords}"
/>
```

---

## 3. Filters

### Pattern

Consistent filter interface with multiple filter options.

### Common Filter Types

- **Conference Filter:** Dropdown to filter by conference
- **Status Filter:** Dropdown for payment/approval status
- **Date Range:** Start and end date pickers
- **Custom Filters:** Module-specific (e.g., LoA Volume, Room)

### Filter UI

```typescript
<Grid>
  <Grid.Col span={{ base: 12, md: 4 }}>
    <Select
      placeholder="-- All Conferences --"
      data={conferences.map(c => ({
        value: c.id.toString(),
        label: c.name
      }))}
      value={conferenceFilter}
      onChange={(value) => setConferenceFilter(value || '')}
    />
  </Grid.Col>
  <Grid.Col span={{ base: 12, md: 4 }}>
    <TextInput
      placeholder="Search..."
      value={searchTerm}
      onChange={(e) => setSearchTerm(e.target.value)}
    />
  </Grid.Col>
  <Grid.Col span={{ base: 12, md: 4 }}>
    <Group>
      <Button onClick={handleFilterChange}>Apply Filter</Button>
      <Button variant="outline" onClick={clearFilters}>Clear</Button>
    </Group>
  </Grid.Col>
</Grid>
```

---

## 4. Data Tables

### Component

PrimeReact DataTable for all list views.

### Standard Features

- **Striped Rows:** Better readability
- **Grid Lines:** Optional, used in most tables
- **Responsive:** Horizontal scroll on mobile
- **Empty State:** "No records found" message
- **Loading State:** Skeleton or spinner during fetch

### Column Patterns

```typescript
const columns = [
  {
    field: 'serial_number',
    header: 'No.',
    body: (_, { rowIndex }) => rowIndex + 1
  },
  {
    field: 'name',
    header: 'Name',
    sortable: true
  },
  {
    field: 'status',
    header: 'Status',
    body: (row) => <Badge>{row.status}</Badge>
  },
  {
    field: 'actions',
    header: 'Actions',
    body: (row) => <ActionButtons row={row} />
  }
];
```

---

## 5. Action Buttons

### Pattern

Consistent action buttons for View, Edit, Delete operations.

### ActionButtonExt Component

```typescript
<ActionButtonExt
  onView={() => handleView(row)}
  onEdit={() => handleEdit(row)}
  onDelete={() => handleDelete(row)}
/>
```

### Individual Actions

- **View:** Eye icon, navigate to detail page
- **Edit:** Pencil icon, navigate to edit form
- **Delete:** Trash icon, confirm dialog then soft delete
- **Download:** Download icon, trigger download
- **Custom:** Module-specific actions

### Delete Confirmation

```typescript
const handleDelete = item => {
  if (confirm('Are you sure you want to delete this item?')) {
    router.delete(route('admin.resource.destroy', item.id), {
      preserveScroll: true,
    });
  }
};
```

---

## 6. Summary Cards

### Pattern

Grid of summary statistics at the top of list pages.

### Layout

```typescript
<Grid>
  <Grid.Col span={{ base: 12, md: 4 }}>
    <Card withBorder>
      <Group justify="space-between">
        <div>
          <Text c="dimmed" size="sm" fw={500}>Total Records</Text>
          <Text fw={700} size="xl">{summary.total}</Text>
        </div>
        <IconFileText size={24} color="blue" />
      </Group>
    </Card>
  </Grid.Col>
  {/* More cards... */}
</Grid>
```

### Common Metrics

- **Total Count:** All records
- **This Month:** Records created in current month
- **Status Breakdown:** Count by status
- **Related Counts:** Associated records (e.g., audiences per volume)

---

## 7. Form Patterns

### Create Form

```typescript
const { data, setData, post, processing, errors } = useForm({
  field1: '',
  field2: '',
});

const handleSubmit = (e: React.FormEvent) => {
  e.preventDefault();
  post(route('admin.resource.store'), {
    onSuccess: () => {
      notifications.show({ message: 'Created successfully!', color: 'green' });
    },
  });
};
```

### Edit Form

```typescript
const { data, setData, put, processing, errors } = useForm({
  field1: resource.field1,
  field2: resource.field2,
});

const handleSubmit = (e: React.FormEvent) => {
  e.preventDefault();
  put(route('admin.resource.update', resource.id), {
    onSuccess: () => {
      notifications.show({ message: 'Updated successfully!', color: 'green' });
    },
  });
};
```

### Form Layout

```typescript
<Card withBorder>
  <form onSubmit={handleSubmit}>
    <Stack gap="md">
      <TextInput
        label="Field Name"
        value={data.field}
        onChange={(e) => setData('field', e.target.value)}
        error={errors.field}
        required
      />

      <Group justify="flex-end" pt="md">
        <Button variant="subtle" onClick={() => router.visit(route('admin.resource.index'))}>
          Cancel
        </Button>
        <Button type="submit" loading={processing}>
          Save
        </Button>
      </Group>
    </Stack>
  </form>
</Card>
```

---

## 8. Validation

### Backend (Laravel)

```php
$request->validate([
    'field' => 'required|string|max:255',
    'email' => 'required|email|unique:table,email',
    'file' => 'required|file|mimes:pdf|max:5120',
]);
```

### Frontend Error Display

```typescript
<TextInput
  label="Email"
  value={data.email}
  onChange={(e) => setData('email', e.target.value)}
  error={errors.email}
  required
/>
```

### Custom Error Messages

```php
$request->validate([
    'volume' => 'required|unique:loa_volume,volume',
], [
    'volume.unique' => 'This volume already exists. Please use a different name.',
]);
```

---

## 9. Notifications

### Success Notification

```typescript
notifications.show({
  message: 'Operation completed successfully!',
  color: 'green',
});
```

### Error Notification

```typescript
notifications.show({
  message: 'An error occurred. Please try again.',
  color: 'red',
});
```

### Flash Messages (from Backend)

```php
return redirect()->back()->with('success', 'Record updated successfully.');
```

---

## 10. Status Badges

### Pattern

Color-coded badges for status display.

### Badge Implementation

```typescript
const getStatusBadge = (status: string) => {
  const statusMap = {
    paid: { color: 'green', label: 'Paid' },
    pending: { color: 'yellow', label: 'Pending' },
    cancelled: { color: 'red', label: 'Cancelled' },
    approved: { color: 'green', label: 'Approved' },
  };

  const config = statusMap[status] || { color: 'gray', label: status };
  return <Badge color={config.color}>{config.label}</Badge>;
};
```

### Common Status Types

- **Payment:** paid, pending_payment, failed, refunded, cancelled
- **LoA:** pending, approved, rejected
- **General:** active, inactive, archived

---

## 11. File Uploads

### Pattern

File input with validation and preview.

### Implementation

```typescript
<FileInput
  label="Upload File"
  accept="application/pdf"
  onChange={(file) => setData('file', file)}
  error={errors.file}
  required
/>
```

### Backend Handling

```php
if ($request->hasFile('file')) {
    $path = $request->file('file')->store('uploads', 'public');
    $data['file_path'] = $path;
}
```

---

## 12. Soft Deletes

### Pattern

All major models use soft deletes for data recovery.

### Model Setup

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Model extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
```

### Delete Operation

```php
$model->delete(); // Soft delete (sets deleted_at)
```

### Restore Operation

```php
$model->restore(); // Restore soft-deleted record
```

### Query Scopes

```php
Model::withTrashed()->get(); // Include soft-deleted
Model::onlyTrashed()->get(); // Only soft-deleted
```

---

## 13. Responsive Design

### Breakpoints (Mantine)

- **xs:** < 576px (mobile)
- **sm:** ≥ 576px (mobile landscape)
- **md:** ≥ 768px (tablet)
- **lg:** ≥ 992px (desktop)
- **xl:** ≥ 1200px (large desktop)

### Grid Responsive Pattern

```typescript
<Grid.Col span={{ base: 12, md: 6, lg: 4 }}>
  {/* Content */}
</Grid.Col>
```

---

## 14. Color Scheme

### Status Colors

- **Green:** Success, approved, paid
- **Yellow/Orange:** Warning, pending
- **Red:** Error, rejected, cancelled
- **Blue:** Info, neutral states
- **Gray:** Disabled, not assigned

### Action Colors

- **Primary (Blue):** Main actions
- **Green:** Success actions (approve, download)
- **Red:** Destructive actions (delete)
- **Gray:** Secondary actions (cancel)

---

## 15. Icons

### Library

Tabler Icons (`@tabler/icons-react`)

### Common Usage

- **IconPlus:** Add/Create
- **IconEdit:** Edit
- **IconTrash:** Delete
- **IconEye:** View
- **IconDownload:** Download
- **IconSearch:** Search
- **IconFilter:** Filter
- **IconArrowLeft:** Back
- **IconCheck:** Confirm/Success
- **IconX:** Close/Cancel

---

## Best Practices

1. **Consistency:** Follow established patterns for new features
2. **Accessibility:** Use semantic HTML and ARIA labels
3. **Performance:** Implement debouncing for search/filter
4. **User Feedback:** Always show loading states and notifications
5. **Error Handling:** Display clear error messages
6. **Validation:** Validate on both frontend and backend
7. **URL State:** Preserve filters/search in URL for sharing
8. **Responsive:** Test on mobile, tablet, and desktop
9. **Documentation:** Document custom patterns and variations
10. **Code Reuse:** Extract common components and utilities
