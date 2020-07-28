<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/track/{entryId}', 'TrackFormEntry')->name('track_form_entry')->middleware('signed');