# SSO Project Documentation

## Table of Contents

- [Architecture Overview](#architecture-overview)
- [Project Structure](#project-structure)
- [Prerequisites](#prerequisites)
- [Setup Guide](#setup-guide)
- [How Full Flow Works](#how-sso-works)
- [Routes](#routes)
- [Troubleshooting](#troubleshooting)

## Architecture Overview

This project implements a centralized Single Sign-On (SSO) system using Laravel Passport. The architecture consists of three main components:



### Simple Box Diagram

```
┌──────────────────────┐
│      SSO Server      │
│   (Passport OAuth2)  │
└──────────┬───────────┘
           │
     ┌─────┴─────┐
     │           │
     ▼           ▼
┌─────────┐  ┌────────────┐
│Ecommerce│  │ Foodpanda  │
│   App   │  │    App     │
│         │  │            │
└─────────┘  └────────────┘

```

---


## Project Structure

```
task-one/
├── sso-server/          # Central OAuth2 authentication server
├── ecommerce-app/       # E-commerce client application
├── foodpanda-app/       # Food ordering client application
└── README.md            # This documentation
```

---

## Prerequisites

- PHP 8.4+
- Composer
- Laravel Herd (for local development)
- SQLite (default database)

---

## Setup Guide

### Step 1: Setup SSO Server

The SSO Server acts as the centralized authentication provider using Laravel Passport.

#### 1.1 Install Dependencies

```bash
cd sso-server
composer install
```

#### 1.2 Configure Environment

```bash
cp .env.example .env
```

Edit `.env` file:

```env
APP_NAME=SSO Server
APP_URL=https://sso-server.test
```

#### 1.3 Generate Application Key

```bash
php artisan key:generate
```

#### 1.4 Run Migrations

```bash
php artisan migrate
```

#### 1.5 Install Passport Keys

```bash
php artisan passport:keys
```

#### 1.6 Create OAuth Clients

For each client application, create an OAuth client:

```bash
php artisan passport:client
```

When prompted:

```
What should we name the client? [Ecommerce App]:
> ecommerce-client

Enter the redirect URI [http://localhost]:
> https://ecommerce-app.test/callback
```

This will output:
```
Personal access client created successfully.
Client ID: <uuid>
Client Secret: <random-string>
```

**Repeat this command for each client app** (Ecommerce App, Foodpanda App).

#### 1.7 Start SSO Server

The SSO server is served by Laravel Herd at: https://sso-server.test

---

### Step 2: Setup Client Applications

Both `ecommerce-app` and `foodpanda-app` follow the same setup process.

#### 2.1 Install Dependencies

```bash
# For Ecommerce App
cd ecommerce-app
composer install

# For Foodpanda App
cd foodpanda-app
composer install
```

#### 2.2 Configure Environment

```bash
# Ecommerce App
cd ecommerce-app
cp .env.example .env

# Foodpanda App
cd foodpanda-app
cp .env.example .env
```

#### 2.3 Generate Application Key

```bash
# Ecommerce App
php artisan key:generate

# Foodpanda App
php artisan key:generate
```

#### 2.4 Run Migrations

```bash
# Ecommerce App
php artisan migrate

# Foodpanda App
php artisan migrate
```

---

### Step 3: Configure SSO Credentials

#### 3.1 Copy Credentials from SSO Server

After creating OAuth clients in Step 1.6, you will have:

| App | Client ID | Client Secret | Redirect URI |
|-----|-----------|---------------|--------------|
| ecommerce-client | `019ce042-82ed-73cb-911a-6ca938a8f2c6` | `nPRniOqxRhtnhJYTkgdHpRJ21SgvQagkJch8Q4mk` | `https://ecommerce-app.test/callback` |
| foodpanda-client | `019ce053-0c28-700b-8a0c-10d9ee6ff0f2` | `UrAFWYhpzRGhcoht5qDZKj1bLNyFJ3LUxYaSpaP5` | `http://foodpanda-app.test/callback` |

#### 3.2 Update .env Files

**For ecommerce-app/.env:**

```env
# SSO Configuration
SSO_SERVER=https://sso-server.test
SSO_CLIENT_ID=019ce042-82ed-73cb-911a-6ca938a8f2c6
SSO_CLIENT_SECRET=nPRniOqxRhtnhJYTkgdHpRJ21SgvQagkJch8Q4mk
SSO_REDIRECT_URI=https://ecommerce-app.test/callback
```

**For foodpanda-app/.env:**

```env
# SSO Configuration
SSO_SERVER=https://sso-server.test
SSO_CLIENT_ID=019ce053-0c28-700b-8a0c-10d9ee6ff0f2
SSO_CLIENT_SECRET=UrAFWYhpzRGhcoht5qDZKj1bLNyFJ3LUxYaSpaP5
SSO_REDIRECT_URI=http://foodpanda-app.test/callback
```

#### 3.3 Clear Config Cache

```bash
# Ecommerce App
php artisan config:clear

# Foodpanda App
php artisan config:clear
```

---

## How SSO Works

This project implements the **OAuth 2.0 Authorization Code Flow**. The SSO server is the single source of truth for identity — client apps never store real passwords and always defer authentication to it.

---

### 1. Technology Stack

| Layer | Technology |
|---|---|
| SSO Server auth engine | **Laravel Passport** (full OAuth 2.0 server) |
| Client app auth guard | **Session** (`web` guard) — standard Laravel session |
| API resource guard | **Passport** (`auth:api` guard) — protects `/api/user` |
| Token format | **JWT-signed opaque tokens** (Passport RSA key pair) |
| Database | **SQLite** (configurable) |

---

### 2. Full Step-by-Step Login Flow

```
┌─────────────────────────────────────────────────────────────────────┐
│  Step 1 — User clicks "Login with SSO"                              │
│  GET /login  →  SSOController::redirectToSSO()                      │
│                                                                     │
│  - Generates a 40-char random $state token                          │
│  - Stores $state in the client's PHP session                        │
│  - Builds the authorization URL with:                               │
│      client_id, redirect_uri, response_type=code,                   │
│      scope='', state                                                │
│  - Redirects browser to:                                            │
│      https://sso-server.test/oauth/authorize?...                    │
└─────────────────────────────────────────────────────────────────────┘
            │ browser redirect
            ▼
┌─────────────────────────────────────────────────────────────────────┐
│  Step 2 — SSO Server receives authorization request                 │
│  GET /oauth/authorize  (handled by Laravel Passport)                │
│                                                                     │
│  - If user is NOT logged in → redirects to /login                   │
│  - User submits email + password                                    │
│  - LoginRequest validates credentials, enforces rate limiting       │
│    (5 attempts per email+IP before lockout)                         │
│  - On success: session is regenerated (session fixation protection) │
└─────────────────────────────────────────────────────────────────────┘
            │ authenticated session established
            ▼
┌─────────────────────────────────────────────────────────────────────┐
│  Step 3 — Consent / Authorization screen                            │
│                                                                     │
│  PassportClient::skipsAuthorization() returns TRUE for:             │
│    - ecommerce-client                                               │
│    - foodpanda-client                                               │
│    - foodpanda-client-new                                           │
│                                                                     │
│  → Consent screen is SKIPPED for these trusted first-party clients  │
│  → Passport immediately issues an authorization code                │
│  → Stores the code in `oauth_auth_codes` table (expires in ~10 min) │
│  → Redirects browser to client's redirect_uri with:                 │
│      ?code=<auth_code>&state=<original_state>                       │
└─────────────────────────────────────────────────────────────────────┘
            │ browser redirect back to client
            ▼
┌─────────────────────────────────────────────────────────────────────┐
│  Step 4 — Client handles the callback                               │
│  GET /callback  →  SSOController::callback()                        │
│                                                                     │
│  a) CSRF check: $request->state == session('state')                 │
│     Aborts with 403 if they don't match                             │
│                                                                     │
│  b) Code exchange (server-to-server POST, never exposed to browser):│
│     POST https://sso-server.test/oauth/token                        │
│       grant_type=authorization_code                                 │
│       client_id + client_secret + redirect_uri + code               │
│     ← Returns: { access_token, token_type, expires_in, ... }       │
│     Passport marks the auth code revoked in `oauth_auth_codes`      │
│     Passport writes the access token to `oauth_access_tokens`       │
│                                                                     │
│  c) Fetch identity — authenticated server-to-server call:           │
│     GET https://sso-server.test/api/user                            │
│       Authorization: Bearer <access_token>                          │
│     ← Returns: { id, name, email, email_verified_at, ... }          │
│     (Protected by auth:api / Passport guard on the SSO server)      │
│                                                                     │
│  d) Local user provisioning:                                        │
│     User::updateOrCreate(['email' => $ssoUser['email']], [...])     │
│     - Creates local user if first login                             │
│     - Updates name if it changed                                    │
│     - Sets a random 16-char bcrypt password (unusable locally)      │
│                                                                     │
│  e) Auth::login($user)  — establishes a local Laravel session       │
│  f) access_token saved in session for later logout use              │
│  g) Redirects to /dashboard                                         │
└─────────────────────────────────────────────────────────────────────┘
```

---

### 3. Key Server Components

#### SSO Server — `PassportClient` Model

The SSO server uses a custom Passport client model that auto-approves trusted clients, removing the need for an explicit consent screen:

```php
// sso-server/app/Models/PassportClient.php
class PassportClient extends BaseClient
{
    public $keyType = 'string';     // UUID primary keys
    public $incrementing = false;

    public function skipsAuthorization(Authenticatable $user, array $scopes): bool
    {
        return in_array($this->name, [
            'ecommerce-client',
            'foodpanda-client',
            'foodpanda-client-new'
        ]);
    }
}
```

This is registered in `AppServiceProvider`:

```php
Passport::useClientModel(PassportClient::class);
Passport::authorizationView('passport.authorize');  // custom consent UI
```

#### SSO Server — API Guard

The `auth:api` guard on the SSO server uses Passport to validate Bearer tokens:

```php
// sso-server/config/auth.php
'guards' => [
    'web' => ['driver' => 'session', 'provider' => 'users'],
    'api' => ['driver' => 'passport', 'provider' => 'users'],  // ← Passport
],
```

The `/api/user` and `/api/logout` routes are both protected by `auth:api`:

```php
// sso-server/routes/api.php
Route::middleware('auth:api')->get('/user', fn(Request $r) => $r->user());
Route::middleware('auth:api')->post('/logout', function (Request $request) {
    $request->user()->token()->revoke();   // revokes the specific access token
    return response()->json(['message' => 'Logged out successfully']);
});
```

#### SSO Server — Authentication

Login is handled by `AuthenticatedSessionController` using `LoginRequest`, which includes:
- Email + password validation
- **Rate limiting**: 5 attempts per `email|ip` before lockout (with time-based unlock)
- Session regeneration after successful login (prevents session fixation attacks)

> **Note:** Registration routes are commented out in `routes/auth.php`. New users can only be created via the database seeder (`UserSeeder` creates `user@gmail.com` / `password`). A demo credentials banner is shown on the login page.

---

### 4. Client-Side Implementation (`SSOController`)

Both `ecommerce-app` and `foodpanda-app` share identical `SSOController` logic:

#### Step 1 — Redirect to SSO

```php
public function redirectToSSO(Request $request)
{
    $state = Str::random(40);
    session(['state' => $state]);

    $query = http_build_query([
        'client_id'     => config('app.sso_client_id'),
        'redirect_uri'  => config('app.sso_redirect_uri'),
        'response_type' => 'code',
        'scope'         => '',
        'state'         => $state,
    ]);

    return redirect(config('app.sso_server') . '/oauth/authorize?' . $query);
}
```

#### Step 2 — Handle Callback

```php
public function callback(Request $request)
{
    // CSRF protection — abort if state doesn't match
    if ($request->state != session('state')) {
        abort(403);
    }

    // Server-to-server code exchange (never exposed to browser)
    $response = Http::asForm()->post(config('app.sso_server') . '/oauth/token', [
        'grant_type'    => 'authorization_code',
        'client_id'     => config('app.sso_client_id'),
        'client_secret' => config('app.sso_client_secret'),
        'redirect_uri'  => config('app.sso_redirect_uri'),
        'code'          => $request->code,
    ]);

    $token = $response->json();

    // Fetch user identity from SSO server using the access token
    $userResponse = Http::withToken($token['access_token'])
        ->get(config('app.sso_server') . '/api/user');

    $ssoUser = $userResponse->json();

    // Provision local user record (create or update)
    $user = User::updateOrCreate(
        ['email' => $ssoUser['email']],
        [
            'name'     => $ssoUser['name'],
            'password' => bcrypt(Str::random(16)),  // random — not usable locally
        ]
    );

    Auth::login($user);

    // Store access token in session for SSO logout
    session(['access_token' => $token['access_token']]);

    return redirect()->route('dashboard');
}
```

---

### 5. Logout Flow

Logout is a three-step process to ensure the user is fully signed out from both the client and the SSO server:

```php
// ecommerce-app/routes/web.php
Route::get('/logout', function () {
    $token = session('access_token', 'none');

    // Step 1 — Revoke access token on SSO server
    if ($token && $token !== 'none') {
        Http::withToken($token)
            ->post(config('app.sso_server') . '/api/logout');
        // SSO server calls $request->user()->token()->revoke()
        // This marks the row in oauth_access_tokens as revoked=1
    }

    // Step 2 — Destroy local session
    Auth::logout();
    session()->flush();

    // Step 3 — Redirect to SSO server logout (clears SSO session cookie)
    return redirect(
        config('app.sso_server') . '/logout?return_url=' . urlencode(config('app.url') . '/')
    );
});
```

The SSO server's `/logout` route:
```php
// sso-server/routes/web.php
Route::get('/logout', function () {
    Auth::logout();
    $returnUrl = request()->query('return_url', '/');
    return redirect($returnUrl);  // sends browser back to the client app homepage
})->name('logout');
```

---

### 6. Database Tables

#### SSO Server Tables (Passport)

| Table | Purpose |
|---|---|
| `users` | User accounts (single source of truth) |
| `oauth_clients` | Registered client applications (UUID PK) |
| `oauth_auth_codes` | Short-lived authorization codes (one-time use) |
| `oauth_access_tokens` | Issued access tokens (revocable) |
| `oauth_refresh_tokens` | Refresh tokens linked to access tokens |
| `oauth_device_codes` | Device authorization flow codes |
| `sessions` | Server-side web sessions |

#### Client App Tables

| Table | Purpose |
|---|---|
| `users` | Local mirror of SSO user data (email, name, random password) |
| `sessions` | Local web sessions |

> **ecommerce-app only:** Has an additional `sso_id` nullable column on `users` (migration `2026_03_12_034453_add_sso_id_to_users`). This is not present in `foodpanda-app`.

---

### 7. SSO Configuration

Client apps read all SSO settings from environment variables via `config/app.php`:

```php
// ecommerce-app/config/app.php  (foodpanda-app is identical)
'sso_server'        => env('SSO_SERVER'),
'sso_client_id'     => env('SSO_CLIENT_ID'),
'sso_client_secret' => env('SSO_CLIENT_SECRET'),
'sso_redirect_uri'  => env('SSO_REDIRECT_URI'),
```

The SSO server uses `config/passport.php` to locate its RSA keys:

```php
// sso-server/config/passport.php
'private_key' => env('PASSPORT_PRIVATE_KEY'),
'public_key'  => env('PASSPORT_PUBLIC_KEY'),
```

---

### 8. Security Mechanisms Summary

| Mechanism | Where | What it protects against |
|---|---|---|
| `state` parameter (random 40 chars) | Client `SSOController` | CSRF on the OAuth callback |
| Server-to-server token exchange | Client `SSOController::callback()` | Authorization code interception (code never sent to browser after exchange) |
| Rate limiting (5 attempts / email+IP) | SSO `LoginRequest` | Brute-force password attacks |
| Session regeneration after login | SSO `LoginRequest::authenticate()` | Session fixation attacks |
| Token revocation on logout | SSO `POST /api/logout` | Token reuse after logout |
| Random 16-char local password | Client `updateOrCreate()` | Direct login bypass on client apps |
| `auth:api` (Passport) on `/api/user` | SSO | Unauthenticated identity disclosure |
| HTTPS enforced by Herd (`.test` domains) | Infrastructure | Man-in-the-middle attacks |

---

## Routes

### SSO Server Routes

| Method | URI | Middleware | Description |
|--------|-----|------------|-------------|
| GET | `/` | — | Welcome page |
| GET | `/login` | `guest` | Login form |
| POST | `/login` | `guest` | Process login credentials |
| GET | `/forgot-password` | `guest` | Password reset request |
| POST | `/forgot-password` | `guest` | Send reset link email |
| GET | `/reset-password/{token}` | `guest` | Reset password form |
| POST | `/reset-password` | `guest` | Process password reset |
| GET | `/oauth/authorize` | `auth` (Passport) | OAuth authorization endpoint |
| POST | `/oauth/authorize` | `auth` (Passport) | Approve authorization |
| DELETE | `/oauth/authorize` | `auth` (Passport) | Deny authorization |
| POST | `/oauth/token` | — | Exchange code for access token |
| GET | `/api/user` | `auth:api` (Passport) | Get authenticated user profile |
| POST | `/api/logout` | `auth:api` (Passport) | Revoke access token |
| GET/POST | `/logout` | — | Clear SSO server session |
| GET | `/dashboard` | `auth`, `verified` | Authenticated user dashboard |
| GET/PATCH | `/profile` | `auth` | View / update user profile |
| DELETE | `/profile` | `auth` | Delete account |

> **Note:** Registration routes (`/register`) are commented out. New accounts must be created via the database seeder.

### Client Application Routes

| Method | URI | Middleware | Description |
|--------|-----|------------|-------------|
| GET | `/` | — | Welcome / home page |
| GET | `/login` | — | Redirects to SSO server (`SSOController::redirectToSSO`) |
| GET | `/callback` | — | SSO callback handler (`SSOController::callback`) |
| GET | `/dashboard` | `auth` | Protected dashboard (requires local session) |
| GET | `/logout` | — | Revoke token, destroy session, redirect to SSO logout |

---

## Troubleshooting

### Common Issues

1. **Invalid Client Credentials**
   - Ensure `SSO_CLIENT_ID` and `SSO_CLIENT_SECRET` match the values from the SSO server
   - Run `php artisan config:clear` after updating `.env`

2. **Redirect URI Mismatch**
   - Ensure `SSO_REDIRECT_URI` exactly matches what's registered in the SSO server

3. **SSL Certificate Errors**
   - If using HTTPS locally, ensure Laravel Herd is properly configured

4. **Database Errors**
   - Run migrations on all three applications
   - Check SQLite database files exist in `database/database.sqlite`

5. **"Table already exists" on SSO Server Migration**
   - The SSO server contains two sets of Passport migration files (`032901–032905` and `035742–035746`) with identical schemas
   - If you hit this error, delete the duplicate set (the earlier-timestamped `032901–032905` files) before running `php artisan migrate`

6. **State Mismatch (403 on callback)**
   - This occurs if the session expired between the redirect and the callback, or if cookies are blocked
   - Ensure your browser accepts cookies for the client app domain

7. **Login Lockout**
   - The SSO server rate-limits to 5 failed attempts per email+IP address
   - Wait for the lockout period to expire, or clear the `cache` table: `php artisan cache:clear`

### Testing SSO

1. Start all three applications via Laravel Herd
2. Visit `https://ecommerce-app.test` or `http://foodpanda-app.test`
3. Click "Login" — you should be redirected to the SSO server
4. Log in with `user@gmail.com` / `password` (seeded demo account)
5. After successful authentication, you should be redirected back to the client app dashboard
6. To test cross-app SSO: open the second client app without logging out — you should be authenticated immediately without re-entering credentials (single sign-on)
7. Click "Logout" to verify the full three-step logout flow clears both the client session and the SSO server session

---
