<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TimetableController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Redirect to login page
    return redirect('/login');
});

Route::get('/dashboard', function () {
    // Redirect to semesters page
    return redirect('/semesters');
})->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/timetable/{semester}/{lecturer}', [LecturerController::class, 'getTimetable'])
    ->middleware('signed')
    ->name('lecturer.timetable');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/lecturers', [LecturerController::class, 'getIndex']);
    Route::get('/lecturers/index', [LecturerController::class, 'getIndex']);
    Route::get('/lecturers/show/{id}', [LecturerController::class, 'getShow']);
    Route::get('/lecturers/add', [LecturerController::class, 'getAdd']);
    Route::post('/lecturers/save', [LecturerController::class, 'postSave']);
    Route::get('/lecturers/edit/{id}', [LecturerController::class, 'getEdit']);
    Route::post('/lecturers/update/{id}', [LecturerController::class, 'postUpdate']);
    Route::get('/lecturers/delete/{id}', [LecturerController::class, 'getDelete']);
    Route::get('/lecturers/json', [LecturerController::class, 'getJson']);
    Route::get('lecturers/import', [LecturerController::class, 'getImportForm'])->name('lecturers.import');
    Route::post('lecturers/import', [LecturerController::class, 'postImportCsv'])->name('lecturers.import.post');

    Route::get('/semesters', [SemesterController::class, 'getIndex']);
    Route::get('/semesters/index', [SemesterController::class, 'getIndex']);
    Route::get('/semesters/show/{id}', [SemesterController::class, 'getShow']);
    Route::get('/semesters/add', [SemesterController::class, 'getAdd']);
    Route::post('/semesters/save', [SemesterController::class, 'postSave']);
    Route::get('/semesters/edit/{id}', [SemesterController::class, 'getEdit']);
    Route::post('/semesters/update/{id}', [SemesterController::class, 'postUpdate']);
    Route::get('/semesters/delete/{id}', [SemesterController::class, 'getDelete']);
    Route::get('/semesters/timetables/{semesterId}', [SemesterController::class, 'showSemesterTimetables']);

    Route::get('/semesters/json', [SemesterController::class, 'getJson']);

    // Start process of notifying all lectures
    Route::get('/semesters/run/{id}', [SemesterController::class, 'getRun']);

    Route::get('/timetables', [TimetableController::class, 'getIndex']);
    Route::get('/timetables/index', [TimetableController::class, 'getIndex']);
    Route::get('/timetables/show/{id}', [TimetableController::class, 'getShow'])
        ->name('timetable.show');
    Route::get('/timetables/add', [TimetableController::class, 'getAdd']);
    Route::get('/timetables/edit/{id}', [TimetableController::class, 'getEdit']);
    Route::post('/timetables/update/{id}', [TimetableController::class, 'postUpdate']);
    Route::get('/timetables/delete/{id}', [TimetableController::class, 'getDelete']);
    Route::get('/timetables/json', [TimetableController::class, 'getJson']);

    Route::get('/notifications', [NotificationController::class, 'overview']);
    Route::get('/notifications/index', [NotificationController::class, 'overview']);
    Route::get('/notifications/pending', [TimetableController::class, 'pendingLecturers']);
    Route::get('/notifications/json', [NotificationController::class, 'getJson']);
});

// Trigger cronjob to send notifications to lecturers
Route::get('/notifications/send', [NotificationController::class, 'sendNotification']);

// Trigger cronjob to send overdue notifications to lecturers
Route::get('/notifications/overdue', [NotificationController::class, 'markOverdue']);

// Save timetable --> Also possible without being logged in
Route::post('/timetables/save', [TimetableController::class, 'postSave']);


require __DIR__ . '/auth.php';
