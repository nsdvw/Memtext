<?php
namespace Memtext\Mapper;

class WordMapper extends AbstractMapper
{
    public function save(array $words = [])
    {
        $sql = "INSERT INTO word (eng, rus) VALUES (:eng, :rus)";
        $sth = $this->connection->prepare($sql);
        $this->connection->beginTransaction();
        foreach ($words as $eng => $rus) {
            $sth->bindValue(':eng', $eng, \PDO::PARAM_STR);
            $sth->bindValue(':rus', $rus, \PDO::PARAM_STR);
            $sth->execute();
        }
        $this->connection->commit();
    }

    public function findByEng(array $eng)
    {
        $clause = implode(',', array_fill(0, count($eng), '?'));
        $sql = "SELECT id, eng, rus FROM word WHERE eng IN (" . $clause . ")";
        $sth = $this->connection->prepare($sql);
        $sth->setFetchMode(\PDO::FETCH_CLASS, '\Memtext\Model\Word');
        $sth->execute($eng);
        return $sth->fetchAll();
    }

    public function fetchArrayByEng(array $eng)
    {
        $arrayOfObjects = $this->findByEng($eng);
        $arrayOfSrings = [];
        foreach ($arrayOfObjects as $wordObject) {
            $arrayOfStrings[$wordObject->eng] = $wordObject->rus;
        }
        return $arrayOfStrings;
    }
}
