<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\CatalogFeedService;

class CatalogFeedController extends Controller
{
    protected CatalogFeedService $feedService;

    public function __construct(CatalogFeedService $feedService)
    {
        $this->feedService = $feedService;
    }

    /**
     * Facebook Product Catalog Feed (XML RSS 2.0 format)
     * Compatible with Facebook Commerce Manager & Instagram Shopping
     */
    public function facebookXml(Request $request)
    {
        $forceRefresh = $request->has('refresh') || $request->has('nocache');
        $xml = $this->feedService->getXmlFeed('facebook', $forceRefresh);

        return response($xml, 200, [
            'Content-Type'        => 'application/xml; charset=UTF-8',
            'X-Robots-Tag'        => 'noindex, follow',
            'Cache-Control'       => 'public, max-age=3600',
        ]);
    }

    /**
     * Google Merchant Center Product Feed (XML RSS 2.0 format)
     * Compatible with Google Shopping & Google Merchant Center
     */
    public function googleXml(Request $request)
    {
        $forceRefresh = $request->has('refresh') || $request->has('nocache');
        $xml = $this->feedService->getXmlFeed('google', $forceRefresh);

        return response($xml, 200, [
            'Content-Type'        => 'application/xml; charset=UTF-8',
            'X-Robots-Tag'        => 'noindex, follow',
            'Cache-Control'       => 'public, max-age=3600',
        ]);
    }

    /**
     * Universal Product Feed (XML format)
     */
    public function universalXml(Request $request)
    {
        $forceRefresh = $request->has('refresh') || $request->has('nocache');
        $xml = $this->feedService->getXmlFeed('universal', $forceRefresh);

        return response($xml, 200, [
            'Content-Type'        => 'application/xml; charset=UTF-8',
            'X-Robots-Tag'        => 'noindex, follow',
            'Cache-Control'       => 'public, max-age=3600',
        ]);
    }

    /**
     * Facebook Product Catalog Feed (CSV format)
     * For manual upload or scheduled feed in CSV format
     */
    public function facebookCsv(Request $request)
    {
        $forceRefresh = $request->has('refresh') || $request->has('nocache');
        $csv = $this->feedService->getCsvFeed($forceRefresh);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="facebook-catalog.csv"',
            'X-Robots-Tag'        => 'noindex, follow',
            'Cache-Control'       => 'public, max-age=3600',
        ]);
    }
}
