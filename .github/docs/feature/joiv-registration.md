# JOIV Registration Feature Documentation

## Overview

This feature allows users to register articles for JOIV (International Journal on Informatics Visualization) with payment integration and admin management capabilities.

## Features Implemented

### 1. Public Registration Form

- **Route:** `/joiv/registration`
- **Access:** Public (no authentication required)
- **Fields:**
  - First Name
  - Last Name
  - Email Address (unique validation)
  - Phone Number
  - Institution
  - Country
  - Paper ID (optional)
  - Paper Title
  - Full Paper (file upload - PDF, DOC, DOCX, max 50MB)

### 2. Payment Integration

- **Payment Methods:**
  - PayPal (payment gateway)
  - Bank Transfer (with payment proof upload)
- **Payment Flow:**
  1. User fills registration form
  2. System generates unique registration ID
  3. User selects payment method
  4. For PayPal: redirected to PayPal for payment
  5. For Bank Transfer: uploads payment proof
  6. Admin verifies payment
- **Payment Status:**
  - Pending Payment
  - Paid
  - Cancelled
  - Refunded

### 3. Admin Management

- **Menu:** "Joiv Article" in admin panel
- **Features:**
  - View all registrations in paginated table
  - Search by name, email, institution, paper title
  - Filter by country and payment status
  - View detailed registration information
  - Download full paper
  - Download payment proof
  - Download receipt (for paid registrations)
  - Update payment status
  - Export to Excel
  - Summary statistics (Paid, Pending, Cancelled, Refunded)

## Database Schema

### Table: `joiv_registrations`

```sql
- id (bigint, primary key)
- first_name (string)
- last_name (string)
- email_address (string, unique)
- phone_number (string)
- institution (string)
- country (string, 2 chars)
- paper_id (string, nullable)
- paper_title (string)
- full_paper_path (string, nullable)
- payment_status (enum: pending_payment, paid, cancelled, refunded)
- payment_method (enum: transfer_bank, payment_gateway, nullable)
- payment_proof_path (string, nullable)
- paid_fee (decimal)
- public_id (string, unique)
- created_by (bigint, foreign key to users, nullable)
- updated_by (bigint, foreign key to users, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable - soft deletes)
```

### Table: `invoice_history` (updated)

- Added `joiv_registration_id` (foreign key to joiv_registrations)

## File Structure

### Backend

```
app/
├── Models/
│   └── JoivRegistration.php
├── Http/Controllers/
│   ├── JoivRegistrationController.php (public)
│   └── Admin/
│       └── JoivArticleController.php
└── Exports/
    └── JoivRegistrationExport.php
```

### Frontend

```
resources/js/Pages/
├── Joiv/
│   ├── Registration/
│   │   └── Index.tsx
│   └── Payment/
│       ├── Index.tsx
│       └── Complete.tsx
└── Admin/
    └── JoivArticles/
        ├── Index.tsx
        └── Show.tsx
```

### Views (PDF Templates)

```
resources/views/joiv/
└── receipt.blade.php
```

## API Endpoints

### Public Routes

- `GET /joiv/registration` - Show registration form
- `POST /joiv/registration` - Submit registration
- `GET /joiv/registration/{public_id}/payment` - Show payment page
- `POST /joiv/registration/{public_id}/payment` - Process payment
- `GET /joiv/registration/{public_id}/payment/complete` - Payment complete page
- `GET /joiv/registration/{public_id}/paypal/success` - PayPal success callback
- `GET /joiv/registration/{public_id}/paypal/cancel` - PayPal cancel callback

### Admin Routes (requires authentication)

- `GET /joiv-articles` - List all registrations
- `GET /joiv-articles/{id}` - View registration details
- `PATCH /joiv-articles/{id}/payment-status` - Update payment status
- `GET /joiv-articles/{id}/download-paper` - Download full paper
- `GET /joiv-articles/{id}/download-payment-proof` - Download payment proof
- `GET /joiv-articles/{id}/download-receipt` - Download receipt PDF
- `GET /joiv-articles/export/excel` - Export to Excel
- `DELETE /joiv-articles/{id}` - Soft delete registration
- `PUT /joiv-articles/{id}/restore` - Restore deleted registration

## How to Use

### For Users (Public)

1. **Register Article:**
   - Navigate to `/joiv/registration`
   - Fill in all required fields
   - Upload full paper (PDF, DOC, or DOCX)
   - Click "Continue to Payment"

2. **Make Payment:**
   - Choose payment method:
     - **PayPal:** Click "Pay with PayPal" and complete payment on PayPal
     - **Bank Transfer:** Upload payment proof and click "Submit Payment"
   - Wait for payment confirmation

3. **Check Status:**
   - Check your email for confirmation
   - For bank transfer, wait for admin verification

### For Admins

1. **View Registrations:**
   - Navigate to "Joiv Article" menu
   - View summary statistics
   - Use search and filters to find specific registrations

2. **Manage Registrations:**
   - Click "View" on any registration
   - Download paper, payment proof, or receipt
   - Update payment status if needed

3. **Export Data:**
   - Click "Export to Excel" to download all registrations
   - Apply filters before exporting for specific data

## Payment Configuration

### Fixed Registration Fee

- **Amount:** $150.00 USD

### Bank Transfer Information

Update bank details in: `resources/js/Pages/Joiv/Payment/Index.tsx`

```typescript
// Current settings:
Bank Name: Bank Central Asia (BCA)
Account Number: 1234567890
Account Name: JOIV Registration
```

### PayPal Configuration

Ensure PayPal credentials are set in `.env`:

```
PAYPAL_MODE=sandbox (or live)
PAYPAL_SANDBOX_CLIENT_ID=your_client_id
PAYPAL_SANDBOX_SECRET=your_secret
PAYPAL_LIVE_CLIENT_ID=your_client_id
PAYPAL_LIVE_SECRET=your_secret
```

## Security Features

- Email uniqueness validation
- Phone number format validation
- File type and size validation
- CSRF protection on all forms
- Authentication required for admin routes
- Soft deletes for data recovery
- Audit trail (created_by, updated_by)

## Future Enhancements

Possible improvements:

1. Email notifications on registration and payment confirmation
2. Bulk payment status updates
3. Payment reminders for pending payments
4. Advanced analytics and reporting
5. Integration with manuscript submission system
6. Multi-currency support
7. Discount codes/coupons
8. Batch paper downloads

## Troubleshooting

### Common Issues

1. **File Upload Fails:**
   - Check file size (max 50MB)
   - Check file type (PDF, DOC, DOCX only)
   - Ensure storage symlink is created: `php artisan storage:link`

2. **PayPal Payment Fails:**
   - Verify PayPal credentials in `.env`
   - Check PayPal mode (sandbox/live)
   - Review Laravel logs: `storage/logs/laravel.log`

3. **Email Not Unique Error:**
   - Each email can only register once
   - Contact admin if re-registration needed

## Support

For technical issues or questions:

- Check application logs: `storage/logs/laravel.log`
- Review PayPal integration logs
- Contact development team

---

**Version:** 1.0.0  
**Last Updated:** December 6, 2025  
**Feature Branch:** `feature/joiv_registration`
