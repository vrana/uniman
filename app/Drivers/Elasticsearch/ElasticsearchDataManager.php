<?php

namespace Adminerng\Drivers\Elasticsearch;

use Adminerng\Core\DataManager\AbstractDataManager;
use Adminerng\Core\Multisort;
use Elasticsearch\Client;
use Exception;

class ElasticsearchDataManager extends AbstractDataManager
{
    private $client;

    private $apiClient;

    private $index;

    public function __construct(Client $client, ElasticsearchApiClient $apiClient)
    {
        $this->client = $client;
        $this->apiClient = $apiClient;
    }

    public function selectDatabase($database)
    {
        $this->index = $database;
    }

    public function databases(array $sorting = [])
    {
        $indices = [];
        foreach ($this->apiClient->indices() as $index) {
            $indices[$index['index']] = [
                'index' => $index['index'],
                'health' => $index['health'],
                'status' => $index['status'],
                'primaries' => $index['pri'],
                'replicas' => $index['rep'],
                'documents_count' => $index['docs.count'],
                'documents_deleted' => $index['docs.deleted'],
                'store_size' => $index['store.size'],
                'primary_store_size' => $index['pri.store.size'],
            ];
        }
        return Multisort::sort($indices, $sorting);
    }

    public function tables(array $sorting = [])
    {
        $index = $this->apiClient->types($this->index);
        $types = [
            ElasticsearchDriver::TYPE_TYPE => [],
        ];
        foreach (array_keys($index[$this->index]['mappings']) as $type) {
            $types[ElasticsearchDriver::TYPE_TYPE][$type] = [
                'type' => $type,
            ];
        }

        return [
            ElasticsearchDriver::TYPE_TYPE => Multisort::sort($types[ElasticsearchDriver::TYPE_TYPE], $sorting),
        ];
    }

    public function itemsCount($type, $table, array $filter = [])
    {
        if (empty($filter)) {
            $stats = $this->apiClient->count($this->index, $table);
            return $stats['count'];
        }
        throw new Exception('Elastic search - count with filter. Not yet implemented');
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
            foreach ($hit['_source'] as $property => $value) {
                $item[$property] = is_array($value) ? json_encode($value) : $value;
            }
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
