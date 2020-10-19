<?php

namespace Day4\NovaForms\Http\Controllers;

use Day4\NovaForms\Models\FormEntry;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class TrackFormEntry extends Controller
{
    use ValidatesRequests;

    /**
     * Handle the incoming request.
     *
     * @param $entryId
     * @return Response
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
