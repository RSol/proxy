<?php

namespace app\components;

use app\components\interfaces\UrlGetter;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\CurlException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;
use yii\base\Component;
use PHPHtmlParser\Dom;
use yii\base\InvalidConfigException;

class SpysOneParser extends Component
{
    /**
     * @var string
     */
    public $baseUrl = 'http://spys.one/free-proxy-list/ALL/';

    /**
     * @var Dom
     */
    private $dom;

    /**
     * @var UrlGetter
     */
    private $urlGetter;

    /**
     * Init Dom
     */
    public function init()
    {
        parent::init();
        $this->dom = new Dom();
    }

    /**
     * Return array
     * <pre>
     * [
     * 0 => [
     * 'address' => '92.51.89.126'
     * 'type' => 'HTTPS'
     * 'anonymity' => 'HIA'
     * 'country' => 'GE'
     * 'port' => '49309'
     * ],
     * 1 => [
     * 'address' => '51.75.164.68'
     * 'type' => 'HTTP (Mikrotik)'
     * 'anonymity' => 'NOA'
     * 'country' => 'FR'
     * 'port' => '8080'
     * ],
     * ...
     * ]
     * </pre>
     * @return array
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws CurlException
     * @throws StrictException
     * @throws NotLoadedException
     * @throws InvalidConfigException
     */
    public function parse()
    {
        $data = $this->getData();
        $this->dom->load($data);
        $ports = $this->getTDPorts($data);

        return $this->parseTable($ports);
    }

    /**
     * @param $data
     * @return array
     */
    private function getTDPorts($data)
    {
        $params = $this->getScriptParams($data);
        $vars = $this->getPortsVar($params);
        $scripts = $this->getTDScripts($data);
        $results = [];
        uksort($vars, static function ($a, $b) {
            return strlen($a) < strlen($b);
        });
        $search = array_keys($vars);
        $replace = array_values($vars);
        foreach ($scripts as $script) {
            $port = 0;
            $str = '$port=' . str_replace($search, $replace, $script) . ';';
            eval($str);
            $results[] = $port;
        }
        return $results;
    }

    /**
     * @param $data
     * @return array
     */
    private function getTDScripts($data)
    {
        preg_match_all('/\<script type="text\/javascript"\>(document\.write.*)\<\/script\>/U', $data, $m);
        $result = [];
        foreach ($m[1] as $item) {
            $result[] = str_replace(['document.write("<font class=spy2>:<\/font>"+', '))', '+'], ['', ')', '.'], $item);
        }
        return $result;
    }

    /**
     * @param $data
     * @return false|string[]
     */
    private function getScriptParams($data)
    {
        preg_match('/\<script type="text\/javascript"\>(eval.*)\<\/script\>/U', $data, $m);
        $script = $m[1];
        preg_match_all('/\(\'(.*)\)/U', $script, $m);
        $params = explode(',', str_replace('\'', '', $m[1][1]));

        $params[3] = str_replace('.split(\u005e', '', $params[3]);

        return $params;
    }

    /**
     * @param $pots
     * @return array
     * @throws ChildNotFoundException
     * @throws NotLoadedException
     */
    private function parseTable($pots)
    {
        $contents = $this->dom->find('table table tr[onmouseover]');
        $result = [];
        /** @var Dom\HtmlNode $content */
        foreach ($contents as $i => $content) {
            $row = $this->parseRow($content);
            $row['port'] = $pots[$i];
            $result[] = $row;
        }
        return $result;
    }

    /**
     * @param Dom\HtmlNode $content
     * @return array
     * @throws ChildNotFoundException
     */
    private function parseRow($content)
    {
        $result = [];
        /** @var Dom\HtmlNode $item */
        foreach ($content->find('td') as $i => $item) {
            switch ($i) {
                case 0:
                    $result['address'] = $item->text(true);
                    break;
                case 1:
                    $result['type'] = $item->text(true);
                    break;
                case 2:
                    $result['anonymity'] = $item->text(true);
                    break;
                case 3:
                    $result['country'] = trim($item->find('font')->text());
                    break;
            }
        }
        return $result;
    }

    /**
     * @param $c
     * @param $r
     * @return string
     */
    private function y1($c, $r)
    {
        return ($c < $r
                ? ''
                : $this->y1((int)($c / $r), $r))
            . (($c %= $r) > 35
                ? chr($c + 29)
                : base_convert($c, 10, 36));
    }

    /**
     * @param $p
     * @param $r
     * @param $o
     * @param $x
     * @return string|string[]|null
     */
    private function jsEmulator($p, $r, $o, $x)
    {
        $s = [];
        $x = explode('^', $x);
        while ($o--) {
            $s[$this->y1($o, $r)] = $x[$o] ?: $this->y1($o, $r);
        }

        return preg_replace_callback('/\b\w\b/', static function ($matches) use ($s) {
            return $s[$matches[0]];
        }, trim($p, ';'));
    }

    /**
     * @param $params
     * @return array
     */
    private function getPortsVar($params)
    {
        $res = call_user_func_array([$this, 'jsEmulator'], $params);

        $result = [];
        foreach (explode(';', $res) as $item) {
            [$var, $exp] = explode('=', $item);
            if (strpos($exp, '^')) {
                $expn = [];
                foreach (explode('^', $exp) as $ep) {
                    $expn[] = is_numeric($ep) ? $ep : ('$' . $ep);
                }
                $exp = implode('^', $expn);
            }
            $eval = '$' . $var . '=' . $exp . ';';
            eval($eval);
            $result[$var] = $$var;
        }
        return $result;
    }

    /**
     * @return mixed
     * @throws InvalidConfigException
     */
    private function getData()
    {
        $data = $this->getUrlGetter()->getFromUrl($this->baseUrl, [
            'xpp' => 5,
            'xx0' => $this->getXx0(),
        ]);
        return str_replace("\n", '', $data);
    }

    private function getXx0()
    {
        $data = $this->getUrlGetter()->getFromUrl($this->baseUrl);
        $this->dom->load($data);
        /** @var Dom\HtmlNode $xx0 */
        $xx0 = $this->dom->find('[name="xx0"]');
        return $xx0->getAttribute('value');
    }

    /**
     * @return UrlGetter
     * @throws InvalidConfigException
     */
    public function getUrlGetter()
    {
        if ($this->urlGetter instanceof UrlGetter) {
            return $this->urlGetter;
        }
        throw new InvalidConfigException('UrlGetter should implement UrlGetter interface');
    }

    /**
     * @param UrlGetter|string $urlGetter
     * @throws InvalidConfigException
     */
    public function setUrlGetter($urlGetter)
    {
        if (is_string($urlGetter) && class_exists($urlGetter)) {
            $urlGetter = new $urlGetter();
        }
        if (!$urlGetter instanceof UrlGetter) {
            throw new InvalidConfigException('UrlGetter should implement UrlGetter interface');
        }
        $this->urlGetter = $urlGetter;
    }
}
