<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MetaData;
use Illuminate\Support\Facades\Storage;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemapsTotal = MetaData::where('key', 'current_sitemap_iteration')->first();
        if (!$sitemapsTotal) {
            abort(404);
        }
        $sitemapsTotal = $sitemapsTotal->value;

        $sitemaps = [];
        for ($i = 1; $i <= $sitemapsTotal; $i++) {
            $sitemaps[] = route('sitemap.questions', [$i]);
        }

        return response()
            ->view('sitemap.index', compact('sitemaps'), 200)
            ->header('Content-Type', 'application/xml');
    }

    public function sitemapContents($index)
    {
        $fileName = 'sitemaps/sitemap-' . $index . '.json';
        if (Storage::missing($fileName)) {
            abort(404);
        }

        $sitemap = Storage::get($fileName);
        $sitemap = json_decode($sitemap, true);

        return response()
            ->view('sitemap.sitemap', compact('sitemap'), 200)
            ->header('Content-Type', 'application/xml');
    }
}
