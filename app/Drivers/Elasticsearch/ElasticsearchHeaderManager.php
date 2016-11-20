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
            ->setKey('name')
            ->setTitle('elasticsearch.headers.database.name')
        ;
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
        foreach ($properties as $property => $propertySettings) {
            $column = (new Column())
                ->setKey($property)
                ->setTitle($property)
                ->setIsSortable(true)
            ;
            if ($propertySettings['type'] == 'integer') {
                $column->setIsNumeric(true);
            }
            $columns[] = $column;
        }
        return $columns;
    }
}
