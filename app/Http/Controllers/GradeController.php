<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MetaData;
use App\Models\Grade;
use Illuminate\Support\Facades\Storage;

class GradeController extends Controller
{
    public function index($grade)
    {
        $currentIndex = MetaData::where('key', 'current_grade_iteration.' . $grade)->first();
        $gradeModel = Grade::where('slug', $grade)->first();
        if (!$currentIndex || !$gradeModel) {
            return redirect()->route('index');
        }
        $currentIndexValue = $currentIndex->value;

        // Get data
        $questions = Storage::get('grade-pages/grade-' . $grade . '-' . $currentIndexValue . '.json');
        $questions = json_decode($questions, true);

        $pageTitle = __('main.grade_page_title', ['grade' => $gradeModel->name]);

        return view('pages.grades', compact('questions', 'gradeModel', 'pageTitle'));
    }
}
