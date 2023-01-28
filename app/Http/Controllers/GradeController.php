<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MetaData;
use App\Models\Grade;
use Illuminate\Support\Facades\Storage;
use App\Helpers\OpenGraph;
use App\Helpers\MetaData as MetaDataTag;

class GradeController extends Controller
{
    public function index(Request $request, $grade)
    {
        $currentPage = $request->query('page', 1);

        $currentIndex = MetaData::where('key', 'current_grade_iteration.' . $grade)->first();
        $gradeModel = Grade::where('slug', $grade)->first();
        if (!$currentIndex || !$gradeModel) {
            abort(404);
        }
        $currentIndexValue = $currentIndex->value;
        if ($currentPage > $currentIndexValue) {
            abort(404);
        }

        $fileIndexToGet = $currentIndexValue - ($currentPage - 1);

        // Get data
        $questions = Storage::get('grade-pages/grade-' . $grade . '-' . $fileIndexToGet . '.json');
        $questions = json_decode($questions, true);

        // Generate pagination URL
        $nextPageURL = false;
        if ($fileIndexToGet > 1) {
            $nextPage = $currentPage + 1;
            $nextPageURL = route('grade', [$grade, 'page' => $nextPage]);
        }
        $prevPageURL = false;
        if ($fileIndexToGet < $currentIndexValue) {
            $prevPage = $currentPage - 1;
            $prevPageURL = route('grade', [$grade, 'page' => $prevPage]);
        }

        $pageTitle = __('main.grade_page_title', ['grade' => $gradeModel->name]);
        $pageDesc = __('main.grade_meta_desc', ['grade' => $gradeModel->name]);

        // Generate meta data
        $metaData = new MetaDataTag();
        if ($currentPage == 1) {
            $metaData->canonical(route('grade', [$grade]));
            $metaData->desc($pageDesc);
        }
        else {
            $pagePosition = __('main.page_position', ['position' => $currentPage]);

            $pageTitle = $pageTitle . ' - ' . $pagePosition;

            $metaData->canonical(route('grade', [$grade, 'page' => $currentPage]));
            $metaData->desc($pagePosition . '. ' . $pageDesc);
        }

        if ($nextPage) {
            $metaData->linkRelNext($nextPageURL);
        }

        if ($prevPageURL) {
            if ($currentPage == 2) {
                $metaData->linkRelPrev(route('grade', [$grade]));
            }
            else {
                $metaData->linkRelPrev($prevPageURL);
            }
        }

        // Generate open graph
        $openGraph = new OpenGraph();
        if ($currentPage == 1) {
            $openGraph->url(route('grade', [$grade]));
        }
        else {
            $openGraph->url(route('grade', [$grade, 'page' => $currentPage]));
        }
        $openGraph->title($pageTitle);
        $openGraph->desc($pageDesc);

        return view('pages.grades', compact('questions', 'gradeModel', 'pageTitle', 'currentPage', 'nextPageURL', 'prevPageURL', 'metaData', 'openGraph'));
    }
}
