<?php
namespace Memtext\Model;

class Word extends AbstractModel
{
    private $id;
    private $eng;
    private $rus;

    public function getId()
    {
        return $this->id;
    }

    public function getEng()
    {
        return $this->eng;
    }

    public function getRus()
    {
        return $this->rus;
    }

    public function setEng($eng)
    {
        $this->eng = $eng;
    }

    public function setRus($rus)
    {
        $this->rus = $rus;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
}
