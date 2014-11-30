<?php

namespace studyqa;

use studyqa\model\Region;
use SplClassLoader;
use studyqa\model\VkImport;

error_reporting(E_ALL);
ini_set("display_errors","On");


include 'SplClassLoader.php';
$loader = new SplClassLoader('studyqa', '../');
$loader->register();


class Main{
    var $regionModel;
    var $vkImportModel;
    var $countryId=1;

    function __construct(){
        $this->regionModel=new Region();
        $this->vkImportModel=new VkImport();
    }



    /**
     * import all regions with check existing records in table `region`
     */
    public function importRegionsAll($countryId,$offset,$queryCount,$limit){
        //limit count of import rows
        $affected_rows=0;
        if($countryId==0){
            echo 'Error: country id couldn`t be 0';
            exit();
        }
        while($offset<=$limit){
            $resultImportRegions=$this->vkImportModel->getRegions($countryId,$offset,$queryCount);

            $importedRegions=$resultImportRegions['response']['items'];
            foreach($importedRegions as $i){
                $existRegion=$this->regionModel->getById($i['id']);
//                echo json_encode($existRegion).'  $i'.json_encode($i['id']);
                if(!($i['id']==$existRegion['region_id'])){
                    $this->regionModel->setOne($i['id'],$i['title'],$this->countryId);
                    $affected_rows++;
                }
            }
            $offset+=$offset;
        }
        echo 'Script finished successfully. Imported rows: '.$affected_rows;
    }



}
$offsetRegion=0;
$countRegion=5;

$test=new Main();
$test->importRegionsAll(1,0,5,20);




