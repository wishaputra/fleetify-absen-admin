<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Dashboard')); // dashboard baru

// departemen
Route::get('/departments', fn () => Inertia::render('Departments/Index'));
Route::get('/departments/create', fn () => Inertia::render('Departments/Create'));

// karyawan
Route::get('/employees', fn () => Inertia::render('Employees/Index'));
Route::get('/employees/create', fn () => Inertia::render('Employees/Create'));

// absen
Route::get('/attendance/logs', fn () => Inertia::render('Attendance/Logs'));
Route::get('/attendance/simulate', fn () => Inertia::render('Attendance/Simulate')); // opsional
