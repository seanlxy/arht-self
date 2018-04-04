<?php
session_start(); 
require_once ('../../utility/config.php');                              ## System config file
if(!$c_Connection->Connect()) {
    echo "Database connection failed";
    exit;
}
$Message = "";
$c_Message	= $c_Connection->GetMessage();

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
    /**
    * Save the file to the specified path
    * @return boolean TRUE on success
    */

    function save($path)
    {    
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $this->getSize())
        {            
            return false;
        }

        $target = fopen($path, "w");        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        return true;
    }
    function getName()
    {
        return $_GET['qqfile'];
    }

    function getSize()
    {
        if (isset($_SERVER["CONTENT_LENGTH"]))
        {
            return (int)$_SERVER["CONTENT_LENGTH"];            
        }
        else
        {
            throw new Exception('Getting content length is not supported.');
        }      
    }   
} // end of class qqUploadedFileXhr

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm{  
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path)
    {
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path))
        {
            return false;
        }
        return true;
    }
    function getName()
    {
        return $_FILES['qqfile']['name'];
    }
    function getSize()
    {
        return $_FILES['qqfile']['size'];
    }

} // end of class qqUploadedFileForm

class qqFileUploader {
    private $allowedExtensions  = array();
    private $sizeLimit          = 10485760;
    private $file;
    public  $parent_dir;
    public  $sub_dir;
    public  $versions_to_create = array();

    function __construct(){ } // do something in constructor if required

    public function do_upload(array $allowedExtensions = array(), $sizeLimit = 10485760)
    {
        $allowedExtensions       = array_map("strtolower", $allowedExtensions);
        
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit         = $sizeLimit;

        $this->checkServerSettings();       

        if (isset($_GET['qqfile']))
        {
            $this->file = new qqUploadedFileXhr();
        }
        elseif (isset($_FILES['qqfile']))
        {
            $this->file = new qqUploadedFileForm();
        }
        else
        {
            $this->file = false; 
        }
    }
    
    private function checkServerSettings()
    {        
        $postSize   = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        
        
        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit)
        {
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
        }        
    }
    
    private function toBytes($str)
    {
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last)
        {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;        
        }
        return $val;
    }
    
    /**
    * Returns array('success'=>true) or array('error'=>'error message')
    */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE, $htmlroot)
    {
        // $parent_dir = $this->get_parent_dir();
        $sub_dir    = $this->get_sub_dir();

    if (!is_writable($uploadDirectory))
    {
        return array('error' => "Server error. Upload directory isn't writable.");
    }

    if (!$this->file)
    {
        return array('error' => 'No files were uploaded.');
    }

    $size = $this->file->getSize();

    if ($size == 0)
    {
        return array('error' => 'File is empty');
    }

    if($size > $this->sizeLimit)
    {
     return array('error' => 'File is too large');
    }

    $pathinfo = pathinfo($this->file->getName());
    $filename = $pathinfo['filename'];
    $ext      = $pathinfo['extension'];

    if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions))
    {
        $these = implode(', ', $this->allowedExtensions);
        return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
    }

    $photo_directory    = "$uploadDirectory{$sub_dir}";
    $photo_versions     = $this->get_versions_to_create();
    $main_dir_to_upload = '';
    $renamed_file       = '';
    $is_saved           = FALSE;
    if(count($photo_versions) > 0)
    {
        require_once("../../classes/class_imageresizer.php");

        foreach ($photo_versions as $photo_version)
        {
        
            $dir_name = $photo_directory.$photo_version['folder_name'];

            if(!file_exists($dir_name))
            {
                mkdir($dir_name,0777,true);
            }

            if(array_key_exists('is_main', $photo_version) && $photo_version['is_main'])
            {
                $main_dir_to_upload = $dir_name;
            }


            if(!$replaceOldFile)
            {

                /// don't overwrite previous files that were uploaded
                while(file_exists("$uploadDirectory{$sub_dir}{$dir_name}/$filename.$ext"))
                {
                    $filename .= rand(10, 99);
                }

            }


            $ext = strtolower($ext);
            if($this->file->save("$main_dir_to_upload/$filename.$ext"))
            {   
                $is_saved = TRUE;

                if(file_exists("$main_dir_to_upload/$filename.$ext"))
                {
                    $renamed_file = prepare_item_url($filename).".$ext";
                    rename("$main_dir_to_upload/$filename.$ext", "$main_dir_to_upload/".$renamed_file);
                }

                if($dir_name != $main_dir_to_upload)
                {
                    $image = new images();
                    // $image->setMaxHeight($photo_version['height']);
                    // $image->setMaxWidth($photo_version['width']);
                    // $image->setQuality($photo_version['quality']);
                    // $image->path = "$main_dir_to_upload/$renamed_file";            
                    // $image->setSaveDirectory("$dir_name");
                    // $image->resizeToFill();
                    $image->resizer("$dir_name", "$main_dir_to_upload/$renamed_file",$photo_version['width'], $photo_version['height']);
                }

               
            }
        }

    }

    if($is_saved)
    {
        
        if(file_exists($main_dir_to_upload)) delete_files($main_dir_to_upload, TRUE, 1);
        $pid = (int) str_replace('p', '', $this->get_sub_dir());
        $rank = fetch_value("SELECT MAX(rank) FROM property_photo WHERE property_id = '$pid'") + 1;
        $insert_arr['path']        =  $renamed_file;
        $insert_arr['is_special']  =  0;
        $insert_arr['rank']        =  $rank;
        $insert_arr['property_id'] =  $pid;

        $new_id = insert_row($insert_arr,'property_photo');

        return array(
            'success'     => true,
            'path'        => $insert_arr['path'],
            'property_id' => $insert_arr['property_id'],
            'photo_path'  => $uploadDirectory,
            'index'       => SHA1($new_id)
        );

    }
    else
    {
        return array('error'=> 'Could not save uploaded file.' .
        'The upload was cancelled, or server error encountered');
    }


    }// end of handleuplaod function

    //getters

    public function get_parent_dir()
    {
        return $this->parent_dir;
    } 

    public function get_sub_dir()
    {
        return $this->sub_dir;   
    }  
    public function get_versions_to_create()
    {
        return $this->versions_to_create;   
    }

    // setters
    public function set_parent_dir($val)
    {
        $this->parent_dir = $val;

    }  
    public function set_sub_dir($val)
    {
        $this->sub_dir = $val;   
    } 
    public function set_versions_to_create($array)
    {
        $this->versions_to_create = $array;   
    }
}

// list of valid extensions, ex. array("jpeg", "xml", "bmp")
$allowedExtensions = array();
// max file size in bytes
$sizeLimit = 10 * 1024 * 1024;
$versions = array(
    array('folder_name' => 'orignal', 'is_main' => TRUE),
    array('folder_name' => 'large',  'width'=> 769, 'height'=> 541, 'quality' => 80),
    // array('folder_name' => 'large',  'width'=> 923, 'height'=> 545, 'quality' => 80),
    array('folder_name' => 'medium', 'width'=> 640, 'height'=> 480, 'quality' => 80),
    array('folder_name' => 'small',  'width'=> 370, 'height'=> 260, 'quality' => 80),
    array('folder_name' => 'thumb',  'width'=> 190,  'height'=> 134, 'quality' => 80),
);
$uploader = new qqFileUploader();

$uploader->set_parent_dir('property_photos/');
$uploader->set_sub_dir("p".mysql_real_escape_string($_GET['property_id'])."/");
$uploader->set_versions_to_create($versions);
$uploader->do_upload($allowedExtensions, $sizeLimit);
$result = $uploader->handleUpload("$root/{$uploader->get_parent_dir()}",false,$htmlroot);

// to pass data through iframe you will need to encode all html tags

$full_result =  htmlspecialchars(json_encode($result), ENT_NOQUOTES);
die($full_result);