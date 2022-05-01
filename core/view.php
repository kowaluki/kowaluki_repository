<?php
    namespace view;

    require_once "functions.php";

    use function functions\fileName;
    use function functions\fileExt;

    class file {
        private $fileName;
        private $fileExt;
        private $file;
        private $path;
        private $header;
        function __construct(string $file) {
            $this->fileExt = fileExt($file);
            $this->fileName = fileName($file);
            $this->file = $file;
            switch($this->fileExt) {
                case "html":
                    $this->path = "html";
                    $this->header = "text/html";
                break;
                case "html2":
                    $this->path = "html/elements";
                    $this->header = "text/html";
                    $file = $this->file;
                    $file = substr_replace($file ,"", -1);
                    $this->file = $file;
                    break;
                case "css":
                    $this->path = "css";
                    $this->header = "text/css";
                break;
                case "js":
                    $this->path = "js";
                    $this->header = "application/javascript";
                break;
                case "xml":
                    $this->path = "xml";
                    $this->header = "text/xml";
                break;
                case "jpg":
                    $this->path = "images";
                    $this->header = "image/jpg";
                break;
                case "png":
                    $this->path = "images";
                    $this->header = "image/png";
                break;
                case "mpeg":
                    $this->path = "sounds";
                    $this->header = "audio/mpeg";
                break;
            }
        }

        function readFile() {
            header("Content-type: ".$this->header);
            include $this->path."/".$this->file;
            exit();
        }
        function config() {
            $array = array(
                "file name"=>$this->fileName,
                "file extension"=>$this->fileExt,
                "file"=>$this->file,
                "path"=>$this->path,
                "header"=>$this->header
            );
            header("Content-type: application/json");
            echo json_encode($array);
        }

        function addFile() {

        }
    }

    class error {
        private $file;
        function __construct(int $file) {
            $errors = [403,404,500];
            for($int = 0; $int<count($errors); $int++) {
                if($file===$errors[$int]) {
                    header("Content-type: text/html");
                    include "errors/".$file.".html";
                }
            }
            exit();
        }
    }
