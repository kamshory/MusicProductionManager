<?php

namespace MusicProductionManager\File;
use MusicProductionManager\Exceptions\NoUploadedFileException;

class FileUpload
{
    /**
     * Maximum file length
     *
     * @var integer
     */
    private $maxLength = 25000000;


    /**
     * File name
     *
     * @var string
     */
    private $fileName = "";

    /**
     * File path
     *
     * @var string
     */
    private $filePath = "";

    /**
     * File size
     *
     * @var integer
     */
    private $fileSize = 0;

    /**
     * File type
     *
     * @var string
     */
    private $fileType = "";

    /**
     * File extension
     *
     * @var string
     */
    private $fileExtension = "";

    public function __construct()
    {
        // just constructor
    }
    
    public function uploadTemporaryFile($files, $name, $targetDir, $targetName, $random)
    {
        if(!isset($files) || !isset($files[$name]) || !isset($files[$name]['name']))
        {
            throw new NoUploadedFileException("No file uploaded");
        }
        else
        {
            $errors = array();
            $file_name = $files[$name]['name'];
            $file_size = $files[$name]['size'];
            $file_tmp = $files[$name]['tmp_name'];
            $file_type = $files[$name]['type'];

            $arr = explode('.', $files[$name]['name']);
            $file_ext = strtolower(end($arr));
            $path = rtrim($targetDir, "/") . "/" . $targetName."_".$random. ".".$file_ext;

            if (empty($errors)) {
                move_uploaded_file($file_tmp, $path);
            }

            $this->fileName = $file_name;
            $this->filePath = $path;
            $this->fileSize = $file_size;
            $this->fileType = $file_type;
            $this->fileExtension = $file_ext;
        }
    }

    public function upload($files, $name, $targetDir, $targetName)
    {
        $errors = array();
        $file_name = $files[$name]['name'];
        $file_size = $files[$name]['size'];
        $file_tmp = $files[$name]['tmp_name'];
        $file_type = $files[$name]['type'];

        $arr = explode('.', $files[$name]['name']);
        $file_ext = strtolower(end($arr));
        $path = rtrim($targetDir, "/") . "/" . $targetName. ".".$file_ext;

        $extensions = array("mp3");

        if (in_array($file_ext, $extensions) === false) {
            $errors[] = "extension not allowed, please choose a JPEG or PNG file.";
        }

        if ($file_size > $this->maxLength) {
            $errors[] = 'File size must be excately 2 MB';
        }

        if (empty($errors)) {
            move_uploaded_file($file_tmp, $path);
        }

        $this->fileName = $file_name;
        $this->filePath = $path;
        $this->fileSize = $file_size;
        $this->fileType = $file_type;
        $this->fileExtension = $file_ext;
    }

    /**
     * Get maximum file length
     *
     * @return  integer
     */ 
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * Set maximum file length
     *
     * @param  integer  $maxLength  Maximum file length
     *
     * @return  self
     */ 
    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;

        return $this;
    }

    /**
     * Get file name
     *
     * @return  string
     */ 
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Get file path
     *
     * @return  string
     */ 
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Get file size
     *
     * @return  integer
     */ 
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * Get file type
     *
     * @return  string
     */ 
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * Get file extension
     *
     * @return  string
     */ 
    public function getFileExtension()
    {
        return $this->fileExtension;
    }
}
