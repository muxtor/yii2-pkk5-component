yii2-pkk5-component
=================


Расширение предназначено для получение и вывести данны с pkk5.rosreestr.ru (парсер)
Можете использовать расширение с [yii2-pkk5-module](http://github.com/muxtor/yii2-pkk5-module) 
  
Установка
------------------
* Установка пакета с помощью Composer
```
composer require muxtor/yii2-pkk5-component
```

Использование
------------------
```
'components' => [
        ...
        'pkk5parser' => [
            'class' => 'muxtor\pkk5component\Pkk5Component',
            'apiLink'=>'http://pkk5.rosreestr.ru/api/features/1',
            'idLink'=>'?text=',
            'fullLink'=>'/',
            'cache' => true,//default false
            'cacheDuration' => 60//default 3600
        ],
        ...
    ],
```
и использовать где угодно:
```
Yii::$app->pkk5parser->getInfo($text); //$text это кадастроый номер типа: 69:27:0000022:1306,69:27:0000022:1307,69:27:0000022:1306,69:27:0000022:1307
```