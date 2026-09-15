# Travels project upgrade notes

## Added

- Central Admin Preferences → Allowed File Extensions.
- Server-side upload extension enforcement for web and API requests.
- Protected server executable/configuration extensions.
- REST API v1 for public website data, authentication, profile, points, bookings, payments and contact.
- Hashed bearer API tokens with configurable expiry.
- API error contract with safe generic messages.
- User-facing 400/401/403/404/405/408/409/419/422/429/500/502/503/504 pages.
- Missing email verification and password reset views.
- Password reset token storage migration.
- API catch-all protection so `/api/*` is not captured by the public CMS catch-all.
- API documentation.

## Existing logic preserved

The existing booking service, payment service, tour/blog controllers and admin controllers are reused rather than rewritten for the API layer.

The existing upload-specific validation remains in place. The central extension policy is an additional security gate.

## Production

Before deployment:

1. Copy `.env.example` to `.env`.
2. Set the real database and mail credentials.
3. Set `APP_ENV=production`.
4. Set `APP_DEBUG=false`.
5. Set the real `APP_URL`.
6. Run `php artisan migrate --force`.
7. Run `php artisan storage:link`.
8. Run `php artisan config:cache`.
9. Run `php artisan route:cache`.
10. Run `php artisan view:cache`.
11. Run `npm ci`.
12. Run `npm run build`.
13. Do not deploy a `public/hot` file. The package intentionally removes the local Vite hot marker.
14. Configure the web server document root to the `public` directory.

## API token

`API_TOKEN_TTL_DAYS=30`

Change this in the environment if the mobile application's token lifetime needs to be different.

## Important

The generated package does not contain the uploaded project's `.env`, `vendor`, `node_modules`, or Git metadata. Install dependencies on the deployment machine using the committed lock files.
