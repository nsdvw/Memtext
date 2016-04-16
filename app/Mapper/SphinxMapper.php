<?php
namespace Memtext\Mapper;

use Doctrine\DBAL\Connection;

class SphinxMapper
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function find(array $words, $indexName)
    {
        $sql = "SELECT id FROM {$indexName} WHERE MATCH(?) LIMIT 10000".
                "OPTION ranker=sph04";
        $conn = $this->connection;
        $sth = $conn->prepare($sql);
        $sth->execute([ join('|', $words) ]);
        $ids = [];
        while ($id = $sth->fetchColumn()) {
            $ids[] = $id;
        }
        return $ids;
    }
}
