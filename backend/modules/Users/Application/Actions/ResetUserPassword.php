<?php

declare(strict_types=1);

namespace Modules\Users\Application\Actions;

use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    public function __call(string $name, array $arguments)
    {
        // TODO: Implement @method void reset(\Illuminate\Foundation\Auth\User $user, array $input)
    }
}
