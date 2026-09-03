# Documentation Conventions

Service pages are the canonical human-readable endpoint index for this SDK.
When an endpoint is implemented, its service page receives:

1. An endpoint-table entry containing the SDK method and HTTP route.
2. A dedicated section containing the official summary and description.
3. Typed path, query, and request-body parameter documentation.
4. The exact typed response and documented exception list.
5. A complete PHP example.
6. A link to the official Laravel Cloud documentation when available.

Endpoint descriptions must come from the supplied official documentation or
OpenAPI contract. Unknown fields, enum values, response shapes, and behavior are
never inferred.

Scalar API values remain native PHP scalars. In particular, resource IDs use
`string`; they do not receive one-property wrapper classes. DTOs are reserved
for request/response objects and genuinely structured nested objects, while
finite documented values use enums.

## Source organization

Code is organized by layer first and service second. For example, Applications
uses `DTO/Requests/Applications`, `DTO/Responses/Applications`,
`DTO/Resources/Applications`, `DTO/Relationships/Applications`,
`Enums/Applications`, and `Internal/Hydration/Applications`. Service classes
remain directly in the top-level `Services` folder. Shared structures remain in locations such as
`DTO/Common`, `Http`, and `Exceptions`.

## Endpoint section template

```markdown
## Endpoint name

METHOD /api/path/{identifier} · ServiceClass::method()

Official description.

### Parameters

| Name | Location | PHP type | Required | Description |
| --- | --- | --- | --- | --- |

### Returns

The response DTO and its important resource types.

### Throws

Documented SDK exception types and their HTTP conditions.

### Example

Complete, runnable PHP usage.
```
