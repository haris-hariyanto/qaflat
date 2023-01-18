<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MetaData;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        $currentIndex = MetaData::where('key', 'current_index_iteration')->first();
        if (!$currentIndex) {
            return redirect()->route('index');
        }
        $currentIndexValue = $currentIndex->value;
        
        // Get data
        $questions = Storage::get('index-pages/index-' . $currentIndexValue . '.json');
        dd('index-pages/index-' . $currentIndexValue . '.json');
        $questions = json_decode($questions, true);

        return view('index', compact('questions'));
    }
}
