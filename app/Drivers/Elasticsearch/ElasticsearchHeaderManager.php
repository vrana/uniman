<?php

namespace Adminerng\Drivers\Elasticsearch;

use Adminerng\Core\Column;
use Adminerng\Core\ListingHeaders\HeaderManagerInterface;

class ElasticsearchHeaderManager implements HeaderManagerInterface
{
    private $dataManager;

    public function __construct(ElasticsearchDataManager $dataManager)
    {
        $this->dataManager = $dataManager;
    }

    public function databasesHeaders()
    {
        $columns = [];
        $columns[] = (new Column())
            ->setKey('index')
            ->setTitle('elasticsearch.headers.database.index')
            ->setIsSortable(true);
        $columns[] = (new Column())
            ->setKey('health')
            ->setTitle('elasticsearch.headers.database.health')
            ->setIsSortable(true);
        $columns[] = (new Column())
            ->setKey('status')
            ->setTitle('elasticsearch.headers.database.status')
            ->setIsSortable(true);
        $columns[] = (new Column())
            ->setKey('primaries')
            ->setTitle('elasticsearch.headers.database.primaries')
            ->setIsSortable(true)
            ->setIsNumeric(true);
        $columns[] = (new Column())
            ->setKey('replicas')
            ->setTitle('elasticsearch.headers.database.replicas')
            ->setIsSortable(true)
            ->setIsNumeric(true);
        $columns[] = (new Column())
            ->setKey('documents_count')
            ->setTitle('elasticsearch.headers.database.documents_count')
            ->setIsSortable(true)
            ->setIsNumeric(true);
        $columns[] = (new Column())
            ->setKey('documents_deleted')
            ->setTitle('elasticsearch.headers.database.documents_deleted')
            ->setIsSortable(true)
            ->setIsNumeric(true);
        $columns[] = (new Column())
            ->setKey('store_size')
            ->setTitle('elasticsearch.headers.database.store_size')
            ->setIsSortable(true)
            ->setIsNumeric(true)
            ->setIsSize(true);
        $columns[] = (new Column())
            ->setKey('primary_store_size')
            ->setTitle('elasticsearch.headers.database.primary_store_size')
            ->setIsSortable(true)
            ->setIsNumeric(true)
            ->setIsSize(true);
        return $columns;
    }

    public function tablesHeaders()
    {
        $columns = [];
        $columns[] = (new Column())
            ->setKey('type')
            ->setTitle('elasticsearch.headers.table.type')
        ;
        return [ElasticsearchDriver::TYPE_TYPE => $columns];
    }

    public function itemsHeaders($type, $table)
    {
        $columns = [];
        $properties = $this->dataManager->getColumns($table);
//        print_R($properties);exit();
        foreach ($properties as $property => $propertySettings) {
            $column = (new Column())
                ->setKey($property)
                ->setTitle($property)
                ->setIsSortable(true)
            ;
            if (isset($propertySettings['type']) && $propertySettings['type'] == 'integer') {
                $column->setIsNumeric(true);
            }
            $columns[] = $column;
        }
//        print_R($columns);exit();
        return $columns;
    }
}
