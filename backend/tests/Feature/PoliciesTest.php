<?php

use App\Models\Exercise;
use App\Models\Meal;
use App\Models\User;
use App\Models\DailyLog;
use Illuminate\Support\Facades\Gate;

uses(Tests\TestCase::class);
uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('ExercisePolicy', function () {
    describe('view', function () {
        test('user can view their own exercise', function () {
            $user = User::factory()->create();
            $exercise = Exercise::factory()->for($user)->create();

            $this->assertTrue(Gate::forUser($user)->allows('view', $exercise));
        });

        test('user can view another user\'s exercise', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $exercise = Exercise::factory()->for($user1)->create();

            // Different users can view each other's exercises
            $this->assertTrue(Gate::forUser($user2)->allows('view', $exercise));
        });
    });

    describe('create', function () {
        test('any authenticated user can create exercise', function () {
            $user = User::factory()->create();

            $this->assertTrue(Gate::forUser($user)->allows('create', Exercise::class));
        });
    });

    describe('update', function () {
        test('user can update own exercise', function () {
            $user = User::factory()->create();
            $exercise = Exercise::factory()->for($user)->create();

            $this->assertTrue(Gate::forUser($user)->allows('update', $exercise));
        });

        test('user cannot update another user\'s exercise', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $exercise = Exercise::factory()->for($user1)->create();

            $this->assertFalse(Gate::forUser($user2)->allows('update', $exercise));
        });
    });

    describe('delete', function () {
        test('user can delete own exercise', function () {
            $user = User::factory()->create();
            $exercise = Exercise::factory()->for($user)->create();

            $this->assertTrue(Gate::forUser($user)->allows('delete', $exercise));
        });

        test('user cannot delete another user\'s exercise', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $exercise = Exercise::factory()->for($user1)->create();

            $this->assertFalse(Gate::forUser($user2)->allows('delete', $exercise));
        });
    });
});

describe('MealPolicy', function () {
    describe('viewAny', function () {
        test('user can view their own meals', function () {
            $user = User::factory()->create();
            $dailyLog = DailyLog::factory()->for($user)->create();
            Meal::factory()->for($user)->for($dailyLog)->create();

            // MealPolicy requires user->id === userId parameter
            $this->assertTrue(
                Gate::forUser($user)->allows('viewAny', [Meal::class, $user->id])
            );
        });

        test('user cannot view another user\'s meals', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();

            $this->assertFalse(
                Gate::forUser($user1)->allows('viewAny', [Meal::class, $user2->id])
            );
        });
    });

    describe('view', function () {
        test('user can view their own meal', function () {
            $user = User::factory()->create();
            $dailyLog = DailyLog::factory()->for($user)->create();
            $meal = Meal::factory()->for($user)->for($dailyLog)->create();

            $this->assertTrue(Gate::forUser($user)->allows('view', $meal));
        });

        test('user cannot view another user\'s meal', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $dailyLog = DailyLog::factory()->for($user1)->create();
            $meal = Meal::factory()->for($user1)->for($dailyLog)->create();

            $this->assertFalse(Gate::forUser($user2)->allows('view', $meal));
        });
    });

    describe('create', function () {
        test('user can create meal for themselves', function () {
            $user = User::factory()->create();

            $this->assertTrue(
                Gate::forUser($user)->allows('create', [Meal::class, $user->id])
            );
        });

        test('user cannot create meal for another user', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();

            $this->assertFalse(
                Gate::forUser($user1)->allows('create', [Meal::class, $user2->id])
            );
        });
    });

    describe('update', function () {
        test('user can update own meal', function () {
            $user = User::factory()->create();
            $dailyLog = DailyLog::factory()->for($user)->create();
            $meal = Meal::factory()->for($user)->for($dailyLog)->create();

            $this->assertTrue(Gate::forUser($user)->allows('update', $meal));
        });

        test('user cannot update another user\'s meal', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $dailyLog = DailyLog::factory()->for($user1)->create();
            $meal = Meal::factory()->for($user1)->for($dailyLog)->create();

            $this->assertFalse(Gate::forUser($user2)->allows('update', $meal));
        });
    });

    describe('delete', function () {
        test('user can delete own meal', function () {
            $user = User::factory()->create();
            $dailyLog = DailyLog::factory()->for($user)->create();
            $meal = Meal::factory()->for($user)->for($dailyLog)->create();

            $this->assertTrue(Gate::forUser($user)->allows('delete', $meal));
        });

        test('user cannot delete another user\'s meal', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $dailyLog = DailyLog::factory()->for($user1)->create();
            $meal = Meal::factory()->for($user1)->for($dailyLog)->create();

            $this->assertFalse(Gate::forUser($user2)->allows('delete', $meal));
        });
    });
});

describe('DailyLogPolicy', function () {
    describe('viewAny', function () {
        test('user can view their own daily logs', function () {
            $user = User::factory()->create();
            DailyLog::factory()->for($user)->create();

            $this->assertTrue(
                Gate::forUser($user)->allows('viewAny', [DailyLog::class, $user->id])
            );
        });

        test('user cannot view another user\'s daily logs', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();

            $this->assertFalse(
                Gate::forUser($user1)->allows('viewAny', [DailyLog::class, $user2->id])
            );
        });
    });
});

describe('UserSettingsPolicy', function () {
    describe('view', function () {
        test('user can view their own settings', function () {
            $user = User::factory()->create();

            $this->assertTrue(Gate::forUser($user)->allows('view', $user->settings));
        });

        test('user cannot view another user\'s settings', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();

            $this->assertFalse(Gate::forUser($user1)->allows('view', $user2->settings));
        });
    });

    describe('update', function () {
        test('user can update their own settings', function () {
            $user = User::factory()->create();

            $this->assertTrue(Gate::forUser($user)->allows('update', $user->settings));
        });

        test('user cannot update another user\'s settings', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();

            $this->assertFalse(Gate::forUser($user1)->allows('update', $user2->settings));
        });
    });
});

describe('WorkoutProgramPolicy', function () {
    describe('view', function () {
        test('user can view their own workout program', function () {
            $user = User::factory()->create();
            $program = \App\Models\WorkoutProgram::factory()->for($user)->create();

            $this->assertTrue(Gate::forUser($user)->allows('view', $program));
        });

        test('user cannot view another user\'s workout program', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $program = \App\Models\WorkoutProgram::factory()->for($user1)->create();

            $this->assertFalse(Gate::forUser($user2)->allows('view', $program));
        });
    });

    describe('update', function () {
        test('user can update their own workout program', function () {
            $user = User::factory()->create();
            $program = \App\Models\WorkoutProgram::factory()->for($user)->create();

            $this->assertTrue(Gate::forUser($user)->allows('update', $program));
        });

        test('user cannot update another user\'s workout program', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $program = \App\Models\WorkoutProgram::factory()->for($user1)->create();

            $this->assertFalse(Gate::forUser($user2)->allows('update', $program));
        });
    });

    describe('delete', function () {
        test('user can delete their own workout program', function () {
            $user = User::factory()->create();
            $program = \App\Models\WorkoutProgram::factory()->for($user)->create();

            $this->assertTrue(Gate::forUser($user)->allows('delete', $program));
        });

        test('user cannot delete another user\'s workout program', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $program = \App\Models\WorkoutProgram::factory()->for($user1)->create();

            $this->assertFalse(Gate::forUser($user2)->allows('delete', $program));
        });
    });
});
