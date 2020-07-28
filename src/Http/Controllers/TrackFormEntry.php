<?php

namespace Day4\NovaForms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Day4\NovaForms\Models\FormEntry;

class TrackFormEntry extends Controller
{
    use ValidatesRequests;
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke($entryId)
    {
        $entry = FormEntry::findOrFail($entryId);
        $entry->read = true;
        $entry->save();

        return response(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mOUdbH6DwACvQGcA+AoMwAAAABJRU5ErkJggg=='))
            ->header('Content-Type', 'image/png');
    }
}
