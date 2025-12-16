# Payroll System

A Laravel-based payroll system for tracking Stripe charges, refunds, and coach commissions.

## Features

- **Stripe Data Integration**: API endpoints to receive charges and refunds from Stripe via N8N
- **Client Management**: Track clients by email address
- **Coach Management**: Manage coaches and assign them to clients
- **Commission Tracking**: Set commission rates per client-coach relationship (percentage-based)
- **One-Off Cash Ins**: Track one-off cash amounts attributed to coaches
- **Dashboard**: View charges, refunds, and coach commission summaries
- **Authentication**: Secure login system

## Setup

1. **Install Dependencies**
   ```bash
   composer install
   ```

2. **Environment Setup**
   Copy `.env.example` to `.env` (if it doesn't exist) and configure:
   ```bash
   php artisan key:generate
   ```

3. **Database Setup**
   ```bash
   php artisan migrate
   ```

4. **API Token Configuration**
   Add to your `.env` file:
   ```
   API_TOKEN=your-secure-api-token-here
   ```
   This token will be used by N8N to authenticate API requests (bearer token).

5. **Create Admin User**
   Register via the `/register` route or use tinker:
   ```bash
   php artisan tinker
   ```
   ```php
   App\Models\User::create([
       'name' => 'Admin',
       'email' => 'admin@example.com',
       'password' => Hash::make('password')
   ]);
   ```

## API Endpoints

All API endpoints require Bearer token authentication. Set the `Authorization` header:
```
Authorization: Bearer your-api-token-from-env
```

### Store Charge
`POST /api/stripe/charge`

Expected payload:
```json
{
  "date": "2024-01-15",
  "net": 100.00,
  "program": "Program Name",
  "email_address": "client@example.com",
  "client_name": "Client Name",
  "amount_charged": 110.00,
  "stripe_url": "https://dashboard.stripe.com/...",
  "client_stripe_id": "cus_xxxxx",
  "billing_information_is_included": true,
  "country": "US",
  "id": "ch_xxxxx",
  "transaction_id": "txn_xxxxx",
  "coach": "Coach Name"
}
```

### Store Refund
`POST /api/stripe/refund`

Expected payload:
```json
{
  "date": "2024-01-20",
  "amount": 50.00,
  "email_address": "client@example.com",
  "client_name": "Client Name",
  "stripe_refund_id": "re_xxxxx",
  "transaction_id": "txn_xxxxx",
  "charge_id": "ch_xxxxx",
  "program": "Program Name",
  "notes": "Refund notes"
}
```

## Commission System

- Coaches are assigned to clients (not just charges)
- Each client-coach relationship has a commission rate (percentage, e.g., 15.00 for 15%)
- Commission is calculated from the NET amount of charges
- One-off cash ins are tracked separately and added to coach totals

## Usage

1. **Login** at `/login`
2. **Dashboard** (`/dashboard`) shows:
   - Total charges and refunds
   - Recent transactions
   - Top coaches by commission

3. **Manage Coaches** (`/coaches`):
   - Create coaches
   - Assign clients to coaches
   - Set commission rates per client

4. **Manage Clients** (`/clients`):
   - View all clients
   - Clients are automatically created when charges are received via API

5. **One-Off Cash Ins** (`/one-off-cash-ins`):
   - Add one-off cash amounts for coaches
   - Edit or delete entries

## Notes

- All amounts are stored in decimal format
- Commission rates are stored as percentages (e.g., 15.00 = 15%)
- Charges are linked to clients by email address
- The system uses Tailwind CSS via CDN (no NPM build required)





