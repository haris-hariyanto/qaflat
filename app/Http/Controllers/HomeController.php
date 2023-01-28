<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MetaData;
use App\Helpers\OpenGraph;
use App\Helpers\MetaData as MetaDataTag;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $currentPage = $request->query('page', 1);

        $currentIndex = MetaData::where('key', 'current_index_iteration')->first();
        if (!$currentIndex) {
            abort(500);
        }
        $currentIndexValue = $currentIndex->value;
        if ($currentPage > $currentIndexValue) {
            abort(404);
        }

        $fileIndexToGet = $currentIndexValue - ($currentPage - 1);
        
        // Get data
        $questions = Storage::url('index-pages/index-' . $fileIndexToGet . '.json');
        $questions = json_decode($questions, true);

        // Generate pagination URL
        $nextPageURL = false;
        if ($fileIndexToGet > 1) {
            $nextPage = $currentPage + 1;
            $nextPageURL = route('index', ['page' => $nextPage]);
        }
        $prevPageURL = false;
        if ($fileIndexToGet < $currentIndexValue) {
            $prevPage = $currentPage - 1;
            $prevPageURL = route('index', ['page' => $prevPage]);
        }

        // Generate meta data
        $metaData = new MetaDataTag();
        if ($currentPage == 1) {
            $pageTitle = __('main.main_page_title');

            $metaData->canonical(route('index'));
            $metaData->desc(__('main.main_meta_desc'));
        }
        else {
            $pagePosition = __('main.page_position', ['position' => $currentPage]);

            $pageTitle = __('main.main_page_title') . ' - ' . $pagePosition;

            $metaData->canonical(route('index', ['page' => $currentPage]));
            $metaData->desc($pagePosition . '. ' . __('main.main_meta_desc'));
        }

        if ($nextPageURL) {
            $metaData->linkRelNext($nextPageURL);
        }

        if ($prevPageURL) {
            if ($currentPage == 2) {
                $metaData->linkRelPrev(route('index'));
            }
            else {
                $metaData->linkRelPrev($prevPageURL);
            }
        }

        // Generate open graph
        $openGraph = new OpenGraph();
        if ($currentPage == 1) {
            $openGraph->url(route('index'));
        }
        else {
            $openGraph->url(route('index', ['page' => $currentPage]));
        }
        $openGraph->title($pageTitle);
        $openGraph->desc(__('main.main_meta_desc'));

        return view('index', compact('questions', 'currentPage', 'nextPageURL', 'prevPageURL', 'metaData', 'pageTitle', 'openGraph'));
    }
}
