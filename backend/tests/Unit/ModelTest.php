<?php

use App\Models\User;

uses(Tests\TestCase::class);
uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('User Model', function () {
    describe('relationships', function () {
        test('user has many exercises', function () {
            $user = User::factory()->create();
            $exercise = \App\Models\Exercise::factory()->for($user)->create();

            $this->assertTrue($user->exercises->contains($exercise));
        });

        test('user has many meals', function () {
            $user = User::factory()->create();
            $dailyLog = \App\Models\DailyLog::factory()->for($user)->create();
            $meal = \App\Models\Meal::factory()->for($user)->for($dailyLog)->create();

            $this->assertTrue($user->meals->contains($meal));
        });

        test('user has one settings', function () {
            $user = User::factory()->create();

            $this->assertNotNull($user->settings);
            $this->assertEquals($user->id, $user->settings->user_id);
        });

        test('user has many workout logs', function () {
            $user = User::factory()->create();
            $workoutLog = \App\Models\WorkoutLog::factory()->for($user)->create();

            $this->assertTrue($user->workoutLogs->contains($workoutLog));
        });

        test('user has many workout programs', function () {
            $user = User::factory()->create();
            $program = \App\Models\WorkoutProgram::factory()->for($user)->create();

            $this->assertTrue($user->workoutPrograms->contains($program));
        });
    });

    describe('JWT implementation', function () {
        test('user has JWT identifier', function () {
            $user = User::factory()->create();

            $identifier = $user->getJWTIdentifier();
            $this->assertEquals($user->id, $identifier);
        });

        test('user has JWT custom claims', function () {
            $user = User::factory()->create();

            $claims = $user->getJWTCustomClaims();
            $this->assertIsArray($claims);
        });
    });

    describe('user settings creation', function () {
        test('user settings are auto-created on user creation', function () {
            $user = User::factory()->create();

            $this->assertNotNull($user->settings);
            $this->assertEquals($user->id, $user->settings->user_id);
        });

        test('user settings have default values', function () {
            $user = User::factory()->create();

            $this->assertNotNull($user->settings->goal_type);
            $this->assertNotNull($user->settings->caloric_goal);
        });
    });
});

describe('Exercise Model', function () {
    describe('relationships', function () {
        test('exercise belongs to user', function () {
            $user = User::factory()->create();
            $exercise = \App\Models\Exercise::factory()->for($user)->create();

            $this->assertTrue($exercise->user->is($user));
        });

        test('exercise has many workout logs', function () {
            $user = User::factory()->create();
            $exercise = \App\Models\Exercise::factory()->for($user)->create();
            $workoutLog = \App\Models\WorkoutLog::factory()->for($user)->for($exercise)->create();

            $this->assertTrue($exercise->workoutLogs->contains($workoutLog));
        });
    });
});

describe('Meal Model', function () {
    describe('relationships', function () {
        test('meal belongs to user', function () {
            $user = User::factory()->create();
            $dailyLog = \App\Models\DailyLog::factory()->for($user)->create();
            $meal = \App\Models\Meal::factory()->for($user)->for($dailyLog)->create();

            $this->assertTrue($meal->user->is($user));
        });

        test('meal belongs to daily log', function () {
            $user = User::factory()->create();
            $dailyLog = \App\Models\DailyLog::factory()->for($user)->create();
            $meal = \App\Models\Meal::factory()->for($user)->for($dailyLog)->create();

            $this->assertTrue($meal->dailyLog->is($dailyLog));
        });
    });

    describe('field casting', function () {
        test('numeric fields are cast to float', function () {
            $user = User::factory()->create();
            $dailyLog = \App\Models\DailyLog::factory()->for($user)->create();
            $meal = \App\Models\Meal::factory()->for($user)->for($dailyLog)->create([
                'calories' => 150,
                'protein' => 25.5,
                'carbs' => 10,
                'fats' => 3.2,
            ]);

            $this->assertIsFloat($meal->calories) || $this->assertIsInt($meal->calories);
            $this->assertIsFloat($meal->protein) || $this->assertIsInt($meal->protein);
        });
    });
});

describe('DailyLog Model', function () {
    describe('relationships', function () {
        test('daily log has many meals', function () {
            $user = User::factory()->create();
            $dailyLog = \App\Models\DailyLog::factory()->for($user)->create();
            $meal = \App\Models\Meal::factory()->for($user)->for($dailyLog)->create();

            $this->assertTrue($dailyLog->meals->contains($meal));
        });

        test('daily log belongs to user', function () {
            $user = User::factory()->create();
            $dailyLog = \App\Models\DailyLog::factory()->for($user)->create();

            $this->assertTrue($dailyLog->user->is($user));
        });
    });
});

describe('WorkoutLog Model', function () {
    describe('relationships', function () {
        test('workout log belongs to user', function () {
            $user = User::factory()->create();
            $exercise = \App\Models\Exercise::factory()->for($user)->create();
            $workoutLog = \App\Models\WorkoutLog::factory()->for($user)->for($exercise)->create();

            $this->assertTrue($workoutLog->user->is($user));
        });

        test('workout log belongs to exercise', function () {
            $user = User::factory()->create();
            $exercise = \App\Models\Exercise::factory()->for($user)->create();
            $workoutLog = \App\Models\WorkoutLog::factory()->for($user)->for($exercise)->create();

            $this->assertTrue($workoutLog->exercise->is($exercise));
        });
    });
});

describe('WorkoutProgram Model', function () {
    describe('relationships', function () {
        test('workout program belongs to user', function () {
            $user = User::factory()->create();
            $program = \App\Models\WorkoutProgram::factory()->for($user)->create();

            $this->assertTrue($program->user->is($user));
        });
    });
});

describe('UserSettings Model', function () {
    describe('relationships', function () {
        test('user settings belongs to user', function () {
            $user = User::factory()->create();
            $settings = $user->settings;

            $this->assertTrue($settings->user->is($user));
        });
    });

    describe('field casting', function () {
        test('numeric fields are properly cast', function () {
            $user = User::factory()->create();
            $settings = $user->settings;
            $settings->update([
                'height' => 180,
                'age' => 30,
                'current_weight' => 75.5,
                'target_weight' => 70.0,
                'caloric_goal' => 2500,
            ]);

            $this->assertIsInt($settings->age) || $this->assertIsFloat($settings->age);
            $this->assertIsFloat($settings->current_weight) || $this->assertIsInt($settings->current_weight);
        });
    });
});
