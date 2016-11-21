<?php

namespace Adminerng\Drivers\Elasticsearch;

use Adminerng\Core\Driver\AbstractDriver;
use Adminerng\Drivers\Elasticsearch\Forms\ElasticsearchCredentialsForm;
use Elasticsearch\ClientBuilder;

class ElasticsearchDriver extends AbstractDriver
{
    const TYPE_TYPE = 'type';

    private $client;

    public function type()
    {
        return 'elasticsearch';
    }

    public function extensions()
    {
        return ['curl'];
    }

    public function getCredentialsForm()
    {
        return new ElasticsearchCredentialsForm();
    }

    public function defaultCredentials()
    {
        return [
            'host' => 'localhost:9200',
        ];
    }

    public function connect(array $credentials)
    {
        $host = trim($credentials['host']);
        $this->connection = ClientBuilder::create()
              ->setHosts([$host])
              ->build();
          $this->client = new ElasticsearchApiClient($host);
    }

    protected function getHeaderManager()
    {
        return new ElasticsearchHeaderManager($this->dataManager());
    }

    protected function getDataManager()
    {
        return new ElasticsearchDataManager($this->connection, $this->client);
    }
}
