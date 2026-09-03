# API Contract and Versioning

This SDK targets one Laravel Cloud API contract at a time. It does not expose a
version selector, parallel versioned clients, or versioned service namespaces
unless Laravel Cloud officially introduces multiple concurrently supported API
versions.

When Laravel Cloud makes a breaking contract change, the corresponding breaking
SDK API change is normally released as a new major Composer package version.
Additive endpoint and schema support may be released without a major version
when it preserves the existing public SDK API.

## Source of truth

Implementation begins with the official [Laravel Cloud documentation index](https://cloud.laravel.com/docs/llms.txt)
and the supplied documentation and OpenAPI definitions for the endpoint being
added. The SDK does not infer undocumented fields or behavior.

## API root and endpoint paths

The default API root is `https://cloud.laravel.com/api/`. It is configurable for
tests, mock servers, proxies, and potential future official environments. The
SDK normalizes the root internally, while services supply relative endpoint
paths only and never hardcode a hostname.
