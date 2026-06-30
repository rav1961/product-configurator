<?php

namespace Modules\Shared\Tests\Concerns;

trait InteractsWithSpaSession
{
    protected function withSpaSession(): static
    {
        return $this->withHeader('Origin', (string) config('app.url'));
    }
}
