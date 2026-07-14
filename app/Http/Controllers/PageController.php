<?php

namespace App\Http\Controllers;

use App\Models\Contact;

class PageController extends Controller
{
    public function service()
    {
        return view('pages.service', [
            'contacts' => Contact::where('is_active', true)->get(),
        ]);
    }

    public function about()
    {
        return view('pages.about', [
            'contacts' => Contact::where('is_active', true)->get(),
        ]);
    }

    public function articles()
    {
        return view('pages.articles');
    }
}
