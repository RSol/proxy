<?php

namespace app\components;

use app\components\interfaces\UrlGetter;
use JonnyW\PhantomJs\Client;

class SpysOneGetter implements UrlGetter
{

    /**
     * @param string $url
     * @param array $data
     * @return string
     */
    public function getFromUrl($url, $data = [])
    {
        $client = Client::getInstance();
        $client->getEngine()
            ->setPath(\Yii::getAlias('@app') . '/bin/phantomjs');

        $cookieFile = \Yii::getAlias('@runtime/cookies.txt');
        $client->getEngine()
            ->debug(true)
            ->addOption('--load-images=false')
            ->addOption("--cookies-file='{$cookieFile}'");

        $request = $client->getMessageFactory()->createRequest();
        $response = $client->getMessageFactory()->createResponse();

//        $request->setMethod('POST');
        $request->setUrl($url);
        if ($data) {
            $request->setRequestData($data);
        }
        $request->setHeaders([
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Accept-Encoding' => 'gzip, deflate',
            'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,uk;q=0.6',
            'Connection' => 'keep-alive',
            'Cookie' => '_ga=GA1.2.1160945105.1592222592; _gid=GA1.2.1058513855.1592222592',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Host' => 'spys.one',
            'Upgrade-Insecure-Requests' => '1',
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.97 Safari/537.36',
        ]);
        $request->setDelay(3);

        $client->send($request, $response);

//        \yii\helpers\VarDumper::dump($client->getLog(), 10, true);exit;
        \yii\helpers\VarDumper::dump($response, 10, true);exit;

        return $response->getContent();
    }
}
