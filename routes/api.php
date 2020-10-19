<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/track-form/{entryId}', 'TrackFormEntry')->name('track_form_entry')->middleware('signed');