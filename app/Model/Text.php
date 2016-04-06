<?php
namespace Memtext\Model;

class Text extends AbstractModel
{
    private $id;
    private $content;
    private $dictionary;
    private $title;
    private $user_id;

    public function ignore(array $words)
    {
        $dictionary = $this->dictionary;
        if ($dictionary === null) {
            return;
        }
        foreach ($words as $word) {
            foreach ($dictionary as &$row) {
                if ($row['eng'] !== $word) {
                    continue;
                }
                $row['ignore'] = true;
                break;
            }
        }
        unset($row);
        $this->dictionary = $dictionary;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getDictionary()
    {
        return $this->dictionary;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getUser_id()
    {
        return $this->user_id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setDictionary($dictionary)
    {
        $this->dictionary = $dictionary;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setUser_id($user_id)
    {
        $this->user_id = $user_id;
    }
}
