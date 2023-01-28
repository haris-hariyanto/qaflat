<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MetaData;
use App\Models\Subject;
use Illuminate\Support\Facades\Storage;
use App\Helpers\OpenGraph;
use App\Helpers\MetaData as MetaDataTag;

class SubjectController extends Controller
{
    public function index(Request $request, $subject)
    {
        $currentPage = $request->query('page', 1);

        $currentIndex = MetaData::where('key', 'current_subject_iteration.' . $subject)->first();
        $subjectModel = Subject::where('slug', $subject)->first();
        if (!$currentIndex || !$subjectModel) {
            abort(404);
        }
        $currentIndexValue = $currentIndex->value;
        if ($currentPage > $currentIndexValue) {
            abort(404);
        }

        $fileIndexToGet = $currentIndexValue - ($currentPage - 1);

        // Get data
        $questions = Storage::get('subject-pages/subject-' . $subject . '-' . $fileIndexToGet . '.json');
        $questions = json_decode($questions, true);

        // Generate pagination URL
        $nextPageURL = false;
        if ($fileIndexToGet > 1) {
            $nextPage = $currentPage + 1;
            $nextPageURL = route('subject', [$subject, 'page' => $nextPage]);
        }
        $prevPageURL = false;
        if ($fileIndexToGet < $currentIndexValue) {
            $prevPage = $currentPage - 1;
            $prevPageURL = route('subject', [$subject, 'page' => $prevPage]);
        }

        $pageTitle = __('main.subject_page_title', ['subject' => $subjectModel->name]);
        $pageDesc = __('main.subject_meta_desc', ['subject' => $subjectModel->name]);

        // Generate meta data
        $metaData = new MetaDataTag();
        if ($currentPage == 1) {
            $metaData->canonical(route('subject', [$subject]));
            $metaData->desc($pageDesc);
        }
        else {
            $pagePosition = __('main.page_position', ['position' => $currentPage]);

            $pageTitle = $pageTitle . ' - ' . $pagePosition;

            $metaData->canonical(route('subject', [$subject, 'page' => $currentPage]));
            $metaData->desc($pagePosition . '. ' . $pageDesc);
        }

        if ($nextPageURL) {
            $metaData->linkRelNext($nextPageURL);
        }

        if ($prevPageURL) {
            if ($currentPage == 2) {
                $metaData->linkRelPrev(route('subject', [$subject]));
            }
            else {
                $metaData->linkRelPrev($prevPageURL);
            }
        }

        // Generate open graph
        $openGraph = new OpenGraph();
        if ($currentPage == 1) {
            $openGraph->url(route('subject', [$subject]));
        }
        else {
            $openGraph->url(route('subject', [$subject, 'page' => $currentPage]));
        }
        $openGraph->title($pageTitle);
        $openGraph->desc($pageDesc);

        return view('pages.subject', compact('questions', 'subjectModel', 'pageTitle', 'currentPage', 'nextPageURL', 'prevPageURL', 'metaData', 'openGraph'));
    }
}
