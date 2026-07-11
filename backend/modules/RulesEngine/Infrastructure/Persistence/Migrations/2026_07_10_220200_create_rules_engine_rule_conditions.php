<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Infrastructure\Persistence\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\RulesEngine\Domain\Models\RuleGroup;
use Modules\Shared\Domain\Enums\SelectionCondition;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rules_engine_rule_conditions', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id');
            $table->foreignIdFor(RuleGroup::class)
                ->constrained('rules_engine_rule_groups')
                ->cascadeOnDelete();
            $table->foreignIdFor(Attribute::class, 'source_attribute_id')
                ->constrained('configurator_attributes')
                ->cascadeOnDelete();
            $table->string('condition')->default(SelectionCondition::Equals->value);
            $table->string('condition_value')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['rule_group_id', 'position']);
            $table->index('source_attribute_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rules_engine_rule_conditions');
    }
};
