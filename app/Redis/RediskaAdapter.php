<?php
namespace Memtext\Redis;

class RediskaAdapter implements RedisAdapterInterface
{
    private $client;

    public function __construct(\Rediska $client)
    {
        $this->client = $client;
    }

    public function hmget($hashName, array $fields)
    {
        $key = new \Rediska_Key_Hash($hashName);
        $result = $key->get($fields);
        return array_combine($fields, $result);
    }

    public function hmset($hashName, array $fields)
    {
        $key = new \Rediska_Key_Hash($hashName);
        $key->set($fields);
        return true;
    }

    public function hgetall($hashName)
    {
        $key = new \Rediska_Key_Hash($hashName);
        return $key->getFieldsAndValues();
    }
}
