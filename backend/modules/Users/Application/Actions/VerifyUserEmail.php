<?php

declare(strict_types=1);

namespace Modules\Users\Application\Actions;

use Illuminate\Auth\Events\Verified;
use Modules\Users\Domain\Contracts\UserRepositoryInterface;
use Modules\Users\Domain\Exceptions\InvalidEmailVerificationHash;

final readonly class VerifyUserEmail
{
    public function __construct(
        private UserRepositoryInterface $users,
    ) {}

    public function handle(string $publicId, string $hash): string
    {
        $user = $this->users->findByPublicId($publicId);

        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            throw new InvalidEmailVerificationHash;
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            event(new Verified($user));
        }

        return rtrim((string) config('app.frontend_url'), '/').'/email/verified';
    }
}
