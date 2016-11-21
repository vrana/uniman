<?php

namespace Adminerng\Drivers\Elasticsearch;

class ElasticsearchApiClient
{
    private $baseUrl;

    public function __construct($host = 'localhost:9200')
    {
        $this->baseUrl = 'http://' . $host;
    }

    public function indices()
    {
        return $this->call('/_cat/indices?v&format=json&bytes=b');
    }

    public function types($index)
    {
        return $this->call("/$index");
    }

    public function count($index, $type)
    {
        return $this->call("/$index/$type/_count");
    }

    private function call($endpoint, $method = 'GET', $params = null)
    {
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($params) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $response = curl_exec($ch);

        curl_close($ch);
        if (!$response) {
            return [];
        }
        $result = json_decode((string)$response, true);
        return $result;
    }
}
