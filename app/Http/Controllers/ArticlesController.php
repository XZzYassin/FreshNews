<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as Http;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Redis;
use App\Jobs\FetchArticles;

class ArticlesController extends Controller
{
    public function fetchArticles($source, $sortBy = 'latest', Request $request)
    {
        dispatch(
            (new FetchArticles($source, $sortBy))
                ->onQueue('fetching_articles')
                ->onConnection('redis')
        );

        $responseData = Redis::get("articles:source:$source:sortBy:$sortBy");
        return json_decode($responseData);
    }
}
