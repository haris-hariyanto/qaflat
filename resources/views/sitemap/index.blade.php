<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<sitemapindex xmlns="http://www.sitemaps.org/">
    @foreach ($sitemaps as $sitemap)
        <sitemap>
            <loc>{{ $sitemap }}</loc>
            <lastmod>{{ date('Y-m-d') }}</lastmod>
        </sitemap>
    @endforeach
</sitemapindex>
