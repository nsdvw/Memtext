<?php
namespace Memtext\Model;

class Text extends AbstractModel
{
    private $id;
    private $content;
    private $title;
    private $user_id;

    public function getId()
    {
        return $this->id;
    }

    public function getContent()
    {
        return $this->content;
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

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setUser_id($user_id)
    {
        $this->user_id = $user_id;
    }
}
