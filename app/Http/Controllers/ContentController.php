<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Text;

class ContentController extends Controller
{
    public function index($content)
    {
        // Get data
        $content = Storage::get('single-pages/' . $content . '.json');
        $content = json_decode($content, true);

        $pageTitle = Text::plain($content['question'], 100, true);
        $pageTitle = ucwords($pageTitle);

        return view('pages.content', compact('content', 'pageTitle'));
    }
}
