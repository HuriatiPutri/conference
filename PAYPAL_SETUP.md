# PayPal Sandbox Integration Setup

## Environment Variables

Add these to your `.env` file:

```env
# PayPal Configuration
PAYPAL_CLIENT_ID=your_sandbox_client_id_here
PAYPAL_CLIENT_SECRET=your_sandbox_client_secret_here
PAYPAL_MODE=sandbox
```

## How to Get PayPal Sandbox Credentials

1. **Login to PayPal Developer**: https://developer.paypal.com/
2. **Create App**: Go to "My Apps & Credentials"
3. **Sandbox App**: Create a new app for sandbox testing
4. **Get Credentials**: Copy Client ID and Client Secret
5. **Add to .env**: Paste credentials in your environment file

## PayPal Sandbox URLs

- **API Base URL**: https://api.sandbox.paypal.com
- **Checkout URL**: https://www.sandbox.paypal.com

## ⚠️ Common Issues & Troubleshooting

### Issue: "URL tidak terbuka" / PayPal URL not opening

**Causes:**

1. **Invalid Credentials**: Client ID and Client Secret are not valid
2. **Same Values**: Client ID and Secret should be different values
3. **Wrong Environment**: Using live credentials in sandbox mode

**Solutions:**

1. **Verify Credentials**: Check PayPal Developer Dashboard for correct values
2. **Check .env File**: Ensure PAYPAL_CLIENT_ID ≠ PAYPAL_CLIENT_SECRET
3. **Clear Cache**: Run `php artisan config:clear` after updating .env
4. **Test Access Token**: Use tinker to test PayPal service

### Issue: "Failed to get PayPal access token"

**Debug Steps:**

```bash
# Check PayPal configuration
php artisan tinker --execute="
echo 'Client ID: ' . config('paypal.client_id') . PHP_EOL;
echo 'Client Secret: ' . (config('paypal.client_secret') ? 'Set' : 'Not set') . PHP_EOL;
echo 'Mode: ' . config('paypal.mode') . PHP_EOL;
"

# Test PayPal service
php artisan tinker --execute="
try {
    \$service = new App\Services\PayPalService();
    echo 'PayPal Service works!' . PHP_EOL;
} catch (\Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"
```

### Issue: "INVALID_RESOURCE_ID" Error

This usually means:

1. Payment ID is not valid
2. Using wrong PayPal environment (sandbox vs live)
3. Payment was not created successfully

## Testing PayPal Payment Flow

### 1. Registration Process

```
User fills registration form
↓
Selects "Pay with PayPal"
↓
System creates PayPal payment
↓
Redirects to PayPal sandbox
↓
User logs in with sandbox account
↓
Approves payment
↓
PayPal redirects back to app
↓
System verifies payment
↓
Creates audience record with "paid" status
↓
Shows success page
```

### 2. PayPal Sandbox Test Accounts

Create test accounts in PayPal Developer Dashboard:

- **Personal Account**: For buyers/users
- **Business Account**: For merchants/sellers

### 3. Test URLs

**Registration URL Format:**

```
http://localhost:8000/registration/CONF2024001
```

**PayPal Return URL:**

```
http://localhost:8000/registration/CONF2024001/paypal/return
```

**PayPal Cancel URL:**

```
http://localhost:8000/registration/CONF2024001/paypal/cancel
```

## Error Handling

The system handles these PayPal scenarios:

1. **Invalid Credentials**: Shows error message
2. **Payment Cancelled**: Redirects back to payment page
3. **Payment Failed**: Shows error with retry option
4. **Session Expired**: Redirects to registration start

## Production Deployment

For production, change these settings:

```env
PAYPAL_MODE=live
PAYPAL_CLIENT_ID=your_live_client_id
PAYPAL_CLIENT_SECRET=your_live_client_secret
```

And use live PayPal credentials from PayPal business account.

## Debugging

Check Laravel logs for PayPal API responses:

```bash
tail -f storage/logs/laravel.log
```

PayPal API errors will be logged with full request/response details.
