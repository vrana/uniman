<?php

namespace Adminerng\Drivers\Redis;

class PredisDriver extends RedisDriver
{
    public function check()
    {
        return class_exists('Predis\Client');
    }

    public function extensions()
    {
        return [];
    }

    public function classes()
    {
        return ['Predis\Client'];
    }

    public function type()
    {
        return 'predis';
    }

    protected function setDriverOrder()
    {
        $this->connection->setDriversOrder(['predis']);
    }
}
