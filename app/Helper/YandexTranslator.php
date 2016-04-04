<?php
namespace Memtext\Helper;

class YandexTranslator implements TranslatorInterface
{
    private $api;
    private $key;
    private $lang;

    public function __construct($api, $key)
    {
        $this->api = $api;
        $this->key = $key;
        $this->lang = 'en-ru';
    }

    public function translate(array $words)
    {
        $url = $this->buildUrl($words);
        $rawObj = json_decode($this->connectAndExec($url));
        return $rawObj->text;
    }

    protected function connectAndExec($url)
    {
        $conn = curl_init($url);
        curl_setopt($conn, \CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($conn, \CURLOPT_RETURNTRANSFER, true);
        return curl_exec($conn);
    }

    protected function buildUrl(array $words)
    {
        $url = "{$this->api}?key={$this->key}&lang={$this->lang}";
        foreach ($words as $word) {
            $url .= "&text={$word}";
        }
        return $url;
    }
}
