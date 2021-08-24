<?php

namespace App\Http\Controllers;

use Symfony\Component\Console\Input\Input;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class HomeController extends BaseController
{
    /*Get Array for create model */
    public function modelArray() {
        $modelArray = array(
            'scope' => array(
                'indirect-emissions–owned',
                'electricity',
                ),
            'name' => 'meeting-rooms',
        );
        return $modelArray;
    }
    
    /** Remove Specialchar Form String */
    public function RemoveSpecialChar($str1,$str2) 
    {
        $stringArray = array($str1,$str2);
        $folderArray =preg_replace('/[\s\W]+/',' ',$stringArray);
        return $folderArray;
    }

     /*Create File*/
    public function getCreatedFile() 
    {
        $path = app_path() . "/Models";
        $fileDir = $this->getFilePath();
        $modelFileName = $this->modelArray();
        $fileNameSpace = $modelFileName['name'];
        $fileNameCapital = ucwords(preg_replace('/[\s\W]+/',' ',$fileNameSpace));
        $fileName = str_replace(' ','',$fileNameCapital);
        $createdfile = fopen($fileDir."/".$fileName.'.php',"w");
        fclose($createdfile);
        return $fileName;
    }

    /*Create Dirctory and file */
    public function getFilePath()
	{
        $path = app_path() . "/Models";
        $modelFileName = $this->modelArray();
        $folderNameFirst = $modelFileName['scope'][0];
        $folderNameSecond = $modelFileName['scope'][1];
        $folderArray = $this->RemoveSpecialChar($folderNameFirst,$folderNameSecond);
        $filePath = '';
        for ($i = 0; $i < count($folderArray); $i++) {
           $FilepathCapital = ucwords(str_replace('',' ',$folderArray[$i]));
           $filePath .= "/";
           $filePath .= str_replace(' ','',$FilepathCapital);
        }
        /*Create Directory*/
        $fileDir = $path.$filePath;
        if(!is_dir($fileDir)) {  
            mkdir($fileDir, 0777, true);
        }
       return $fileDir;
	}

    /*generates Template  */
    public function parseTemplate()
    {
        $modelFileName = $this->modelArray();
        $className = $this->getCreatedFile();
        $tableName = $modelFileName['name'];

        $uplodedfileName = $this->getFilePath().'/'. $className.'.php';
        /*Get path from full string path */
        $namespaceCaps = ucfirst(strstr(dirname($uplodedfileName), 'app'));
        $namespace = str_replace('/',"\\",$namespaceCaps);
        /* Model file Content  */
        $startFile="<?php \n ";
        $content= "namespace $namespace;
            use Illuminate\Database\Eloquent\Model;
            
            class $className extends Model
            {
                const TABLE_NAME = '$tableName';
                public function getTableName(): string
                {
                    return self::TABLE_NAME;
                }
            }";
        $fileContent = $startFile.$content."?>";
        /*Generates Model */
        file_put_contents( $uplodedfileName , $fileContent );
    }
}

