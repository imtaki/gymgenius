export enum difficultyLevel {
    beginner = 'beginner',
    intermediate = 'intermediate',
    advanced = 'advanced'
}

export enum muscleGroup {
    chest = 'chest',
    back = 'back',
    shoulders = 'shoulders',
    biceps = 'biceps',
    triceps = 'triceps',
    forearms = 'forearms',
    legs = 'legs',
    glutes = 'glutes',
    quads = 'quads',
    hamstrings = 'hamstrings',
    calves = 'calves',
    core = 'core',
    cardio = 'cardio',
}

export enum equipment {
    barbell = 'barbell',
    dumbbell = 'dumbbell',
    kettlebell = 'kettlebell',
    machine = 'machine',
    cable = 'cable',
    bodyweight = 'bodyweight',
    resistanceBand = 'resistance_band',
    medicineBall = 'medicine_ball',
}

export interface Exercise {
    name: string;
    muscleGroup: muscleGroup;
    secondaryMuscles?: string;
    description?: string;
    difficulty: difficultyLevel;
    equipment?: equipment;
}

export const MUSCLE_GROUPS = Object.values(muscleGroup).map(val => 
    val.charAt(0).toUpperCase() + val.slice(1).replace('_', ' ')
);

export const EQUIPMENT_OPTIONS = Object.values(equipment).map(val => 
    val.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
);
