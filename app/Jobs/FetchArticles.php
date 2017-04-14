<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client as Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class FetchArticles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $source;
    private $sortBy;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($source, $sortBy)
    {
        $this->source = $source;
        $this->sortBy = $sortBy;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $apiKey = config('integrations.newsapi.api_key');
        $client = new Http;
        $response = $client->request('GET', "https://newsapi.org/v1/articles?source={$this->source}&sortBy={$this->sortBy}&apiKey=$apiKey");

        $data = json_decode($response->getBody());
        Redis::set("articles:source:{$data->source}:sortBy:{$data->sortBy}", json_encode($data->articles));
    }
}
