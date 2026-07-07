<?php

declare(strict_types=1);

namespace Modules\Shared\Presentation\Http;

/**
 * Shared route middleware stacks for module API routes.
 *
 * Module routes are registered per-module via ModuleServiceProvider — not in routes/api.php.
 */
final class ApiRouteMiddleware
{
    /** @var list<string> */
    public const VERIFIED = ['auth:sanctum', 'verified'];

    /** @var list<string> */
    public const SENSITIVE_THROTTLE = ['throttle:6,1'];
}
