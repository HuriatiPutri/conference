# JOIV Registration Fee Management

## Overview

The JOIV Registration Fee Management system allows administrators to dynamically configure and track the registration fee for JOIV article submissions in two currencies: USD for international participants and IDR for Indonesian participants. The system maintains a complete history of all fee changes, showing who made each change and when.

## Key Features

- **Dual Currency Support**: Separate fees for USD (international) and IDR (Indonesia)
- **Automatic Currency Selection**: System automatically selects currency based on participant's country
- **Dynamic Fee Configuration**: Administrators can update both currency fees at any time
- **Complete History Tracking**: Every fee change creates a new record (no updates to existing records)
- **Audit Trail**: Tracks who changed the fee and when
- **Automatic Application**: New registrations automatically use the current fee from the database based on their country
- **Fallback Defaults**: If no fee is set, defaults to $150.00 USD or Rp 2,250,000.00 IDR

## Database Schema

### `joiv_registration_fees` Table

| Column       | Type            | Description                                                       |
| ------------ | --------------- | ----------------------------------------------------------------- |
| `id`         | bigint          | Primary key                                                       |
| `usd_amount` | decimal(10,2)   | Registration fee amount in USD for international participants     |
| `idr_amount` | decimal(15,2)   | Registration fee amount in IDR for Indonesian participants        |
| `notes`      | text (nullable) | Notes about the fee change                                        |
| `created_by` | bigint          | Foreign key to users table (who created this fee)                 |
| `created_at` | timestamp       | When this fee was created                                         |
| `updated_at` | timestamp       | Last update timestamp                                             |

**Indexes:**

- Primary key on `id`
- Index on `created_at` (for efficiently finding the latest fee)
- Foreign key on `created_by` references `users(id)` with cascade delete

## Backend Implementation

### Model: `JoivRegistrationFee`

**Location:** `app/Models/JoivRegistrationFee.php`

**Key Methods:**

```php
// Get the latest fee record
public static function getCurrentFee(): ?JoivRegistrationFee

// Get the current fee amount (returns decimal or 150.00 as fallback)
public static function getCurrentFeeAmount(): float

// Relationship to the user who created this fee
public function creator(): BelongsTo
```

**Usage Example:**

```php
// Get current fee amount
$fee = JoivRegistrationFee::getCurrentFeeAmount(); // Returns 150.00 or latest fee

// Get full fee record with creator info
$currentFee = JoivRegistrationFee::getCurrentFee();
if ($currentFee) {
    echo $currentFee->fee_amount;
    echo $currentFee->creator->full_name;
}
```

### Controller: `JoivArticleController`

**Location:** `app/Http/Controllers/Admin/JoivArticleController.php`

#### Fee Settings Page

```php
GET /joiv-articles/fee-settings
Method: feeSettings()
```

Returns Inertia page with:

- `currentFee`: Latest fee record with creator information
- `feeHistory`: Paginated list of all fee changes (10 per page, ordered by newest first)

#### Update Fee

```php
POST /joiv-articles/fee-settings
Method: updateFee()
```

**Request Body:**

```json
{
  "fee_amount": 200.0,
  "notes": "Increased fee for 2025 conference season"
}
```

**Validation:**

- `fee_amount`: Required, numeric, minimum 0
- `notes`: Optional, string, max 1000 characters

**Behavior:**

- Creates a new record in `joiv_registration_fees` table
- Sets `created_by` to authenticated user's ID
- Does NOT update existing records (insert-only for history)
- Redirects back to fee settings page with success message

### Registration Controller Update

**Location:** `app/Http/Controllers/JoivRegistrationController.php`

The `store()` method now uses dynamic fee:

```php
// Old (hardcoded):
$paidFee = 150.00; // USD

// New (dynamic from database):
$paidFee = JoivRegistrationFee::getCurrentFeeAmount();
```

## Frontend Implementation

### Fee Settings Page

**Location:** `resources/js/Pages/Admin/JoivArticles/FeeSettings.tsx`

**Route:** `/joiv-articles/fee-settings`

**Components:**

1. **Current Fee Display Card**
   - Shows active fee amount in large text
   - Displays last update date and user who made the change
   - Shows notes if available

2. **Update Fee Form Card**
   - Number input for fee amount with USD prefix
   - Textarea for optional notes
   - Submit button (disabled if no amount entered)
   - Loading state during submission

3. **Fee History Table Card**
   - Shows all past fees in reverse chronological order
   - Columns: Date, Amount, Changed By, Notes
   - Pagination info displayed at bottom

**Features:**

- Success/error notifications on form submission
- Form clears after successful update
- Responsive layout using Mantine Grid
- Formatted currency display
- Date formatting with locale support

### Navigation Integration

**Location:** `resources/js/Pages/Admin/JoivArticles/Index.tsx`

Added "Fee Settings" button to the header toolbar:

- Blue dollar icon button
- Positioned next to "Export to Excel" button
- Click navigates to fee settings page
- Tooltip shows "Fee Settings"

## Routes

```php
// Admin routes (require authentication)
Route::middleware(['auth'])->prefix('joiv-articles')->name('joiv-articles.')->group(function () {
    // Fee settings
    Route::get('/fee-settings', [JoivArticleController::class, 'feeSettings'])
        ->name('fee-settings');

    Route::post('/fee-settings', [JoivArticleController::class, 'updateFee'])
        ->name('fee-settings.update');

    // ... other JOIV article routes
});
```

## Initial Setup

### Migration

Run the migration to create the fee table:

```bash
php artisan migrate
```

This creates the `joiv_registration_fees` table.

### Seeder

Run the seeder to create the initial fee record:

```bash
php artisan db:seed --class=JoivRegistrationFeeSeeder
```

This creates an initial fee of $150.00 USD with the note "Initial JOIV registration fee".

**Note:** The seeder will skip if:

- No users exist in the database (warns you to create admin first)
- Fee records already exist (prevents duplicate initial fees)

## User Flow

### Admin Updates Fee

1. Admin navigates to "Joiv Article" from admin menu
2. Clicks the blue dollar icon button in the toolbar
3. Views current fee and complete history
4. Enters new fee amount (e.g., 175.00)
5. Optionally adds notes (e.g., "Increased for 2025 season")
6. Clicks "Update Fee" button
7. Receives success notification
8. New fee is immediately active for all future registrations
9. History table shows the new entry

### Public Registration Uses Current Fee

1. User visits `/joiv/registration` page
2. Fills out registration form
3. Submits form
4. Backend automatically:
   - Calls `JoivRegistrationFee::getCurrentFeeAmount()`
   - Gets latest fee from database (e.g., 175.00)
   - Creates registration with that fee
   - User sees correct amount on payment page

## Data Integrity

### Insert-Only Pattern

The system uses an **insert-only** pattern for fee changes:

- Each fee update creates a NEW row
- Existing rows are NEVER updated or deleted
- Complete audit trail is preserved forever
- Latest fee is determined by `created_at DESC LIMIT 1`

### Foreign Key Constraints

- `created_by` references `users(id)` with CASCADE delete
  - If admin user is deleted, their fee records are also deleted
  - This maintains referential integrity

### Indexes

- Primary key on `id` for fast lookups
- Index on `created_at` for efficient "latest fee" queries
- Uses `orderBy('created_at', 'desc')` to find current fee

## Example Scenarios

### Scenario 1: Initial System Setup

```sql
-- After running seeder
SELECT * FROM joiv_registration_fees;
-- Result:
-- id | fee_amount | currency | notes                        | created_by | created_at
-- 1  | 150.00     | USD      | Initial JOIV registration fee | 1         | 2025-12-06 14:00:00
```

### Scenario 2: Fee Increase

Admin updates fee to 175.00 with note "Price increase for 2025":

```sql
SELECT * FROM joiv_registration_fees ORDER BY created_at DESC;
-- Result:
-- id | fee_amount | currency | notes                      | created_by | created_at
-- 2  | 175.00     | USD      | Price increase for 2025    | 1         | 2025-12-06 15:30:00
-- 1  | 150.00     | USD      | Initial JOIV registration fee | 1      | 2025-12-06 14:00:00
```

Now `JoivRegistrationFee::getCurrentFeeAmount()` returns `175.00`

### Scenario 3: Multiple Updates

```sql
SELECT * FROM joiv_registration_fees ORDER BY created_at DESC;
-- Result:
-- id | fee_amount | currency | notes                   | created_by | created_at
-- 4  | 200.00     | USD      | Final price for 2025    | 1         | 2025-12-07 10:00:00
-- 3  | 180.00     | USD      | Adjusted pricing        | 1         | 2025-12-06 18:00:00
-- 2  | 175.00     | USD      | Price increase for 2025 | 1         | 2025-12-06 15:30:00
-- 1  | 150.00     | USD      | Initial fee             | 1         | 2025-12-06 14:00:00
```

Complete history preserved, current fee is $200.00

## Testing Checklist

- [ ] Run migration successfully
- [ ] Run seeder creates initial fee
- [ ] Fee settings page loads without errors
- [ ] Current fee displays correctly
- [ ] Update fee form validation works
- [ ] New fee is created (not updated) on submit
- [ ] Fee history table shows all records
- [ ] Pagination works if more than 10 records
- [ ] New registration uses current fee from database
- [ ] Creator name displays correctly in history
- [ ] Date formatting displays correctly
- [ ] Dollar icon button navigates to fee settings
- [ ] Success/error notifications appear

## Troubleshooting

### Fee Not Updating for Registrations

**Problem:** New registrations still use old fee

**Solutions:**

1. Clear application cache: `php artisan cache:clear`
2. Check database: `SELECT * FROM joiv_registration_fees ORDER BY created_at DESC LIMIT 1;`
3. Verify `JoivRegistrationFee::getCurrentFeeAmount()` returns expected value

### No Initial Fee

**Problem:** No fee records exist in database

**Solution:**

```bash
php artisan db:seed --class=JoivRegistrationFeeSeeder
```

### Fee History Not Displaying

**Problem:** Fee history table is empty on fee settings page

**Solutions:**

1. Check controller returns paginated data
2. Verify relationship loads creator: `$fee->load('creator')`
3. Check frontend receives `feeHistory.data` array

### User Deleted, Fees Disappeared

**Problem:** Fee records deleted when admin user is deleted

**Explanation:** This is expected behavior due to CASCADE delete on `created_by` foreign key

**Prevention:** Consider using soft deletes for users or changing FK constraint to SET NULL

## Future Enhancements

Possible improvements:

1. **Currency Support**: Allow different currencies beyond USD
2. **Effective Date**: Add "effective from" date for future price changes
3. **Bulk Import**: Import fee schedule from CSV
4. **Fee Tiers**: Different fees based on country or membership status
5. **Approval Workflow**: Require approval before fee changes take effect
6. **Notifications**: Email admins when fee is changed
7. **Reporting**: Analytics on revenue impact of fee changes
8. **Fee Schedule**: Display upcoming scheduled fee changes

## Related Documentation

- [JOIV Registration Feature](./joiv-registration.md)
- [Payment Processing](./payment-processing.md)
- [Admin Panel Features](./admin-features.md)
