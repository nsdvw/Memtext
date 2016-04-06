<?php
namespace Memtext\Mapper;

use \Memtext\Model\Text;

class TextMapper extends AbstractMapper
{
    public function save(Text $text)
    {
        $sql = "INSERT INTO `text` (content, dictionary, title, user_id)
                VALUES (:content, :dictionary, :title, :user_id)";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':content', $text->content, \PDO::PARAM_STR);
        $text->dictionary = $this->serialize($text->dictionary);
        $sth->bindValue(':dictionary', $text->dictionary, \PDO::PARAM_STR);
        $sth->bindValue(':title', $text->title, \PDO::PARAM_STR);
        $sth->bindValue(':user_id', $text->user_id, \PDO::PARAM_INT);
        $sth->execute();
        $text->id = $this->connection->lastInsertId();
    }

    public function findById($id)
    {
        $sql = "SELECT id, content, dictionary, title, user_id
                FROM `text`
                WHERE id=:id";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':id', $id, \PDO::PARAM_INT);
        $sth->execute();
        $sth->setFetchMode(\PDO::FETCH_CLASS, '\Memtext\Model\Text');
        $text = $sth->fetch();
        $text->dictionary = $this->unserialize($text->dictionary);
        return $text;
    }

    public function findAllByUserId($user_id, $rows = 20, $offset = 0)
    {
        $sql = "SELECT id, content, dictionary, title, user_id
                FROM `text`
                WHERE user_id=:user_id
                LIMIT :offset, :rows";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
        $sth->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $sth->bindValue(':rows', $rows, \PDO::PARAM_INT);
        $sth->execute();
        $sth->setFetchMode(\PDO::FETCH_CLASS, '\Memtext\Model\Text');
        $texts = $sth->fetchAll();
        array_walk($texts, function ($text) {
            $text->dictionary = $this->unserialize($text->dictionary);
        });
        return $texts;
    }

    public function getTextCountByUserId($user_id)
    {
        $sql = "SELECT count(*) FROM `text` WHERE user_id = :user_id";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetchColumn();
    }

    public function getAuthorId($textId)
    {
        $sql = "SELECT user_id FROM `text` WHERE id=:id";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':id', $textId, \PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetchColumn();
    }

    public function delete($textId)
    {
        $sql = "DELETE FROM `text` WHERE id=:id";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':id', $textId, \PDO::PARAM_INT);
        $sth->execute();
    }

    public function update(Text $text)
    {
        $sql = "UPDATE `text`
                SET title=:title, content=:content, dictionary=:dictionary
                WHERE id=:id";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':title', $text->title, \PDO::PARAM_STR);
        $sth->bindValue(':content', $text->content, \PDO::PARAM_STR);
        $text->dictionary = $this->serialize($text->dictionary);
        $sth->bindValue(':dictionary', $text->dictionary, \PDO::PARAM_STR);
        $sth->bindValue(':id', $text->id, \PDO::PARAM_INT);
        $sth->execute();
    }

    private function serialize($dictionary)
    {
        return json_encode($dictionary);
    }

    private function unserialize($dictionary)
    {
        return json_decode($dictionary, true);
    }
}
