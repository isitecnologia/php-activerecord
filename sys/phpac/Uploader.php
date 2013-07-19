<?php

class Uploader
{
    private $destinationPath;
    private $errorMessage;
    private $extensions;
    private $allowAll;
    private $maxSize;
    private $uploadName;
    public $name='Uploader';
    public $useTable    =false;

    function setDir($path){
        $this->destinationPath  =   $path;
        $this->allowAll =   false;
    }

    function allowAllFormats(){
        $this->allowAll =   true;
    }

    function setMaxSize($sizeMB){
        $this->maxSize  =   $sizeMB * (1024*1024);
    }

    function setExtensions($options){
        $this->extensions   =   $options;
    }

    function setSameFileName(){
        $this->sameFileName =   true;
        $this->sameName =   true;
    }
    function getExtension($string){
        $ext    =   "";
        try{
                $parts  =   explode(".",$string);
                $ext        =   strtolower($parts[count($parts)-1]);
        }catch(Exception $c){
                $ext    =   "";
        }
        return $ext;
    }

    function setMessage($message){
        $this->errorMessage =   $message;
    }

    function getMessage(){
        return $this->errorMessage;
    }

    function getUploadName(){
        return $this->uploadName;
    }
    function setSequence($seq){
        $this->imageSeq =   $seq;
    }
    
    function setName($name){
        $this->name = $name;
    }

    function getRandom(){
        return strtotime(date('Y-m-d H:i:s')).rand(1111,9999).rand(11,99).rand(111,999);
    }
    function sameName($true){
        $this->sameName =   $true;
    }
    function uploadFile($fileBrowse){
        $result =   false;
        $size   =   $_FILES[$fileBrowse]["size"];
        $name   =   $_FILES[$fileBrowse]["name"];
        $ext    =   $this->getExtension($name);
        if(!is_dir($this->destinationPath)){
            $this->setMessage("Destination folder is not a directory ");
        }else if(!is_writable($this->destinationPath)){
            $this->setMessage("Destination is not writable !");
        }else if(!$name){
            $this->setMessage("File not selected ");
        }else if($size>$this->maxSize){
            $this->setMessage("Too large file !");
        }else if($this->allowAll || (!$this->allowAll && in_array($ext,$this->extensions))){

            if($this->sameName==false && $this->name){
                $this->uploadName   =  $this->name.".".$ext;
            }else{
                $this->uploadName = removeAcentos(substr($name,0,-(strlen($ext)+1)),"_").".$ext";
            }
            
            //checa se já não existe um arquivo com o nome, se já, gera outro e tenta novamente
            $i = 1;
            $name = $this->uploadName;
            while (file_exists($this->destinationPath.$this->uploadName) && $i < PHP_INT_SIZE){
                $this->uploadName = substr($name,0,-(strlen($ext)+1))."_$i.$ext";
                $i++;
            }
            
            if(move_uploaded_file($_FILES[$fileBrowse]["tmp_name"],$this->destinationPath.$this->uploadName)){
                $result =   true;
            }else{
                $this->setMessage("Upload failed , try later !");
            }
            
        }else{
            $this->setMessage("Invalid file format !");
        }
        return $result;
    }

    function deleteUploaded(){
        unlink($this->destinationPath.$this->uploadName);
    }

    
    
    
    function resize($max_width = 0, $max_height = 0 ){
        
    if(eregi("\.png$",$this->uploadName)) 
    { 
     $img = imagecreatefrompng($this->uploadName); 
    } 
     
    if(eregi("\.(jpg|jpeg)$",$this->uploadName)) 
    { 
     $img = imagecreatefromjpeg($this->uploadName); 
    } 
     
    if(eregi("\.gif$",$this->uploadName)) 
    { 
     $img = imagecreatefromgif($this->uploadName); 
    } 

        $FullImage_width = imagesx ($img);     
        $FullImage_height = imagesy ($img);    

        if(isset($max_width) && isset($max_height) && $max_width != 0 && $max_height != 0) 
        { 
         $new_width = $max_width; 
         $new_height = $max_height; 
        } 
        else if(isset($max_width) && $max_width != 0) 
        { 
         $new_width = $max_width; 
         $new_height = ((int)($new_width * $FullImage_height) / $FullImage_width); 
        } 
        else if(isset($max_height) && $max_height != 0) 
        { 
         $new_height = $max_height; 
         $new_width = ((int)($new_height * $FullImage_width) / $FullImage_height); 
        }         
        else 
        { 
         $new_height = $FullImage_height; 
         $new_width = $FullImage_width; 
        }     

        $full_id =  imagecreatetruecolor( $new_width , $new_height ); 
        // Check transparent gif and pngs 
    if(eregi("\.png$",$this->uploadName) || eregi("\.gif$",$this->uploadName)) 
        { 
            $trnprt_indx = imagecolortransparent($img); 
            $trnprt_color = imagecolorsforindex($img, $trnprt_indx); 
            $trnprt_indx = imagecolorallocate($full_id, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']); 
            imagefill($full_id, 0, 0, $trnprt_indx); 
            imagecolortransparent($full_id, $trnprt_indx); 
        } 
        imagecopyresampled( $full_id, $img, 0,0,0,0, $new_width, $new_height, $FullImage_width, $FullImage_height ); 
         

        if(eregi("\.(jpg|jpeg)$",$this->uploadName)) 
        { 
         $full = imagejpeg( $full_id, $this->uploadName,100); 
        } 
         
        if(eregi("\.png$",$this->uploadName)) 
        { 
         $full = imagepng( $full_id, $this->uploadName); 
        } 
         
        if(eregi("\.gif$",$this->uploadName)) 
        { 
         $full = imagegif($full_id, $this->uploadName); 
        } 
        imagedestroy( $full_id ); 
        unset($max_width); 
        unset($max_height); 
    } 
    
    
}

?>