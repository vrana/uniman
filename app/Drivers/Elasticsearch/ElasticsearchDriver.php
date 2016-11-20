<?php

namespace Adminerng\Drivers\Elasticsearch;

use Adminerng\Core\Driver\AbstractDriver;
use Adminerng\Drivers\Elasticsearch\Forms\ElasticsearchCredentialsForm;
use Elasticsearch\ClientBuilder;

class ElasticsearchDriver extends AbstractDriver
{
    const TYPE_TYPE = 'type';

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
            'hosts' => 'localhost:9200',
        ];
    }

    public function connect(array $credentials)
    {
        $hosts = array_map('trim', explode("\n", trim($credentials['hosts'])));
        $this->connection = ClientBuilder::create()
              ->setHosts($hosts)
              ->build();
    }

    protected function getHeaderManager()
    {
        return new ElasticsearchHeaderManager($this->dataManager());
    }

    protected function getDataManager()
    {
        return new ElasticsearchDataManager($this->connection);
    }
}
