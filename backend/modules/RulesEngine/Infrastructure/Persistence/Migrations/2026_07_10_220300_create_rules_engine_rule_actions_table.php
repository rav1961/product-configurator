<?php

declare(strict_types=1);

namespace app\modules\RulesEngine\Infrastructure\Persistence\Migrations\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\RulesEngine\Domain\Enums\RuleActionType;
use Modules\RulesEngine\Domain\Models\Rule;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rules_engine_rule_actions', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id');
            $table->foreignIdFor(Rule::class)
                ->constrained('rules_engine_rules')
                ->cascadeOnDelete();
            $table->string('type')->default(RuleActionType::AddModifier->value);
            $table->json('payload');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['rule_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rules_engine_rule_actions');
    }
};
