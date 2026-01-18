# Entregable DB - El Juramento de Valtherion

Generado: 2026-01-16T10:54:12.6297727+01:00

## Archivos incluidos
- database\migrations\2026_01_16_000100_create_rarities_table.php
- database\migrations\2026_01_16_000200_create_races_table.php
- database\migrations\2026_01_16_000300_create_characters_table.php
- database\migrations\2026_01_16_000400_create_items_table.php
- database\migrations\2026_01_16_000500_create_inventory_and_equipment_tables.php
- database\migrations\2026_01_16_000600_create_animals_and_character_animals_tables.php
- database\migrations\2026_01_16_000700_add_active_animal_id_to_characters_table.php
- database\migrations\2026_01_16_000800_create_matches_tables.php
- database\migrations\2026_01_16_001000_create_loot_tables_and_entries_tables.php
- database\migrations\2026_01_16_001100_create_missions_tables.php
- database\migrations\2026_01_16_001150_create_seasons_and_rankings_tables.php
- database\migrations\2026_01_16_001200_create_mission_runs_tables.php
- database\migrations\2026_01_16_001300_create_chests_and_rewards_tables.php
- database\migrations\2026_01_16_002000_create_premium_codes_tables.php
- database\migrations\2026_01_16_002100_add_premium_fields_to_users_table.php
- database\migrations\2026_01_16_002200_create_heroes_table.php
- database\migrations\2026_01_16_002300_add_hero_id_to_characters_table.php
- database/seeders/DatabaseSeeder.php
- database/seeders/RaritiesSeeder.php
- database/seeders/RacesSeeder.php
- database/seeders/EquipmentSlotsSeeder.php
- database/seeders/HeroesSeeder.php
- database/seeders/LootBaseSeeder.php
- app\Models\Animal.php
- app\Models\Character.php
- app\Models\CharacterAnimal.php
- app\Models\CharacterEquipment.php
- app\Models\Chest.php
- app\Models\ChestOpening.php
- app\Models\ChestOpeningReward.php
- app\Models\EquipmentSlot.php
- app\Models\Hero.php
- app\Models\Item.php
- app\Models\LootEntry.php
- app\Models\LootTable.php
- app\Models\MatchModel.php
- app\Models\MatchParticipant.php
- app\Models\MatchTurn.php
- app\Models\Mission.php
- app\Models\MissionChoice.php
- app\Models\MissionNode.php
- app\Models\MissionRun.php
- app\Models\MissionRunChoice.php
- app\Models\PremiumCode.php
- app\Models\PremiumCodeRedemption.php
- app\Models\Race.php
- app\Models\Rarity.php
- app\Models\Season.php
- app\Models\SeasonRaceRanking.php
- app\Models\SeasonRaceWinner.php
- app\Models\User.php

## database\migrations\2026_01_16_000100_create_rarities_table.php

```php
<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('rarities', function (Blueprint $table) {\n            $table->id();\n            $table->string('name')->unique();\n            $table->unsignedInteger('weight');\n            $table->unsignedInteger('min_level')->default(1);\n            $table->timestamps();\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::dropIfExists('rarities');\n    }\n};\n
```

## database\migrations\2026_01_16_000200_create_races_table.php

```php
<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('races', function (Blueprint $table) {\n            $table->id();\n            $table->string('name')->unique();\n            $table->string('access')->default('free')->index();\n            $table->longText('lore');\n\n            $table->unsignedInteger('base_hp');\n            $table->unsignedInteger('base_strength');\n            $table->unsignedInteger('base_magic');\n            $table->unsignedInteger('base_defense');\n            $table->unsignedInteger('base_speed');\n\n            $table->timestamps();\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::dropIfExists('races');\n    }\n};\n
```

## database\migrations\2026_01_16_000300_create_characters_table.php

```php
<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('characters', function (Blueprint $table) {\n            $table->id();\n            $table->foreignId('user_id')->constrained()->cascadeOnDelete();\n            $table->foreignId('race_id')->constrained()->restrictOnDelete();\n\n            $table->string('name')->unique();\n\n            $table->unsignedInteger('level')->default(1);\n            $table->unsignedBigInteger('exp')->default(0);\n            $table->unsignedBigInteger('gold')->default(0);\n\n            $table->integer('hp_bonus')->default(0);\n            $table->integer('strength_bonus')->default(0);\n            $table->integer('magic_bonus')->default(0);\n            $table->integer('defense_bonus')->default(0);\n            $table->integer('speed_bonus')->default(0);\n\n            $table->timestamps();\n        });\n\n        Schema::table('characters', function (Blueprint $table) {\n            $table->index(['user_id']);\n            $table->index(['race_id']);\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::dropIfExists('characters');\n    }\n};\n
```

## database\migrations\2026_01_16_000400_create_items_table.php

```php
<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('items', function (Blueprint $table) {\n            $table->id();\n            $table->string('name')->unique();\n            $table->string('type');\n            $table->foreignId('rarity_id')->constrained('rarities')->restrictOnDelete();\n            $table->unsignedInteger('required_level')->default(1)->index();\n            $table->boolean('stackable')->default(false);\n\n            $table->integer('bonus_hp')->default(0);\n            $table->integer('bonus_strength')->default(0);\n            $table->integer('bonus_magic')->default(0);\n            $table->integer('bonus_defense')->default(0);\n            $table->integer('bonus_speed')->default(0);\n\n            $table->unsignedInteger('sell_price')->default(0);\n            $table->timestamps();\n\n            $table->index(['rarity_id']);\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::dropIfExists('items');\n    }\n};\n
```

## database\migrations\2026_01_16_000500_create_inventory_and_equipment_tables.php

```php
<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('character_items', function (Blueprint $table) {\n            $table->id();\n            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();\n            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();\n            $table->unsignedInteger('quantity')->default(1);\n            $table->timestamps();\n\n            $table->unique(['character_id', 'item_id']);\n            $table->index(['item_id']);\n        });\n\n        Schema::create('equipment_slots', function (Blueprint $table) {\n            $table->id();\n            $table->string('code')->unique();\n            $table->string('name');\n            $table->timestamps();\n        });\n\n        Schema::create('character_equipment', function (Blueprint $table) {\n            $table->id();\n            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();\n            $table->foreignId('slot_id')->constrained('equipment_slots')->restrictOnDelete();\n            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();\n            $table->timestamps();\n\n            $table->unique(['character_id', 'slot_id']);\n            $table->index(['item_id']);\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::dropIfExists('character_equipment');\n        Schema::dropIfExists('equipment_slots');\n        Schema::dropIfExists('character_items');\n    }\n};\n
```

## database\migrations\2026_01_16_000600_create_animals_and_character_animals_tables.php

```php
<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('animals', function (Blueprint $table) {\n            $table->id();\n            $table->string('name')->unique();\n            $table->foreignId('rarity_id')->constrained('rarities')->restrictOnDelete();\n            $table->unsignedInteger('required_level')->default(1)->index();\n            $table->boolean('mountable')->default(false);\n\n            $table->unsignedInteger('hp');\n            $table->unsignedInteger('strength');\n            $table->unsignedInteger('magic');\n            $table->unsignedInteger('defense');\n            $table->unsignedInteger('speed');\n\n            $table->longText('description');\n            $table->timestamps();\n\n            $table->index(['rarity_id']);\n        });\n\n        Schema::create('character_animals', function (Blueprint $table) {\n            $table->id();\n            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();\n            $table->foreignId('animal_id')->constrained('animals')->restrictOnDelete();\n            $table->dateTime('acquired_at');\n            $table->timestamps();\n\n            $table->unique(['character_id', 'animal_id']);\n            $table->index(['animal_id']);\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::dropIfExists('character_animals');\n        Schema::dropIfExists('animals');\n    }\n};\n
```

## database\migrations\2026_01_16_000700_add_active_animal_id_to_characters_table.php

```php
<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::table('characters', function (Blueprint $table) {\n            $table->foreignId('active_animal_id')\n                ->nullable()\n                ->after('speed_bonus')\n                ->constrained('animals')\n                ->nullOnDelete();\n\n            $table->index(['active_animal_id']);\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::table('characters', function (Blueprint $table) {\n            $table->dropForeign(['active_animal_id']);\n            $table->dropIndex(['active_animal_id']);\n            $table->dropColumn('active_animal_id');\n        });\n    }\n};\n
```

## database\migrations\2026_01_16_000800_create_matches_tables.php

```php
<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('matches', function (Blueprint $table) {\n            $table->id();\n            $table->string('status')->default('ongoing')->index();\n            $table->dateTime('started_at')->nullable();\n            $table->dateTime('ended_at')->nullable();\n            $table->timestamps();\n        });\n\n        Schema::create('match_participants', function (Blueprint $table) {\n            $table->id();\n            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();\n            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();\n            $table->boolean('is_winner')->default(false);\n            $table->dateTime('joined_at')->nullable();\n            $table->timestamps();\n\n            $table->unique(['match_id', 'character_id']);\n            $table->index(['match_id']);\n            $table->index(['character_id']);\n        });\n\n        Schema::create('match_turns', function (Blueprint $table) {\n            $table->id();\n            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();\n            $table->unsignedInteger('turn_number');\n            $table->foreignId('acting_participant_id')\n                ->constrained('match_participants')\n                ->cascadeOnDelete();\n            $table->longText('action_text');\n            $table->json('metadata')->nullable();\n            $table->timestamps();\n\n            $table->unique(['match_id', 'turn_number']);\n            $table->index(['match_id']);\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::dropIfExists('match_turns');\n        Schema::dropIfExists('match_participants');\n        Schema::dropIfExists('matches');\n    }\n};\n
```

## database\migrations\2026_01_16_001000_create_loot_tables_and_entries_tables.php

```php
<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('loot_tables', function (Blueprint $table) {\n            $table->id();\n            $table->string('name')->unique();\n            $table->unsignedInteger('rolls')->default(1);\n            $table->unsignedInteger('min_level')->nullable();\n            $table->timestamps();\n        });\n\n        Schema::create('loot_entries', function (Blueprint $table) {\n            $table->id();\n            $table->foreignId('loot_table_id')->constrained('loot_tables')->cascadeOnDelete();\n\n            $table->string('entry_type');\n            $table->foreignId('item_id')->nullable()->constrained('items')->restrictOnDelete();\n            $table->foreignId('animal_id')->nullable()->constrained('animals')->restrictOnDelete();\n\n            $table->unsignedInteger('weight');\n            $table->unsignedInteger('min_qty')->default(1);\n            $table->unsignedInteger('max_qty')->default(1);\n            $table->unsignedInteger('required_level')->nullable();\n            $table->timestamps();\n\n            $table->index(['loot_table_id']);\n            $table->index(['entry_type']);\n            $table->index(['item_id']);\n            $table->index(['animal_id']);\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::dropIfExists('loot_entries');\n        Schema::dropIfExists('loot_tables');\n    }\n};\n
```

## database\migrations\2026_01_16_001100_create_missions_tables.php

```php
<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('missions', function (Blueprint $table) {\n            $table->id();\n            $table->string('title')->unique();\n            $table->longText('description');\n            $table->unsignedInteger('min_level')->default(1);\n            $table->unsignedInteger('base_difficulty');\n            $table->unsignedInteger('base_exp');\n            $table->unsignedInteger('base_points');\n            $table->timestamps();\n\n            $table->index(['min_level']);\n        });\n\n        Schema::create('mission_nodes', function (Blueprint $table) {\n            $table->id();\n            $table->foreignId('mission_id')->constrained('missions')->cascadeOnDelete();\n            $table->string('node_key');\n            $table->longText('text');\n            $table->boolean('is_end')->default(false);\n            $table->timestamps();\n\n            $table->unique(['mission_id', 'node_key']);\n            $table->index(['mission_id']);\n        });\n\n        Schema::create('mission_choices', function (Blueprint $table) {\n            $table->id();\n            $table->foreignId('node_id')->constrained('mission_nodes')->cascadeOnDelete();\n            $table->string('choice_text');\n            $table->foreignId('next_node_id')\n                ->nullable()\n                ->constrained('mission_nodes')\n                ->nullOnDelete();\n\n            $table->integer('difficulty_delta')->default(0);\n            $table->decimal('exp_multiplier', 6, 2)->default(1.00);\n            $table->decimal('points_multiplier', 6, 2)->default(1.00);\n            $table->foreignId('loot_table_id')->nullable()->constrained('loot_tables')->nullOnDelete();\n            $table->timestamps();\n\n            $table->index(['node_id']);\n            $table->index(['next_node_id']);\n            $table->index(['loot_table_id']);\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::dropIfExists('mission_choices');\n        Schema::dropIfExists('mission_nodes');\n        Schema::dropIfExists('missions');\n    }\n};\n
```

## database\migrations\2026_01_16_001150_create_seasons_and_rankings_tables.php

```php
<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('seasons', function (Blueprint $table) {\n            $table->id();\n            $table->unsignedInteger('year');\n            $table->unsignedInteger('month');\n            $table->dateTime('starts_at');\n            $table->dateTime('ends_at');\n            $table->timestamps();\n\n            $table->unique(['year', 'month']);\n        });\n\n        Schema::create('season_race_rankings', function (Blueprint $table) {\n            $table->id();\n            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();\n            $table->foreignId('race_id')->constrained('races')->cascadeOnDelete();\n            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();\n            $table->unsignedInteger('points')->default(0);\n            $table->timestamps();\n\n            $table->unique(['season_id', 'race_id', 'character_id']);\n            $table->index(['season_id', 'race_id', 'points']);\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::dropIfExists('season_race_rankings');\n        Schema::dropIfExists('seasons');\n    }\n};\n
```

## database\migrations\2026_01_16_001200_create_mission_runs_tables.php

```php
<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('mission_runs', function (Blueprint $table) {\n            $table->id();\n            $table->foreignId('mission_id')->constrained('missions')->cascadeOnDelete();\n            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();\n            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();\n\n            $table->unsignedInteger('final_difficulty');\n            $table->unsignedInteger('exp_gained');\n            $table->unsignedInteger('points_gained');\n            $table->boolean('completed')->default(false);\n            $table->dateTime('started_at');\n            $table->dateTime('ended_at')->nullable();\n            $table->timestamps();\n\n            $table->index(['mission_id']);\n            $table->index(['character_id']);\n            $table->index(['season_id']);\n        });\n\n        Schema::create('mission_run_choices', function (Blueprint $table) {\n            $table->id();\n            $table->foreignId('mission_run_id')->constrained('mission_runs')->cascadeOnDelete();\n            $table->foreignId('node_id')->constrained('mission_nodes')->restrictOnDelete();\n            $table->foreignId('choice_id')->constrained('mission_choices')->restrictOnDelete();\n            $table->unsignedInteger('step_number');\n            $table->timestamps();\n\n            $table->index(['mission_run_id']);\n            $table->index(['step_number']);\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::dropIfExists('mission_run_choices');\n        Schema::dropIfExists('mission_runs');\n    }\n};\n
```

## database\migrations\2026_01_16_001300_create_chests_and_rewards_tables.php

```php
<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('chests', function (Blueprint $table) {\n            $table->id();\n            $table->string('name')->unique();\n            $table->foreignId('loot_table_id')->constrained('loot_tables')->restrictOnDelete();\n            $table->unsignedInteger('items_count')->default(3);\n            $table->timestamps();\n        });\n\n        Schema::create('season_race_winners', function (Blueprint $table) {\n            $table->id();\n            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();\n            $table->foreignId('race_id')->constrained('races')->cascadeOnDelete();\n            $table->foreignId('winner_character_id')->constrained('characters')->cascadeOnDelete();\n            $table->foreignId('chest_id')->constrained('chests')->restrictOnDelete();\n            $table->dateTime('granted_at');\n            $table->dateTime('claimed_at')->nullable();\n            $table->timestamps();\n\n            $table->unique(['season_id', 'race_id']);\n            $table->index(['season_id']);\n            $table->index(['race_id']);\n        });\n\n        Schema::create('chest_openings', function (Blueprint $table) {\n            $table->id();\n            $table->foreignId('chest_id')->constrained('chests')->restrictOnDelete();\n            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();\n            $table->dateTime('opened_at');\n            $table->timestamps();\n\n            $table->index(['chest_id']);\n            $table->index(['character_id']);\n        });\n\n        Schema::create('chest_opening_rewards', function (Blueprint $table) {\n            $table->id();\n            $table->foreignId('chest_opening_id')->constrained('chest_openings')->cascadeOnDelete();\n            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();\n            $table->unsignedInteger('quantity')->default(1);\n            $table->timestamps();\n\n            $table->index(['chest_opening_id']);\n            $table->index(['item_id']);\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::dropIfExists('chest_opening_rewards');\n        Schema::dropIfExists('chest_openings');\n        Schema::dropIfExists('season_race_winners');\n        Schema::dropIfExists('chests');\n    }\n};\n
```

## database\migrations\2026_01_16_002000_create_premium_codes_tables.php

```php
<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('premium_codes', function (Blueprint $table) {\n            $table->id();\n            $table->string('code_hash')->unique();\n            $table->unsignedInteger('max_uses');\n            $table->unsignedInteger('uses_count')->default(0);\n            $table->boolean('is_active')->default(true);\n            $table->foreignId('created_by_user_id')->constrained('users')->restrictOnDelete();\n            $table->timestamps();\n\n            $table->index(['is_active']);\n        });\n\n        Schema::create('premium_code_redemptions', function (Blueprint $table) {\n            $table->id();\n            $table->foreignId('premium_code_id')->constrained('premium_codes')->cascadeOnDelete();\n            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();\n            $table->dateTime('redeemed_at');\n            $table->timestamps();\n\n            $table->unique(['premium_code_id', 'user_id']);\n            $table->index(['user_id']);\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::dropIfExists('premium_code_redemptions');\n        Schema::dropIfExists('premium_codes');\n    }\n};\n
```

## database\migrations\2026_01_16_002100_add_premium_fields_to_users_table.php

```php
<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::table('users', function (Blueprint $table) {\n            $table->string('plan')->default('free')->after('password');\n            $table->foreignId('premium_granted_by_code_id')\n                ->nullable()\n                ->after('plan')\n                ->constrained('premium_codes')\n                ->nullOnDelete();\n            $table->dateTime('premium_granted_at')->nullable()->after('premium_granted_by_code_id');\n\n            $table->index(['plan']);\n            $table->index(['premium_granted_by_code_id']);\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::table('users', function (Blueprint $table) {\n            $table->dropIndex(['plan']);\n            $table->dropIndex(['premium_granted_by_code_id']);\n\n            $table->dropForeign(['premium_granted_by_code_id']);\n            $table->dropColumn('premium_granted_at');\n            $table->dropColumn('premium_granted_by_code_id');\n            $table->dropColumn('plan');\n        });\n    }\n};\n
```

## database\migrations\2026_01_16_002200_create_heroes_table.php

```php
<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('heroes', function (Blueprint $table) {\n            $table->id();\n            $table->foreignId('race_id')->constrained('races')->restrictOnDelete();\n            $table->string('code')->unique();\n            $table->string('name');\n            $table->longText('description');\n\n            $table->unsignedInteger('base_hp');\n            $table->unsignedInteger('base_strength');\n            $table->unsignedInteger('base_magic');\n            $table->unsignedInteger('base_defense');\n            $table->unsignedInteger('base_speed');\n\n            $table->boolean('unique_global')->default(true);\n            $table->timestamps();\n\n            $table->index(['race_id']);\n            $table->index(['unique_global']);\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::dropIfExists('heroes');\n    }\n};\n
```

## database\migrations\2026_01_16_002300_add_hero_id_to_characters_table.php

```php
<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::table('characters', function (Blueprint $table) {\n            $table->foreignId('hero_id')\n                ->nullable()\n                ->after('race_id')\n                ->constrained('heroes')\n                ->nullOnDelete();\n\n            $table->unique(['hero_id']);\n            $table->index(['hero_id']);\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::table('characters', function (Blueprint $table) {\n            $table->dropUnique(['hero_id']);\n            $table->dropIndex(['hero_id']);\n            $table->dropForeign(['hero_id']);\n            $table->dropColumn('hero_id');\n        });\n    }\n};\n
```

## database/seeders/DatabaseSeeder.php

```php
<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\EquipmentSlotsSeeder;
use Database\Seeders\HeroesSeeder;
use Database\Seeders\LootBaseSeeder;
use Database\Seeders\RacesSeeder;
use Database\Seeders\RaritiesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RaritiesSeeder::class,
            RacesSeeder::class,
            EquipmentSlotsSeeder::class,
            HeroesSeeder::class,
            LootBaseSeeder::class,
        ]);

        if (!User::query()->where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }
    }
}


```

## database/seeders/RaritiesSeeder.php

```php
<?php\n\nnamespace Database\Seeders;\n\nuse Illuminate\Database\Seeder;\nuse Illuminate\Support\Facades\DB;\nuse Illuminate\Support\Facades\Date;\n\nclass RaritiesSeeder extends Seeder\n{\n    public function run(): void\n    {\n        $now = Date::now();\n\n        DB::table('rarities')->upsert([\n            ['name' => 'common', 'weight' => 700, 'min_level' => 1, 'created_at' => $now, 'updated_at' => $now],\n            ['name' => 'rare', 'weight' => 250, 'min_level' => 5, 'created_at' => $now, 'updated_at' => $now],\n            ['name' => 'epic', 'weight' => 45, 'min_level' => 20, 'created_at' => $now, 'updated_at' => $now],\n            ['name' => 'legendary', 'weight' => 5, 'min_level' => 40, 'created_at' => $now, 'updated_at' => $now],\n        ], ['name'], ['weight', 'min_level', 'updated_at']);\n    }\n}\n
```

## database/seeders/RacesSeeder.php

```php
<?php\n\nnamespace Database\Seeders;\n\nuse Illuminate\Database\Seeder;\nuse Illuminate\Support\Facades\DB;\nuse Illuminate\Support\Facades\Date;\n\nclass RacesSeeder extends Seeder\n{\n    public function run(): void\n    {\n        $now = Date::now();\n\n        $races = [\n            [\n                'name' => 'Altos Elfos (Asur)',\n                'access' => 'free',\n                'lore' => 'Orgullosos y disciplinados, los Asur combinan tradición, arte y dominio arcano. Sus huestes destacan por la precisión, la magia refinada y una voluntad férrea frente al caos.',\n                'base_hp' => 95,\n                'base_strength' => 12,\n                'base_magic' => 20,\n                'base_defense' => 14,\n                'base_speed' => 18,\n            ],\n            [\n                'name' => 'Elfos Oscuros (Druchii)',\n                'access' => 'free',\n                'lore' => 'Crueles y ambiciosos, los Druchii prosperan en intrigas y golpes letales. Su poder nace del dolor, la velocidad y una magia oscura que castiga los errores del enemigo.',\n                'base_hp' => 90,\n                'base_strength' => 14,\n                'base_magic' => 18,\n                'base_defense' => 12,\n                'base_speed' => 20,\n            ],\n            [\n                'name' => 'Elfos Silvanos (Asray)',\n                'access' => 'free',\n                'lore' => 'Guardianes de bosques antiguos, los Asray libran una guerra de emboscadas y flechas. Su fuerza está en la movilidad, el sigilo y el vínculo con espíritus del bosque.',\n                'base_hp' => 92,\n                'base_strength' => 13,\n                'base_magic' => 16,\n                'base_defense' => 12,\n                'base_speed' => 22,\n            ],\n            [\n                'name' => 'Enanos',\n                'access' => 'free',\n                'lore' => 'Resistentes y testarudos, los Enanos dominan la forja y la guerra defensiva. Su metal aguanta, sus hachas golpean fuerte y su experiencia compensa su menor movilidad.',\n                'base_hp' => 120,\n                'base_strength' => 18,\n                'base_magic' => 6,\n                'base_defense' => 22,\n                'base_speed' => 10,\n            ],\n            [\n                'name' => 'Humanos (Imperio/Bretonia/Kislev)',\n                'access' => 'free',\n                'lore' => 'Versátiles y resilientes, los Humanos se adaptan a cualquier frente. Entre acero, pólvora, fe y disciplina, su equilibrio los convierte en un pilar contra amenazas sobrenaturales.',\n                'base_hp' => 105,\n                'base_strength' => 15,\n                'base_magic' => 12,\n                'base_defense' => 15,\n                'base_speed' => 15,\n            ],\n            [\n                'name' => 'Orcos y Goblins',\n                'access' => 'free',\n                'lore' => 'Caóticos e impredecibles, viven para la pelea. Los Orcos aportan brutalidad y resistencia; los Goblins, trampas y astucia. Su fuerza crece con la batalla.',\n                'base_hp' => 125,\n                'base_strength' => 20,\n                'base_magic' => 8,\n                'base_defense' => 14,\n                'base_speed' => 14,\n            ],\n            [\n                'name' => 'Hombres Bestia',\n                'access' => 'free',\n                'lore' => 'Engendros salvajes de bosques oscuros, atacan desde la maleza con furia primaria. Su estilo es directo, con golpes potentes y empuje constante.',\n                'base_hp' => 118,\n                'base_strength' => 19,\n                'base_magic' => 9,\n                'base_defense' => 13,\n                'base_speed' => 16,\n            ],\n            [\n                'name' => 'Skaven',\n                'access' => 'free',\n                'lore' => 'Las ratas del subsuelo se multiplican en sombras, traiciones y tecnología peligrosa. Rápidos y astutos, compensan su fragilidad con venenos, números y artimañas.',\n                'base_hp' => 88,\n                'base_strength' => 12,\n                'base_magic' => 14,\n                'base_defense' => 10,\n                'base_speed' => 23,\n            ],\n            [\n                'name' => 'Reyes Funerarios',\n                'access' => 'free',\n                'lore' => 'Reinos de polvo y eternidad, sus legiones no sienten miedo ni fatiga. Su poder reside en la disciplina ancestral, resistencia y magia ritual ligada a tumba y desierto.',\n                'base_hp' => 110,\n                'base_strength' => 16,\n                'base_magic' => 14,\n                'base_defense' => 17,\n                'base_speed' => 12,\n            ],\n            [\n                'name' => 'Hombres Lagarto',\n                'access' => 'free',\n                'lore' => 'Forjados para cumplir designios antiguos, combinan fuerza reptiliana con disciplina fría. Sus guerreros soportan el castigo y sus sabios canalizan poderes primordiales.',\n                'base_hp' => 130,\n                'base_strength' => 18,\n                'base_magic' => 16,\n                'base_defense' => 18,\n                'base_speed' => 12,\n            ],\n            [\n                'name' => 'Ogros',\n                'access' => 'free',\n                'lore' => 'Gigantes hambrientos, aplastan líneas con pura masa y ferocidad. Su magia es instintiva y su defensa nace de su dureza natural y armaduras improvisadas.',\n                'base_hp' => 140,\n                'base_strength' => 22,\n                'base_magic' => 10,\n                'base_defense' => 16,\n                'base_speed' => 12,\n            ],\n            [\n                'name' => 'Condes Vampiro',\n                'access' => 'free',\n                'lore' => 'Señores inmortales que gobiernan la noche. Su poder mezcla magia necromántica y fuerza sobrenatural, con defensas alimentadas por la voluntad y la sangre.',\n                'base_hp' => 112,\n                'base_strength' => 17,\n                'base_magic' => 18,\n                'base_defense' => 14,\n                'base_speed' => 16,\n            ],\n            [\n                'name' => 'Demonios del Caos',\n                'access' => 'premium',\n                'lore' => 'Manifestaciones del caos puro, existen para corromper y destruir. Su presencia distorsiona la realidad: magia feroz, golpes imprevisibles y resistencia antinatural.',\n                'base_hp' => 115,\n                'base_strength' => 18,\n                'base_magic' => 22,\n                'base_defense' => 14,\n                'base_speed' => 18,\n            ],\n            [\n                'name' => 'Guerreros del Caos',\n                'access' => 'admin',\n                'lore' => 'Veteranos endurecidos por el norte, juramentados a poderes oscuros. Su armadura es casi impenetrable y su fuerza implacable, a costa de una magia menos refinada.',\n                'base_hp' => 135,\n                'base_strength' => 22,\n                'base_magic' => 12,\n                'base_defense' => 22,\n                'base_speed' => 12,\n            ],\n        ];\n\n        $payload = array_map(function (array $race) use ($now) {\n            return array_merge($race, ['created_at' => $now, 'updated_at' => $now]);\n        }, $races);\n\n        DB::table('races')->upsert(\n            $payload,\n            ['name'],\n            ['access', 'lore', 'base_hp', 'base_strength', 'base_magic', 'base_defense', 'base_speed', 'updated_at']\n        );\n    }\n}\n
```

## database/seeders/EquipmentSlotsSeeder.php

```php
<?php\n\nnamespace Database\Seeders;\n\nuse Illuminate\Database\Seeder;\nuse Illuminate\Support\Facades\DB;\nuse Illuminate\Support\Facades\Date;\n\nclass EquipmentSlotsSeeder extends Seeder\n{\n    public function run(): void\n    {\n        $now = Date::now();\n\n        DB::table('equipment_slots')->upsert([\n            ['code' => 'head', 'name' => 'Cabeza', 'created_at' => $now, 'updated_at' => $now],\n            ['code' => 'chest', 'name' => 'Pecho', 'created_at' => $now, 'updated_at' => $now],\n            ['code' => 'legs', 'name' => 'Piernas', 'created_at' => $now, 'updated_at' => $now],\n            ['code' => 'weapon', 'name' => 'Arma', 'created_at' => $now, 'updated_at' => $now],\n            ['code' => 'accessory1', 'name' => 'Accesorio 1', 'created_at' => $now, 'updated_at' => $now],\n            ['code' => 'accessory2', 'name' => 'Accesorio 2', 'created_at' => $now, 'updated_at' => $now],\n        ], ['code'], ['name', 'updated_at']);\n    }\n}\n
```

## database/seeders/HeroesSeeder.php

```php
<?php\n\nnamespace Database\Seeders;\n\nuse Illuminate\Database\Seeder;\nuse Illuminate\Support\Facades\DB;\nuse Illuminate\Support\Facades\Date;\n\nclass HeroesSeeder extends Seeder\n{\n    public function run(): void\n    {\n        $now = Date::now();\n\n        $chaosWarriorsRaceId = DB::table('races')->where('name', 'Guerreros del Caos')->value('id');\n        if (!$chaosWarriorsRaceId) {\n            return;\n        }\n\n        DB::table('heroes')->upsert([\n            [\n                'race_id' => $chaosWarriorsRaceId,\n                'code' => 'archaon',\n                'name' => 'Archaon',\n                'description' => 'El Elegido de los Poderes Ruinosos. Un campeón único cuya mera presencia quiebra la moral enemiga. Su fuerza y defensa superan con claridad a cualquier guerrero común.',\n                'base_hp' => 160,\n                'base_strength' => 28,\n                'base_magic' => 18,\n                'base_defense' => 28,\n                'base_speed' => 16,\n                'unique_global' => true,\n                'created_at' => $now,\n                'updated_at' => $now,\n            ],\n        ], ['code'], ['race_id', 'name', 'description', 'base_hp', 'base_strength', 'base_magic', 'base_defense', 'base_speed', 'unique_global', 'updated_at']);\n    }\n}\n
```

## database/seeders/LootBaseSeeder.php

```php
<?php\n\nnamespace Database\Seeders;\n\nuse Illuminate\Database\Seeder;\nuse Illuminate\Support\Facades\DB;\nuse Illuminate\Support\Facades\Date;\n\nclass LootBaseSeeder extends Seeder\n{\n    public function run(): void\n    {\n        $now = Date::now();\n\n        $rarityCommonId = DB::table('rarities')->where('name', 'common')->value('id');\n        $rarityRareId = DB::table('rarities')->where('name', 'rare')->value('id');\n        $rarityEpicId = DB::table('rarities')->where('name', 'epic')->value('id');\n\n        if (!$rarityCommonId || !$rarityRareId || !$rarityEpicId) {\n            return;\n        }\n\n        DB::table('items')->upsert([\n            [\n                'name' => 'Espada de Hierro',\n                'type' => 'weapon',\n                'rarity_id' => $rarityCommonId,\n                'required_level' => 1,\n                'stackable' => false,\n                'bonus_hp' => 0,\n                'bonus_strength' => 2,\n                'bonus_magic' => 0,\n                'bonus_defense' => 0,\n                'bonus_speed' => 0,\n                'sell_price' => 10,\n                'created_at' => $now,\n                'updated_at' => $now,\n            ],\n            [\n                'name' => 'Armadura Reforzada',\n                'type' => 'armor',\n                'rarity_id' => $rarityRareId,\n                'required_level' => 5,\n                'stackable' => false,\n                'bonus_hp' => 5,\n                'bonus_strength' => 0,\n                'bonus_magic' => 0,\n                'bonus_defense' => 3,\n                'bonus_speed' => -1,\n                'sell_price' => 50,\n                'created_at' => $now,\n                'updated_at' => $now,\n            ],\n            [\n                'name' => 'Anillo Arcano',\n                'type' => 'accessory',\n                'rarity_id' => $rarityEpicId,\n                'required_level' => 20,\n                'stackable' => false,\n                'bonus_hp' => 0,\n                'bonus_strength' => 0,\n                'bonus_magic' => 6,\n                'bonus_defense' => 1,\n                'bonus_speed' => 1,\n                'sell_price' => 200,\n                'created_at' => $now,\n                'updated_at' => $now,\n            ],\n        ], ['name'], ['type', 'rarity_id', 'required_level', 'stackable', 'bonus_hp', 'bonus_strength', 'bonus_magic', 'bonus_defense', 'bonus_speed', 'sell_price', 'updated_at']);\n\n        DB::table('loot_tables')->upsert([\n            [\n                'name' => 'cofre_ganador_racial',\n                'rolls' => 3,\n                'min_level' => null,\n                'created_at' => $now,\n                'updated_at' => $now,\n            ],\n        ], ['name'], ['rolls', 'min_level', 'updated_at']);\n\n        $lootTableId = DB::table('loot_tables')->where('name', 'cofre_ganador_racial')->value('id');\n        if (!$lootTableId) {\n            return;\n        }\n\n        DB::table('chests')->upsert([\n            [\n                'name' => 'Cofre de Campeón Racial',\n                'loot_table_id' => $lootTableId,\n                'items_count' => 3,\n                'created_at' => $now,\n                'updated_at' => $now,\n            ],\n        ], ['name'], ['loot_table_id', 'items_count', 'updated_at']);\n\n        $itemCommonId = DB::table('items')->where('name', 'Espada de Hierro')->value('id');\n        $itemRareId = DB::table('items')->where('name', 'Armadura Reforzada')->value('id');\n        $itemEpicId = DB::table('items')->where('name', 'Anillo Arcano')->value('id');\n\n        if (!$itemCommonId || !$itemRareId || !$itemEpicId) {\n            return;\n        }\n\n        $existing = DB::table('loot_entries')\n            ->where('loot_table_id', $lootTableId)\n            ->count();\n\n        if ($existing === 0) {\n            DB::table('loot_entries')->insert([\n                [\n                    'loot_table_id' => $lootTableId,\n                    'entry_type' => 'item',\n                    'item_id' => $itemCommonId,\n                    'animal_id' => null,\n                    'weight' => 700,\n                    'min_qty' => 1,\n                    'max_qty' => 1,\n                    'required_level' => 1,\n                    'created_at' => $now,\n                    'updated_at' => $now,\n                ],\n                [\n                    'loot_table_id' => $lootTableId,\n                    'entry_type' => 'item',\n                    'item_id' => $itemRareId,\n                    'animal_id' => null,\n                    'weight' => 250,\n                    'min_qty' => 1,\n                    'max_qty' => 1,\n                    'required_level' => 5,\n                    'created_at' => $now,\n                    'updated_at' => $now,\n                ],\n                [\n                    'loot_table_id' => $lootTableId,\n                    'entry_type' => 'item',\n                    'item_id' => $itemEpicId,\n                    'animal_id' => null,\n                    'weight' => 45,\n                    'min_qty' => 1,\n                    'max_qty' => 1,\n                    'required_level' => 20,\n                    'created_at' => $now,\n                    'updated_at' => $now,\n                ],\n            ]);\n        }\n    }\n}\n
```

## app\Models\Animal.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\n\nclass Animal extends Model\n{\n    protected $fillable = [\n        'name',\n        'rarity_id',\n        'required_level',\n        'mountable',\n        'hp',\n        'strength',\n        'magic',\n        'defense',\n        'speed',\n        'description',\n    ];\n\n    protected $casts = [\n        'mountable' => 'boolean',\n    ];\n\n    public function rarity(): BelongsTo\n    {\n        return $this->belongsTo(Rarity::class);\n    }\n}\n
```

## app\Models\Character.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\nuse Illuminate\Database\Eloquent\Relations\BelongsToMany;\nuse Illuminate\Database\Eloquent\Relations\HasMany;\n\nclass Character extends Model\n{\n    protected $fillable = [\n        'user_id',\n        'race_id',\n        'hero_id',\n        'name',\n        'level',\n        'exp',\n        'gold',\n        'hp_bonus',\n        'strength_bonus',\n        'magic_bonus',\n        'defense_bonus',\n        'speed_bonus',\n        'active_animal_id',\n    ];\n\n    public function user(): BelongsTo\n    {\n        return $this->belongsTo(User::class);\n    }\n\n    public function race(): BelongsTo\n    {\n        return $this->belongsTo(Race::class);\n    }\n\n    public function hero(): BelongsTo\n    {\n        return $this->belongsTo(Hero::class);\n    }\n\n    public function items(): BelongsToMany\n    {\n        return $this->belongsToMany(Item::class, 'character_items')\n            ->withPivot(['quantity'])\n            ->withTimestamps();\n    }\n\n    public function equipment(): HasMany\n    {\n        return $this->hasMany(CharacterEquipment::class);\n    }\n\n    public function characterAnimals(): HasMany\n    {\n        return $this->hasMany(CharacterAnimal::class);\n    }\n\n    public function activeAnimal(): BelongsTo\n    {\n        return $this->belongsTo(Animal::class, 'active_animal_id');\n    }\n\n    public function missionRuns(): HasMany\n    {\n        return $this->hasMany(MissionRun::class);\n    }\n\n    public function matchParticipants(): HasMany\n    {\n        return $this->hasMany(MatchParticipant::class);\n    }\n}\n
```

## app\Models\CharacterAnimal.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\n\nclass CharacterAnimal extends Model\n{\n    protected $table = 'character_animals';\n\n    protected $fillable = [\n        'character_id',\n        'animal_id',\n        'acquired_at',\n    ];\n\n    protected $casts = [\n        'acquired_at' => 'datetime',\n    ];\n\n    public function character(): BelongsTo\n    {\n        return $this->belongsTo(Character::class);\n    }\n\n    public function animal(): BelongsTo\n    {\n        return $this->belongsTo(Animal::class);\n    }\n}\n
```

## app\Models\CharacterEquipment.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\n\nclass CharacterEquipment extends Model\n{\n    protected $table = 'character_equipment';\n\n    protected $fillable = [\n        'character_id',\n        'slot_id',\n        'item_id',\n    ];\n\n    public function character(): BelongsTo\n    {\n        return $this->belongsTo(Character::class);\n    }\n\n    public function slot(): BelongsTo\n    {\n        return $this->belongsTo(EquipmentSlot::class, 'slot_id');\n    }\n\n    public function item(): BelongsTo\n    {\n        return $this->belongsTo(Item::class);\n    }\n}\n
```

## app\Models\Chest.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\nuse Illuminate\Database\Eloquent\Relations\HasMany;\n\nclass Chest extends Model\n{\n    protected $fillable = [\n        'name',\n        'loot_table_id',\n        'items_count',\n    ];\n\n    public function lootTable(): BelongsTo\n    {\n        return $this->belongsTo(LootTable::class);\n    }\n\n    public function openings(): HasMany\n    {\n        return $this->hasMany(ChestOpening::class);\n    }\n}\n
```

## app\Models\ChestOpening.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\nuse Illuminate\Database\Eloquent\Relations\HasMany;\n\nclass ChestOpening extends Model\n{\n    protected $fillable = [\n        'chest_id',\n        'character_id',\n        'opened_at',\n    ];\n\n    protected $casts = [\n        'opened_at' => 'datetime',\n    ];\n\n    public function chest(): BelongsTo\n    {\n        return $this->belongsTo(Chest::class);\n    }\n\n    public function character(): BelongsTo\n    {\n        return $this->belongsTo(Character::class);\n    }\n\n    public function rewards(): HasMany\n    {\n        return $this->hasMany(ChestOpeningReward::class);\n    }\n}\n
```

## app\Models\ChestOpeningReward.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\n\nclass ChestOpeningReward extends Model\n{\n    protected $fillable = [\n        'chest_opening_id',\n        'item_id',\n        'quantity',\n    ];\n\n    public function chestOpening(): BelongsTo\n    {\n        return $this->belongsTo(ChestOpening::class);\n    }\n\n    public function item(): BelongsTo\n    {\n        return $this->belongsTo(Item::class);\n    }\n}\n
```

## app\Models\EquipmentSlot.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass EquipmentSlot extends Model\n{\n    protected $fillable = [\n        'code',\n        'name',\n    ];\n}\n
```

## app\Models\Hero.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\nuse Illuminate\Database\Eloquent\Relations\HasOne;\n\nclass Hero extends Model\n{\n    protected $fillable = [\n        'race_id',\n        'code',\n        'name',\n        'description',\n        'base_hp',\n        'base_strength',\n        'base_magic',\n        'base_defense',\n        'base_speed',\n        'unique_global',\n    ];\n\n    public function race(): BelongsTo\n    {\n        return $this->belongsTo(Race::class);\n    }\n\n    public function character(): HasOne\n    {\n        return $this->hasOne(Character::class);\n    }\n}\n
```

## app\Models\Item.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\n\nclass Item extends Model\n{\n    protected $fillable = [\n        'name',\n        'type',\n        'rarity_id',\n        'required_level',\n        'stackable',\n        'bonus_hp',\n        'bonus_strength',\n        'bonus_magic',\n        'bonus_defense',\n        'bonus_speed',\n        'sell_price',\n    ];\n\n    protected $casts = [\n        'stackable' => 'boolean',\n    ];\n\n    public function rarity(): BelongsTo\n    {\n        return $this->belongsTo(Rarity::class);\n    }\n}\n
```

## app\Models\LootEntry.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\n\nclass LootEntry extends Model\n{\n    protected $fillable = [\n        'loot_table_id',\n        'entry_type',\n        'item_id',\n        'animal_id',\n        'weight',\n        'min_qty',\n        'max_qty',\n        'required_level',\n    ];\n\n    public function lootTable(): BelongsTo\n    {\n        return $this->belongsTo(LootTable::class);\n    }\n\n    public function item(): BelongsTo\n    {\n        return $this->belongsTo(Item::class);\n    }\n\n    public function animal(): BelongsTo\n    {\n        return $this->belongsTo(Animal::class);\n    }\n}\n
```

## app\Models\LootTable.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\HasMany;\n\nclass LootTable extends Model\n{\n    protected $fillable = [\n        'name',\n        'rolls',\n        'min_level',\n    ];\n\n    public function entries(): HasMany\n    {\n        return $this->hasMany(LootEntry::class);\n    }\n}\n
```

## app\Models\MatchModel.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\HasMany;\n\nclass MatchModel extends Model\n{\n    protected $table = 'matches';\n\n    protected $fillable = [\n        'status',\n        'started_at',\n        'ended_at',\n    ];\n\n    protected $casts = [\n        'started_at' => 'datetime',\n        'ended_at' => 'datetime',\n    ];\n\n    public function participants(): HasMany\n    {\n        return $this->hasMany(MatchParticipant::class, 'match_id');\n    }\n\n    public function turns(): HasMany\n    {\n        return $this->hasMany(MatchTurn::class, 'match_id');\n    }\n}\n
```

## app\Models\MatchParticipant.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\n\nclass MatchParticipant extends Model\n{\n    protected $fillable = [\n        'match_id',\n        'character_id',\n        'is_winner',\n        'joined_at',\n    ];\n\n    protected $casts = [\n        'is_winner' => 'boolean',\n        'joined_at' => 'datetime',\n    ];\n\n    public function match(): BelongsTo\n    {\n        return $this->belongsTo(MatchModel::class, 'match_id');\n    }\n\n    public function character(): BelongsTo\n    {\n        return $this->belongsTo(Character::class);\n    }\n}\n
```

## app\Models\MatchTurn.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\n\nclass MatchTurn extends Model\n{\n    protected $fillable = [\n        'match_id',\n        'turn_number',\n        'acting_participant_id',\n        'action_text',\n        'metadata',\n    ];\n\n    protected $casts = [\n        'metadata' => 'array',\n    ];\n\n    public function match(): BelongsTo\n    {\n        return $this->belongsTo(MatchModel::class, 'match_id');\n    }\n\n    public function actingParticipant(): BelongsTo\n    {\n        return $this->belongsTo(MatchParticipant::class, 'acting_participant_id');\n    }\n}\n
```

## app\Models\Mission.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\HasMany;\n\nclass Mission extends Model\n{\n    protected $fillable = [\n        'title',\n        'description',\n        'min_level',\n        'base_difficulty',\n        'base_exp',\n        'base_points',\n    ];\n\n    public function nodes(): HasMany\n    {\n        return $this->hasMany(MissionNode::class);\n    }\n\n    public function runs(): HasMany\n    {\n        return $this->hasMany(MissionRun::class);\n    }\n}\n
```

## app\Models\MissionChoice.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\n\nclass MissionChoice extends Model\n{\n    protected $fillable = [\n        'node_id',\n        'choice_text',\n        'next_node_id',\n        'difficulty_delta',\n        'exp_multiplier',\n        'points_multiplier',\n        'loot_table_id',\n    ];\n\n    public function node(): BelongsTo\n    {\n        return $this->belongsTo(MissionNode::class, 'node_id');\n    }\n\n    public function nextNode(): BelongsTo\n    {\n        return $this->belongsTo(MissionNode::class, 'next_node_id');\n    }\n\n    public function lootTable(): BelongsTo\n    {\n        return $this->belongsTo(LootTable::class);\n    }\n}\n
```

## app\Models\MissionNode.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\nuse Illuminate\Database\Eloquent\Relations\HasMany;\n\nclass MissionNode extends Model\n{\n    protected $fillable = [\n        'mission_id',\n        'node_key',\n        'text',\n        'is_end',\n    ];\n\n    protected $casts = [\n        'is_end' => 'boolean',\n    ];\n\n    public function mission(): BelongsTo\n    {\n        return $this->belongsTo(Mission::class);\n    }\n\n    public function choices(): HasMany\n    {\n        return $this->hasMany(MissionChoice::class, 'node_id');\n    }\n}\n
```

## app\Models\MissionRun.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\nuse Illuminate\Database\Eloquent\Relations\HasMany;\n\nclass MissionRun extends Model\n{\n    protected $fillable = [\n        'mission_id',\n        'character_id',\n        'season_id',\n        'final_difficulty',\n        'exp_gained',\n        'points_gained',\n        'completed',\n        'started_at',\n        'ended_at',\n    ];\n\n    protected $casts = [\n        'completed' => 'boolean',\n        'started_at' => 'datetime',\n        'ended_at' => 'datetime',\n    ];\n\n    public function mission(): BelongsTo\n    {\n        return $this->belongsTo(Mission::class);\n    }\n\n    public function character(): BelongsTo\n    {\n        return $this->belongsTo(Character::class);\n    }\n\n    public function season(): BelongsTo\n    {\n        return $this->belongsTo(Season::class);\n    }\n\n    public function choices(): HasMany\n    {\n        return $this->hasMany(MissionRunChoice::class);\n    }\n}\n
```

## app\Models\MissionRunChoice.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\n\nclass MissionRunChoice extends Model\n{\n    protected $fillable = [\n        'mission_run_id',\n        'node_id',\n        'choice_id',\n        'step_number',\n    ];\n\n    public function missionRun(): BelongsTo\n    {\n        return $this->belongsTo(MissionRun::class);\n    }\n\n    public function node(): BelongsTo\n    {\n        return $this->belongsTo(MissionNode::class);\n    }\n\n    public function choice(): BelongsTo\n    {\n        return $this->belongsTo(MissionChoice::class);\n    }\n}\n
```

## app\Models\PremiumCode.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\nuse Illuminate\Database\Eloquent\Relations\HasMany;\n\nclass PremiumCode extends Model\n{\n    protected $fillable = [\n        'code_hash',\n        'max_uses',\n        'uses_count',\n        'is_active',\n        'created_by_user_id',\n    ];\n\n    protected $casts = [\n        'is_active' => 'boolean',\n    ];\n\n    public function redemptions(): HasMany\n    {\n        return $this->hasMany(PremiumCodeRedemption::class);\n    }\n\n    public function createdBy(): BelongsTo\n    {\n        return $this->belongsTo(User::class, 'created_by_user_id');\n    }\n}\n
```

## app\Models\PremiumCodeRedemption.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\n\nclass PremiumCodeRedemption extends Model\n{\n    protected $fillable = [\n        'premium_code_id',\n        'user_id',\n        'redeemed_at',\n    ];\n\n    protected $casts = [\n        'redeemed_at' => 'datetime',\n    ];\n\n    public function premiumCode(): BelongsTo\n    {\n        return $this->belongsTo(PremiumCode::class);\n    }\n\n    public function user(): BelongsTo\n    {\n        return $this->belongsTo(User::class);\n    }\n}\n
```

## app\Models\Race.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\HasMany;\n\nclass Race extends Model\n{\n    protected $fillable = [\n        'name',\n        'access',\n        'lore',\n        'base_hp',\n        'base_strength',\n        'base_magic',\n        'base_defense',\n        'base_speed',\n    ];\n\n    public function characters(): HasMany\n    {\n        return $this->hasMany(Character::class);\n    }\n\n    public function heroes(): HasMany\n    {\n        return $this->hasMany(Hero::class);\n    }\n}\n
```

## app\Models\Rarity.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\HasMany;\n\nclass Rarity extends Model\n{\n    protected $fillable = [\n        'name',\n        'weight',\n        'min_level',\n    ];\n\n    public function items(): HasMany\n    {\n        return $this->hasMany(Item::class);\n    }\n\n    public function animals(): HasMany\n    {\n        return $this->hasMany(Animal::class);\n    }\n}\n
```

## app\Models\Season.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\HasMany;\n\nclass Season extends Model\n{\n    protected $fillable = [\n        'year',\n        'month',\n        'starts_at',\n        'ends_at',\n    ];\n\n    protected $casts = [\n        'starts_at' => 'datetime',\n        'ends_at' => 'datetime',\n    ];\n\n    public function raceRankings(): HasMany\n    {\n        return $this->hasMany(SeasonRaceRanking::class);\n    }\n\n    public function raceWinners(): HasMany\n    {\n        return $this->hasMany(SeasonRaceWinner::class);\n    }\n}\n
```

## app\Models\SeasonRaceRanking.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\n\nclass SeasonRaceRanking extends Model\n{\n    protected $fillable = [\n        'season_id',\n        'race_id',\n        'character_id',\n        'points',\n    ];\n\n    public function season(): BelongsTo\n    {\n        return $this->belongsTo(Season::class);\n    }\n\n    public function race(): BelongsTo\n    {\n        return $this->belongsTo(Race::class);\n    }\n\n    public function character(): BelongsTo\n    {\n        return $this->belongsTo(Character::class);\n    }\n}\n
```

## app\Models\SeasonRaceWinner.php

```php
<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\n\nclass SeasonRaceWinner extends Model\n{\n    protected $fillable = [\n        'season_id',\n        'race_id',\n        'winner_character_id',\n        'chest_id',\n        'granted_at',\n        'claimed_at',\n    ];\n\n    protected $casts = [\n        'granted_at' => 'datetime',\n        'claimed_at' => 'datetime',\n    ];\n\n    public function season(): BelongsTo\n    {\n        return $this->belongsTo(Season::class);\n    }\n\n    public function race(): BelongsTo\n    {\n        return $this->belongsTo(Race::class);\n    }\n\n    public function winnerCharacter(): BelongsTo\n    {\n        return $this->belongsTo(Character::class, 'winner_character_id');\n    }\n\n    public function chest(): BelongsTo\n    {\n        return $this->belongsTo(Chest::class);\n    }\n}\n
```

## app\Models\User.php

```php
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'plan',
        'premium_granted_by_code_id',
        'premium_granted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'premium_granted_at' => 'datetime',
        ];
    }

    public function characters()
    {
        return $this->hasMany(Character::class);
    }

    public function premiumGrantedByCode()
    {
        return $this->belongsTo(PremiumCode::class, 'premium_granted_by_code_id');
    }
}


```

