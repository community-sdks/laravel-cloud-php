# Authentication

Every Laravel Cloud API request requires an API token sent as a Bearer token:

```http
Authorization: Bearer YOUR_API_TOKEN
```

Create, inspect, and revoke API tokens from the API tokens section of the
Laravel Cloud organization settings. A newly created token is shown only once,
so store it in an environment variable or secret manager and never commit it.

Laravel Cloud currently offers token expiration periods of one month, six
months, and one year. Replace tokens before they expire to avoid interrupting an
integration.

## Token scope

Tokens have full organization access by default. Laravel Cloud provides two
independent optional scope controls:

- Permissions restrict which API operations the token may call.
- Resource scoping restricts which applications and environments it may use.

These controls may be used separately or together and do not inherit the role
of the user who created the token. Prefer a separate least-privilege token for
each integration so it can be rotated or revoked independently.

See the official [Laravel Cloud documentation index](https://cloud.laravel.com/docs/llms.txt)
for the current authentication page.
