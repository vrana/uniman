<?php

namespace Adminerng\Drivers\Elasticsearch;

use Adminerng\Core\DataManager\AbstractDataManager;
use Adminerng\Core\Multisort;
use Elasticsearch\Client;

class ElasticsearchDataManager extends AbstractDataManager
{
    private $client;

    private $index;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function selectDatabase($database)
    {
        $this->index = $database;
    }

    public function databases(array $sorting = [])
    {
        $indicesStats = $this->client->indices()->stats();
        $indices = [];
        foreach ($indicesStats['indices'] as $name => $indice) {
            $indices[$name] = [
                'name' => $name,
            ];
        }
        return $indices;
    }

    public function tables(array $sorting = [])
    {
        $indice = $this->client->indices()->get(['index' => $this->index]);
        $types = [
            ElasticsearchDriver::TYPE_TYPE => [],
        ];
        foreach ($indice[$this->index]['mappings'] as $type => $typeMapping) {
            $types[ElasticsearchDriver::TYPE_TYPE][$type] = [
                'type' => $type,
            ];
        }
        return $types;
    }

    public function itemsCount($type, $table, array $filter = [])
    {
        $stats = $this->client->count(['index' => $this->index, 'type' => $table]);
        return $stats['count'];
    }

    public function items($type, $table, $page, $onPage, array $filter = [], array $sorting = [])
    {
        $params = [
            'index' => $this->index,
            'type' => $table,
            'size' => $onPage,
            'from' => ($page - 1) * $onPage,
        ];

        $sortings = [];
        foreach ($sorting as $sort) {
            foreach ($sort as $field => $direction) {
                $sortings[] = "$field:$direction";
            }
        }
        $params['sort'] = implode(',', $sortings);

        $result = $this->client->search($params);
        $items = [];
        foreach ($result['hits']['hits'] as $hit) {
            $item = [
                '_id' => $hit['_id'],
            ];
            $item = array_merge($item, $hit['_source']);
            $items[$hit['_id']] = $item;
        }
        return $items;
    }

    public function getColumns($table)
    {
        $mappings = $this->client->indices()->getMapping(['index' => $this->index, 'type' => $table]);
        return $mappings[$this->index]['mappings'][$table]['properties'];
    }
}
