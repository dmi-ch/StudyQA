<?php

namespace studyqa\model;


class Translate {

    var $headerOptions;
    var $lang=0;
    var $countryId = 1; // Russia

    function __construct(){
        $this->headerOptions = array(
            'http' => array(
                'method' => "GET",
                'header' => "Accept-language: en\r\n" .
                    "Cookie: remixlang=$this->lang\r\n"
            )
        );
    }
    public function translate($text,$lang){
        $methodUrlGetTranslation = 'https://translate.yandex.net/api/v1.5/tr.json/translate?key=trnsl.1.1.20141204T135639Z.2e562d7f850b0006.d77d3bc4d9eaaf61b12575bcabff6e1857654c4b&lang=ru-'.$lang.'&text='.$text;
        try{

            $streamContext = stream_context_create($this->headerOptions);
            $json = file_get_contents($methodUrlGetTranslation, false, $streamContext);
        }catch (Exception $e){
            echo('ERROR '.$e);
            exit();
        }

        $result=json_decode($json, true);
        if (!empty($result)) {
             if($result['code']=='200'){
                 $translation=$result['text'][0];
             }else{
                 echo 'Erorr:'.$result['code'];
             }
        }else{
            echo 'Empty response error';
        }
        if (!empty($translation)) {
            return $translation;
        }else{
            return null;
        }

    }

} 