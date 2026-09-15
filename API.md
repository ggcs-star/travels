# Travels API v1

Base URL:

`/api/v1`

Authenticated endpoints use:

`Authorization: Bearer YOUR_API_TOKEN`

## Public

| Method | Endpoint | Purpose |
|---|---|---|
| GET | `/settings/public` | Safe public website settings |
| GET | `/categories` | Active tour categories |
| GET | `/destinations` | Available tour destinations |
| GET | `/tours` | Paginated available tours |
| GET | `/tours/{slug}` | Tour details and bookable departures |
| GET | `/departures/{id}` | Departure availability |
| GET | `/blogs` | Paginated published blogs |
| GET | `/blogs/{slug}` | Blog details |
| GET | `/pages/{slug}` | Published CMS page |
| POST | `/contact` | Submit contact enquiry |

### Tour filters

`GET /tours?search=kashmir&category=holidays&featured=1&per_page=20`

### Blog filters

`GET /blogs?search=kashmir&per_page=20`

## Authentication

| Method | Endpoint | Auth |
|---|---|---|
| POST | `/auth/register` | Public |
| POST | `/auth/login` | Public |
| POST | `/auth/forgot-password` | Public |
| GET | `/auth/me` | Bearer |
| POST | `/auth/logout` | Bearer |
| POST | `/auth/logout-all` | Bearer |
| POST | `/auth/resend-verification` | Bearer |

### Register body

```json
{
  "name": "John Doe",
  "username": "john.doe",
  "email": "john@example.com",
  "password": "secret123",
  "password_confirmation": "secret123",
  "device_name": "Android"
}
```

### Login body

```json
{
  "email": "john@example.com",
  "password": "secret123",
  "device_name": "Android"
}
```

The API token is returned only when a new token is issued. Tokens are stored hashed in the database and expire according to `API_TOKEN_TTL_DAYS` (default 30).

## User profile

| Method | Endpoint |
|---|---|
| GET | `/profile` |
| PUT | `/profile` |
| PUT | `/profile/password` |
| GET | `/points` |

## Bookings

| Method | Endpoint | Auth |
|---|---|---|
| GET | `/bookings` | Bearer |
| GET | `/bookings/{id}` | Bearer |
| POST | `/tours/{slug}/bookings` | Bearer |
| POST | `/bookings/{id}/cancel` | Bearer |

Booking creation uses multipart/form-data because every traveller must provide an ID proof document.

Expected traveller upload field:

`travellers[0][id_proof_document]`

The existing booking validation rules remain in force. The new central Preferences extension policy is additionally enforced server-side.

## Payments

| Method | Endpoint |
|---|---|
| POST | `/bookings/{id}/payment/order` |
| POST | `/bookings/{id}/payment/verify` |
| POST | `/bookings/{id}/payment/points/apply` |
| DELETE | `/bookings/{id}/payment/points` |

The existing Razorpay service is reused. The API does not expose the Razorpay secret key.

## Response contract

Success:

```json
{
  "success": true,
  "message": "Success message",
  "data": {}
}
```

Validation error:

```json
{
  "success": false,
  "message": "Please check the submitted information.",
  "errors": {
    "email": [
      "The email field is required."
    ]
  }
}
```

Server error:

```json
{
  "success": false,
  "message": "An unexpected server error occurred.",
  "errors": []
}
```

Exception messages, SQL errors, stack traces, file paths, and credentials are never returned by the API error handler.
