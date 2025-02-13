<?php

use App\Http\Controllers\EventLogController;
use Illuminate\Support\Facades\Route;

Route::get('/event-logs', [EventLogController::class, 'index']);