<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Services;

use CommunitySDKs\LaravelCloud\DTO\Requests\Applications\CreateApplicationRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Applications\GetApplicationRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Applications\ListApplicationsRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Applications\UpdateApplicationRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Applications\UploadApplicationAvatarRequest;
use CommunitySDKs\LaravelCloud\DTO\Responses\Applications\CreateApplicationResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Applications\GetApplicationResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Applications\ListApplicationsResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Applications\UpdateApplicationResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Applications\UploadApplicationAvatarResponse;
use CommunitySDKs\LaravelCloud\Exceptions\ApiException;
use CommunitySDKs\LaravelCloud\Exceptions\AuthenticationException;
use CommunitySDKs\LaravelCloud\Exceptions\AuthorizationException;
use CommunitySDKs\LaravelCloud\Exceptions\NotFoundException;
use CommunitySDKs\LaravelCloud\Exceptions\TransportException;
use CommunitySDKs\LaravelCloud\Exceptions\ValidationException;
use CommunitySDKs\LaravelCloud\Http\ApiTransport;
use CommunitySDKs\LaravelCloud\Internal\Hydration\Applications\ApplicationsHydrator;
use Throwable;

/**
 * Provides the extension point for documented application operations.
 *
 * Endpoint methods are added only when their official Laravel Cloud schema is
 * available, keeping the public API strongly typed and contract-accurate.
 */
final class ApplicationsService extends AbstractService
{
    public function __construct(
        ApiTransport $transport,
        private readonly ApplicationsHydrator $hydrator = new ApplicationsHydrator(),
    ) {
        parent::__construct($transport);
    }

    /**
     * List applications.
     *
     * Gets a paginated list of all applications for the authenticated
     * organization.
     *
     * Laravel Cloud API:
     * GET /applications
     *
     * Authentication:
     * Requires a Laravel Cloud Bearer token. Token permissions and resource
     * scope may restrict which applications are returned.
     *
     * Query parameters:
     * - filter[name]: Optional exact API filter for application name.
     * - filter[region]: Optional exact API filter for application region.
     * - filter[slug]: Optional exact API filter for application slug.
     * - include: Optional comma-separated selection of organization,
     *   environments, and defaultEnvironment relationships.
     *
     * Response:
     * Returns application resources with pagination links and metadata. When
     * requested by the API, included resources may be repositories,
     * organizations, environments, or deployments.
     *
     * HTTP responses:
     * - 200: Paginated set of application resources.
     * - 401: The Bearer token is missing, invalid, or expired.
     * - 403: The token is not authorized for this operation.
     *
     * @param ListApplicationsRequest|null $request Optional filters and included relationships.
     *
     * @throws AuthenticationException When Laravel Cloud returns HTTP 401.
     * @throws AuthorizationException  When Laravel Cloud returns HTTP 403.
     * @throws TransportException      When the request or response cannot be completed.
     * @throws ApiException            When Laravel Cloud returns another unexpected API error.
     *
     * @see https://cloud.laravel.com/docs/api/applications/list-applications
     */
    public function list(?ListApplicationsRequest $request = null): ListApplicationsResponse
    {
        $payload = $this->transport->get('applications', $request?->toQuery() ?? []);

        try {
            return $this->hydrator->hydrate($payload);
        } catch (Throwable $exception) {
            throw new TransportException('Laravel Cloud returned an invalid applications response.', 0, $exception);
        }
    }

    /**
     * Get a specific application.
     *
     * Laravel Cloud API:
     * GET /applications/{application}
     *
     * Authentication:
     * Requires a Laravel Cloud Bearer token authorized to access the requested
     * application.
     *
     * Path parameters:
     * - application: The application identifier.
     *
     * Query parameters:
     * - include: Optional comma-separated selection of organization,
     *   environments, and defaultEnvironment relationships.
     *
     * Response:
     * Returns the requested application and any repository, organization,
     * environment, or deployment resources included by Laravel Cloud.
     *
     * HTTP responses:
     * - 200: Application returned successfully.
     * - 403: The token is not authorized to access the application.
     * - 404: The application does not exist or is not visible to the token.
     *
     * @param string                     $application The application identifier.
     * @param GetApplicationRequest|null $request     Optional included relationships.
     *
     * @throws AuthorizationException When Laravel Cloud returns HTTP 403.
     * @throws NotFoundException      When Laravel Cloud returns HTTP 404.
     * @throws TransportException     When the request fails or the success response is invalid.
     * @throws ApiException           When Laravel Cloud returns another unexpected API error.
     *
     * @see https://cloud.laravel.com/docs/api/applications/get-application
     */
    public function get(string $application, ?GetApplicationRequest $request = null): GetApplicationResponse
    {
        $payload = $this->transport->get(
            'applications/' . rawurlencode($application),
            $request?->toQuery() ?? [],
        );

        try {
            return new GetApplicationResponse(
                $this->hydrator->hydrateApplication($payload['data'] ?? null),
                $this->hydrator->hydrateIncluded($payload['included'] ?? null),
            );
        } catch (Throwable $exception) {
            throw new TransportException('Laravel Cloud returned an invalid get-application response.', 0, $exception);
        }
    }

    /**
     * Delete an application and all of its environments.
     *
     * Laravel Cloud API:
     * DELETE /applications/{application}
     *
     * Authentication:
     * Requires a Laravel Cloud Bearer token authorized to delete the requested
     * application.
     *
     * Path parameters:
     * - application: The application identifier.
     *
     * Response:
     * Returns no value because Laravel Cloud responds with HTTP 204 and no
     * response body after deletion succeeds.
     *
     * HTTP responses:
     * - 204: The application and all of its environments were deleted.
     * - 403: The token is not authorized to delete the application.
     * - 404: The application does not exist or is not visible to the token.
     *
     * @param string $application The application identifier.
     *
     * @throws AuthorizationException When Laravel Cloud returns HTTP 403.
     * @throws NotFoundException      When Laravel Cloud returns HTTP 404.
     * @throws TransportException     When the request cannot be completed.
     * @throws ApiException           When Laravel Cloud returns another unexpected API error.
     *
     * @see https://cloud.laravel.com/docs/api/applications/delete-application
     */
    public function delete(string $application): void
    {
        $this->transport->delete('applications/' . rawurlencode($application));
    }

    /**
     * Update an application.
     *
     * Sends PATCH /applications/{application}. Every request field is optional:
     * source-control provider, name, slug, default environment ID, repository,
     * and Slack channel. Use clearSlackChannel to explicitly send null.
     *
     * A successful HTTP 200 response contains the updated application and may
     * include repository, organization, environment, or deployment resources.
     * Laravel Cloud documents HTTP 401, 403, 404, and 422 error responses.
     *
     * @param string                   $application The application identifier.
     * @param UpdateApplicationRequest $request     Fields to update.
     *
     * @throws AuthenticationException When Laravel Cloud returns HTTP 401.
     * @throws AuthorizationException  When Laravel Cloud returns HTTP 403.
     * @throws NotFoundException       When Laravel Cloud returns HTTP 404.
     * @throws ValidationException     When Laravel Cloud returns HTTP 422.
     * @throws TransportException      When the request fails or the success response is invalid.
     * @throws ApiException            When Laravel Cloud returns another unexpected API error.
     *
     * @see https://cloud.laravel.com/docs/api/applications/update-application
     */
    public function update(string $application, UpdateApplicationRequest $request): UpdateApplicationResponse
    {
        $payload = $this->transport->patch(
            'applications/' . rawurlencode($application),
            $request->toArray(),
        );

        try {
            return new UpdateApplicationResponse(
                $this->hydrator->hydrateApplication($payload['data'] ?? null),
                $this->hydrator->hydrateIncluded($payload['included'] ?? null),
            );
        } catch (Throwable $exception) {
            throw new TransportException('Laravel Cloud returned an invalid update-application response.', 0, $exception);
        }
    }

    /**
     * Upload or replace an application's avatar.
     *
     * Sends POST /applications/{application}/avatar as multipart/form-data. The
     * required avatar field contains binary data and is limited to 3072 KB.
     *
     * A successful HTTP 200 response contains the updated application and may
     * include repository, organization, environment, or deployment resources.
     * Laravel Cloud documents HTTP 403, 404, and 422 error responses.
     *
     * @param string                         $application The application identifier.
     * @param UploadApplicationAvatarRequest $request     Binary avatar and multipart filename.
     *
     * @throws AuthorizationException When Laravel Cloud returns HTTP 403.
     * @throws NotFoundException      When Laravel Cloud returns HTTP 404.
     * @throws ValidationException    When Laravel Cloud returns HTTP 422.
     * @throws TransportException     When the request fails or the success response is invalid.
     * @throws ApiException           When Laravel Cloud returns another unexpected API error.
     *
     * @see https://cloud.laravel.com/docs/api/applications/upload-application-avatar
     */
    public function uploadAvatar(
        string $application,
        UploadApplicationAvatarRequest $request,
    ): UploadApplicationAvatarResponse {
        $payload = $this->transport->postFile(
            'applications/' . rawurlencode($application) . '/avatar',
            'avatar',
            $request->avatar,
            $request->filename,
        );

        try {
            return new UploadApplicationAvatarResponse(
                $this->hydrator->hydrateApplication($payload['data'] ?? null),
                $this->hydrator->hydrateIncluded($payload['included'] ?? null),
            );
        } catch (Throwable $exception) {
            throw new TransportException('Laravel Cloud returned an invalid upload-avatar response.', 0, $exception);
        }
    }

    /**
     * Remove an application's avatar.
     *
     * Sends DELETE /applications/{application}/avatar. A successful request
     * returns HTTP 204 with no response body. Laravel Cloud documents HTTP 403
     * and 404 error responses.
     *
     * @param string $application The application identifier.
     *
     * @throws AuthorizationException When Laravel Cloud returns HTTP 403.
     * @throws NotFoundException      When Laravel Cloud returns HTTP 404.
     * @throws TransportException     When the request cannot be completed.
     * @throws ApiException           When Laravel Cloud returns another unexpected API error.
     *
     * @see https://cloud.laravel.com/docs/api/applications/delete-application-avatar
     */
    public function deleteAvatar(string $application): void
    {
        $this->transport->delete('applications/' . rawurlencode($application) . '/avatar');
    }

    /**
     * Create an application.
     *
     * Creates a new Laravel Cloud application from a source-control repository.
     *
     * Laravel Cloud API:
     * POST /applications
     *
     * Authentication:
     * Requires a Laravel Cloud Bearer token with permission to create an
     * application in the authenticated organization.
     *
     * Request body:
     * - source_control_provider_type: GitHub, GitLab, or Bitbucket. Although it
     *   is absent from the OpenAPI required array, its description says it has
     *   been required since March 9, 2026, so the SDK requires it.
     * - repository: The source-control repository.
     * - name: A 3–40 character application name matching the documented pattern.
     * - region: A supported Laravel Cloud region.
     * - root_directory: Optional repository subdirectory or null.
     * - cluster_id: Optional dedicated cluster identifier or null.
     *
     * Response:
     * Returns the created application resource and any repository,
     * organization, environment, or deployment resources included by Laravel
     * Cloud.
     *
     * HTTP responses:
     * - 201: Application created successfully.
     * - 401: The Bearer token is missing, invalid, or expired.
     * - 403: The token is not authorized to create the application.
     * - 422: One or more request fields failed validation.
     *
     * @param CreateApplicationRequest $request Fully typed application creation fields.
     *
     * @throws AuthenticationException When Laravel Cloud returns HTTP 401.
     * @throws AuthorizationException  When Laravel Cloud returns HTTP 403.
     * @throws ValidationException     When Laravel Cloud returns HTTP 422.
     * @throws TransportException When the request fails or the success response is invalid.
     * @throws ApiException       When Laravel Cloud returns another unexpected API error.
     *
     * @see https://cloud.laravel.com/docs/api/applications/create-application
     */
    public function create(CreateApplicationRequest $request): CreateApplicationResponse
    {
        $payload = $this->transport->post('applications', $request->toArray());

        try {
            return new CreateApplicationResponse(
                $this->hydrator->hydrateApplication($payload['data'] ?? null),
                $this->hydrator->hydrateIncluded($payload['included'] ?? null),
            );
        } catch (Throwable $exception) {
            throw new TransportException('Laravel Cloud returned an invalid create-application response.', 0, $exception);
        }
    }
}
