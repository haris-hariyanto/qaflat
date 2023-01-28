<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\MetaData as MetaDataTag;
use App\Helpers\Text;

class PageController extends Controller
{
    public function page($page)
    {
        $pageAvailables = [
            'about' => 'About Us',
            'privacy-policy' => 'Privacy Policy',
            'terms' => 'Terms and Conditions',
            'disclaimer' => 'Disclaimer',
            'dmca' => 'DMCA Copyright',
            'contact' => 'Contact',
        ];

        if (!array_key_exists($page, $pageAvailables)) {
            abort(404);
        }

        $pageTitle = $pageAvailables[$page];
        $pageContent = view('others.' . $page);

        $metaData = new MetaDataTag();
        $metaData->desc(Text::plain($pageContent, 160));
        $metaData->canonical(route('page', [$page]));

        return view('pages.page', compact('pageTitle', 'pageContent', 'metaData'));
    }
}
