<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;

class ElasticService
{
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts(config('elasticsearch.host')) // array of hosts
            ->build();
    }

    public function getClient()
    {
        return $this->client;
    }

    public function bulkIndex(string $index, array $documents)
    {
        if (empty($documents)) {
            return;
        }

        $params = ['body' => []];

        foreach ($documents as $doc) {
            $params['body'][] = [
                'index' => [
                    '_index' => $index,
                    '_id' => $doc['id'] ?? null,
                ]
            ];
            $params['body'][] = $doc;
        }

        return $this->client->bulk($params);
    }
}
