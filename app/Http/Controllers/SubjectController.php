<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MetaData;
use App\Models\Subject;
use Illuminate\Support\Facades\Storage;

class SubjectController extends Controller
{
    public function index($subject)
    {
        $currentIndex = MetaData::where('key', 'current_subject_iteration.' . $subject)->first();
        $subjectModel = Subject::where('slug', $subject)->first();
        if (!$currentIndex || !$subjectModel) {
            return redirect()->route('index');
        }
        $currentIndexValue = $currentIndex->value;

        // Get data
        $questions = Storage::get('subject-pages/subject-' . $subject . '-' . $currentIndexValue . '.json');
        $questions = json_decode($questions, true);

        $pageTitle = __('main.subject_page_title', ['subject' => $subjectModel->name]);

        return view('pages.subject', compact('questions', 'subjectModel', 'pageTitle'));
    }
}
