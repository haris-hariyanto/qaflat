<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Text;
use App\Helpers\OpenGraph;
use App\Helpers\MetaData as MetaDataTag;
use App\Helpers\StructuredData;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    public function index($content)
    {
        if ($content == 'slug') {
            return redirect()->route('index');
        }
        
        // Get data
        $content = Storage::get('single-pages/' . $content . '.json');
        $content = json_decode($content, true);

        $pageTitle = Text::plain($content['question'], 100, true);
        $pageTitle = ucwords($pageTitle);
        
        $pageDesc = Text::plain($content['answers'][0]['answer'], 160, true);

        // Generate meta data
        $metaData = new MetaDataTag();
        $metaData->canonical(route('content', [$content['slug']]));
        $metaData->desc($pageDesc, 160, true);

        // Generate open graph
        $openGraph = new OpenGraph();
        $openGraph->url(route('content', [$content['slug']]));
        $openGraph->title($pageTitle);
        $openGraph->desc($pageDesc, 160, true);

        // Generate structured data
        $structuredData = new StructuredData();
        $structuredData->breadcrumb([
            ucwords($content['subject']) => route('subject', [Str::slug($content['subject'])]),
            $pageTitle => '',
        ]);

        $sdQuestionAnswers = [];
        $i = 1;
        foreach ($content['answers'] as $answer) {
            $sdQuestionAnswer = [];
            $sdQuestionAnswer['text'] = Text::plain($answer['answer']);
            $sdQuestionAnswer['upvoteCount'] = $answer['vote'];
            $sdQuestionAnswer['url'] = route('content', [$content['slug']]) . '#answer' . $i;
            $sdQuestionAnswer['isTop'] = $answer['is_best'] == 'Y' ? true : false;
            $sdQuestionAnswers[] = $sdQuestionAnswer;

            $i++;
        }

        $sdQuestion = [
            'questionShort' => ucwords(Text::plain($content['question'])),
            'questionFull' => ucwords(Text::plain($content['question'])),
            'answerCount' => count($content['answers']),
            'answers' => $sdQuestionAnswers,
        ];
        $structuredData->QA($sdQuestion);

        return view('pages.content', compact('content', 'pageTitle', 'metaData', 'openGraph', 'structuredData'));
    }
}
