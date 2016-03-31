<?php
namespace Memtext\Mapper;

class AbstractMapper
{
    protected $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }
}
