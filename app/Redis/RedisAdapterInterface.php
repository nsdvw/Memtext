<?php
namespace Memtext\Redis;

interface RedisAdapterInterface
{
    function hmget($hashName, array $fields);
    function hmset($hashName, array $fields);
    function hgetall($hashName);
    function hdel($hashName, array $fields);
}
