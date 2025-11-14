<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ScheduleController;

Route::get('/', function () {
    return view('pages.home.hero');
})->name('home');

Route::get('/', [StatsController::class, 'index'])->name('home');

Route::get('/konaba-ami', function () {
    return view('pages.about');
})->name('about');

Route::get('/horariu', [ScheduleController::class, 'index'])->name('schedule');
Route::get('/horariu/download', [ScheduleController::class, 'download'])->name('schedule.download');

Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');

Route::get('/lista-estudante', [StudentController::class, 'index'])->name('students.index');

Route::get('/portal-informasaun', [NewsController::class, 'index'])->name('news.index');
Route::get('/portal-informasaun/{slug}', [NewsController::class, 'show'])->name('news.show');

Route::get('/news/category/{slug}', [App\Http\Controllers\NewsController::class, 'category'])->name('news.category');
Route::get('/news/tag/{slug}', [App\Http\Controllers\NewsController::class, 'tag'])->name('news.tag');



