<?php

namespace muxtor\pkk5component;

/**
 * Pkk5Component
 *
 * @author Ulugbek Mukhtorov <muxtorsoft@gmail.com>
 * @version 0.0.1
 *
 * Парсер по API http://pkk5.rosreestr.ru/api/features/1/
 */

class Pkk5Component extends \yii\base\Component {

    /**
     * @var string
     */
    public $apiLink='http://pkk5.rosreestr.ru/api/features/1';
    /**
     * @var string
     */
    public $idLink='?text=';
    /**
     * @var string
     */
    public $fullLink='/';

    /**
     * @var bool
     * Кэшерование по каждому кадастр номеру
     */
    public $cache = false;

    /**
     * @var int
     */
    public $cacheDuration = 3600;

    /**
     * @param array $config
     */
    public function __cosnstruct(array $config = [])
    {
        parent::__construct($config);

        //настройка конфигурации
        $this->apiLink          = isset($config['apiLink'])?$config['apiLink']:$this->apiLink;
        $this->idLink           = isset($config['idLink'])?$config['idLink']:$this->idLink;
        $this->fullLink         = isset($config['fullLink'])?$config['fullLink']:$this->fullLink;
        $this->cache            = isset($config['cache'])?(boolean)$config['cache']:$this->cache;
        $this->cacheDuration    = isset($config['cacheDuration'])?(int)$config['cacheDuration']:$this->cacheDuration;
    }

    /**
     * @param $kadastrNumbers
     * @return array
     * Запрос и получение инфо с парсера
     */
    public function getInfo($kadastrNumbers)
    {
        return $this->ExplodeKadastr($kadastrNumbers);
    }

    /**
     * @param $kadastrText
     * @return array
     * Полчить данны для каждому кад. номер.
     */
    private function ExplodeKadastr($kadastrText)
    {
        $kadastrs = explode(',',$kadastrText);
        $gettedKadastrs = [];
        foreach ($kadastrs as $kadastr){
            if($this->cache){ //c кэш
                $cache = \Yii::$app->cache;
                $data = $cache->get($kadastr);
                if ($data == false) {
                    $data = $this->setInfo($this->Parse($kadastr));
                    // Хранить данные в кэше
                    $cache->set($kadastr, $data, $this->cacheDuration);
                }
                $gettedKadastrs[$kadastr] = $data;
            }else{ //без кэш
                $gettedKadastrs[$kadastr] = $this->setInfo($this->Parse($kadastr));
            }
        }
        return $gettedKadastrs;
    }

    /**
     * @param $datas
     * @return array|null
     * Задать полученный данны в свои усмотрению
     */
    private function setInfo($datas)
    {
        $info = [];

        if($datas!=null){
            $data = $datas->feature->attrs;//данны с сервера API

            $info['kadastrnumber'] = $data->cn;       //1.	Кадастровый номер
            $info['address'] = $data->address;        //2.	Адрес
            $info['cost'] = $data->cad_cost;          //3.	Кадастровая цена (рубли)
            $info['area'] = $data->area_value;        //4.	Площадь (квадратные метры)
        }else{
            $info = null;
        }

        return $info;
    }

    /**
     * @param $getIdInfo
     * @return mixed
     * просто Json decoder
     */
    public function JsonDecode($getIdInfo)
    {
        return json_decode($getIdInfo);
    }

    /**
     * @param $kadastr
     * @return mixed|null
     * Получить id кадастр
     */
    private function Parse($kadastr)
    {
        $getIdInfo = (new RemoteLoad)->load($this->apiLink.$this->idLink.$kadastr);
        if($getIdInfo){
            $json = $this->JsonDecode($getIdInfo);
            if(!empty($json->features)){
                return $this->FullParse($json->features[0]->attrs->id);
            }
        }
        return null;
    }

    /**
     * @param $kadastrId
     * @return mixed|null
     * Получить данны с помощю Кадастр ID
     */
    private function FullParse($kadastrId)
    {
        $getFullInfo = (new RemoteLoad)->load($this->apiLink.$this->fullLink.$kadastrId);

        if($getFullInfo){
            return $this->JsonDecode($getFullInfo);
        }
        return null;
    }

}
