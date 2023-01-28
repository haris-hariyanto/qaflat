<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($sitemap as $sitemapItem)
        <url>
            <loc>{{ $sitemapItem['url'] }}</loc>
            <lastmod>{{ date('Y-m-d', $sitemapItem['created']) }}</lastmod>
            
            @if (!empty(config('content.sitemap_changefreq')))
                <changefreq>{{ config('content.sitemap_changefreq') }}</changefreq>
            @endif

            @if (!empty(config('content.sitemap_priority')))
                <priority>{{ config('content.sitemap_priority') }}</priority>
            @endif
        </url>
    @endforeach
</urlset>