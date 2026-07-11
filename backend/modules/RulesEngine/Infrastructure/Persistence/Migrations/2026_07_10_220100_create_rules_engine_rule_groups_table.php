<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Infrastructure\Persistence\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\RulesEngine\Domain\Enums\MatchMode;
use Modules\RulesEngine\Domain\Models\Rule;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rules_engine_rule_groups', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id');
            $table->foreignIdFor(Rule::class)
                ->constrained('rules_engine_rules')
                ->cascadeOnDelete();
            $table->string('conditions_match_mode')->default(MatchMode::All->value);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['rule_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rules_engine_rule_groups');
    }
};
