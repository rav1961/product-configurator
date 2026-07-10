<?php

declare(strict_types=1);

namespace Modules\Shared\Tests\Unit;

use Modules\Shared\Domain\Enums\SelectionCondition;
use Modules\Shared\Domain\Services\SelectionConditionMatcher;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class SelectionConditionMatcherTest extends TestCase
{
    private SelectionConditionMatcher $matcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->matcher = new SelectionConditionMatcher;
    }

    #[DataProvider('equalsProvider')]
    public function test_equals(mixed $sourceValue, ?string $conditionValue, bool $expected): void
    {
        $this->assertSame(
            $expected,
            $this->matcher->matches($sourceValue, SelectionCondition::Equals, $conditionValue),
        );
    }

    /**
     * @return iterable<string, array{mixed, ?string, bool}>
     */
    public static function equalsProvider(): iterable
    {
        yield 'scalar match' => ['red', 'red', true];
        yield 'scalar mismatch' => ['blue', 'red', false];
        yield 'scalar null source' => [null, 'red', false];
        yield 'scalar null condition' => ['red', null, false];
        yield 'numeric cast to string' => [42, '42', true];
        yield 'multiselect contains' => [['red', 'blue'], 'red', true];
        yield 'multiselect missing' => [['blue'], 'red', false];
        yield 'empty array' => [[], 'red', false];
    }

    #[DataProvider('notEqualsProvider')]
    public function test_not_equals(mixed $sourceValue, ?string $conditionValue, bool $expected): void
    {
        $this->assertSame(
            $expected,
            $this->matcher->matches($sourceValue, SelectionCondition::NotEquals, $conditionValue),
        );
    }

    /**
     * @return iterable<string, array{mixed, ?string, bool}>
     */
    public static function notEqualsProvider(): iterable
    {
        yield 'scalar mismatch' => ['blue', 'red', true];
        yield 'scalar match' => ['red', 'red', false];
        yield 'null source' => [null, 'red', true];
        yield 'multiselect missing value' => [['blue'], 'red', true];
    }

    #[DataProvider('isSetProvider')]
    public function test_is_set(mixed $sourceValue, bool $expected): void
    {
        $this->assertSame(
            $expected,
            $this->matcher->matches($sourceValue, SelectionCondition::IsSet, null),
        );
    }

    /**
     * @return iterable<string, array{mixed, bool}>
     */
    public static function isSetProvider(): iterable
    {
        yield 'null' => [null, false];
        yield 'empty string' => ['', false];
        yield 'empty array' => [[], false];
        yield 'non-empty string' => ['red', true];
        yield 'boolean false' => [false, true];
        yield 'boolean true' => [true, true];
        yield 'zero' => [0, true];
        yield 'multiselect values' => [['red'], true];
    }

    #[DataProvider('isEmptyProvider')]
    public function test_is_empty(mixed $sourceValue, bool $expected): void
    {
        $this->assertSame(
            $expected,
            $this->matcher->matches($sourceValue, SelectionCondition::IsEmpty, null),
        );
    }

    /**
     * @return iterable<string, array{mixed, bool}>
     */
    public static function isEmptyProvider(): iterable
    {
        yield 'null' => [null, true];
        yield 'empty string' => ['', true];
        yield 'empty array' => [[], true];
        yield 'non-empty string' => ['red', false];
        yield 'boolean false' => [false, false];
        yield 'zero' => [0, false];
    }

    #[DataProvider('isNotSetProvider')]
    public function test_is_not_set(mixed $sourceValue, bool $expected): void
    {
        $this->assertSame(
            $expected,
            $this->matcher->matches($sourceValue, SelectionCondition::IsNotSet, null),
        );
    }

    /**
     * @return iterable<string, array{mixed, bool}>
     */
    public static function isNotSetProvider(): iterable
    {
        yield 'null' => [null, true];
        yield 'empty string' => ['', false];
        yield 'empty array' => [[], false];
        yield 'boolean false' => [false, false];
        yield 'zero' => [0, false];
    }
}
