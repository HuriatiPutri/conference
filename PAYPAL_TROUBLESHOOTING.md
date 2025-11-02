# PayPal Integration Troubleshooting Guide

## Common Issues When Deploying to Server

### 1. Environment Variables Not Set

The most common cause of "Payment processing failed" on server is missing PayPal environment variables.

**Check these variables in your server's `.env` file:**

```bash
# For Sandbox (Development/Testing)
PAYPAL_MODE=sandbox
PAYPAL_SANDBOX_CLIENT_ID=your_sandbox_client_id
PAYPAL_SANDBOX_CLIENT_SECRET=your_sandbox_client_secret

# For Live (Production)
PAYPAL_MODE=live
PAYPAL_LIVE_CLIENT_ID=your_live_client_id
PAYPAL_LIVE_CLIENT_SECRET=your_live_client_secret
```

### 2. Test PayPal Configuration

Run this command on your server to test PayPal connectivity:

```bash
php artisan paypal:test
```

This will check:

- Environment variables are set
- PayPal API connectivity
- Authentication with PayPal

### 3. Check Laravel Logs

Monitor the logs while testing payments:

```bash
tail -f storage/logs/laravel.log
```

Look for errors related to:

- `PayPal access token request failed`
- `PayPal payment creation failed`
- `Failed to get PayPal access token`

### 4. Common Server Configuration Issues

#### A. SSL/TLS Issues

If your server has SSL certificate issues, add this to your `.env`:

```bash
CURL_CA_BUNDLE=/path/to/ca-bundle.crt
```

Or disable SSL verification (NOT recommended for production):

```bash
PAYPAL_DISABLE_SSL_VERIFY=true
```

#### B. Firewall/Network Issues

Ensure your server can reach PayPal APIs:

**Sandbox URLs:**

- https://api.sandbox.paypal.com

**Live URLs:**

- https://api.paypal.com

Test connectivity:

```bash
curl -I https://api.sandbox.paypal.com
```

#### C. PHP Extensions

Ensure these PHP extensions are installed:

- `curl`
- `json`
- `openssl`

Check with:

```bash
php -m | grep -E "(curl|json|openssl)"
```

### 5. Debugging Steps

1. **Test locally first** - Ensure PayPal works in your local environment
2. **Check server .env** - Verify all PayPal environment variables are set
3. **Run test command** - Use `php artisan paypal:test` to verify configuration
4. **Check logs** - Monitor `storage/logs/laravel.log` for specific errors
5. **Test API connectivity** - Ensure server can reach PayPal APIs

### 6. Environment Variable Examples

Copy the appropriate section to your server's `.env` file:

#### For Sandbox Testing:

```bash
PAYPAL_MODE=sandbox
PAYPAL_SANDBOX_CLIENT_ID=AQlMUgjyUmRjdOGmKjH4mUIjmAmw0YGdGt3jh-UX8GvCCe7gVNQR_w8gR1OJjPfwRkJqo4PbmGoOX8nH
PAYPAL_SANDBOX_CLIENT_SECRET=EMVkzqAZGrk8P1jQKJzQSa3mLGWmpjsaYbh3oPfJ7zZ_Fkrz-YzCGpGF1OL8xKzlGfV5nO_y6kSXvQM1
```

#### For Production:

```bash
PAYPAL_MODE=live
PAYPAL_LIVE_CLIENT_ID=your_live_client_id_from_paypal_dashboard
PAYPAL_LIVE_CLIENT_SECRET=your_live_client_secret_from_paypal_dashboard
```

### 7. After Making Changes

After updating environment variables:

1. Clear config cache:

```bash
php artisan config:clear
```

2. Clear application cache:

```bash
php artisan cache:clear
```

3. Test again:

```bash
php artisan paypal:test
```

### 8. Getting PayPal Credentials

1. Go to [PayPal Developer](https://developer.paypal.com)
2. Log in to your PayPal account
3. Create an app for your website
4. Copy the Client ID and Client Secret
5. Use Sandbox credentials for testing, Live credentials for production

### 9. Contact Support

If issues persist, provide this information:

- Output of `php artisan paypal:test`
- Relevant lines from `storage/logs/laravel.log`
- Your PayPal app configuration (without sharing credentials)
- Server environment details (PHP version, OS, etc.)
