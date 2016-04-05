<?php
namespace Memtext\Redis;

class RediskaAdapter implements RedisAdapterInterface
{
    private $client;
    private $prefix;

    public function __construct(\Rediska $client, $prefix = 'text')
    {
        $this->client = $client;
        $this->prefix = $prefix;
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

    public function hdel($hashName, array $fields)
    {
        $key = new \Rediska_Key_Hash($hashName);
        foreach ($fields as $field) {
            $key->remove($field);
        }
        return true;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }
}
