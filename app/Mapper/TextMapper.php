<?php
namespace Memtext\Mapper;

use \Memtext\Model\Text;

class TextMapper extends AbstractMapper
{
    public function save(Text $text, User $user)
    {
        $sql = "INSERT INTO `text` (content, user_id)
                VALUES (:content, :user_id)";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':content', $text->content, \PDO::PARAM_STR);
        $sth->bindValue(':user_id', $user->id, \PDO::PARAM_INT);
        $sth->execute();
        $text->id = $this->connection->lastInsertId();
    }

    public function findById($id)
    {
        $sql = "SELECT id, content, user_id
                FROM `text`
                WHERE id=:id";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':id', $id, \PDO::PARAM_INT);
        $sth->execute();
        $sth->setFetchMode(\PDO::FETCH_CLASS, '\Memtext\Model\Text');
        return $sth->fetch();
    }

    public function findAllByUserId($user_id, $rows = 20, $offset = 0)
    {
        $sql = "SELECT id, content, user_id
                FROM `text`
                WHERE user_id=:user_id
                LIMIT :offset, :rows";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
        $sth->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $sth->bindValue(':rows', $rows, \PDO::PARAM_INT);
        $sth->execute();
        $sth->setFetchMode(\PDO::FETCH_CLASS, '\Memtext\Model\Text');
        return $sth->fetchAll();
    }
}
