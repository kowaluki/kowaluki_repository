<?php
    namespace controller;

    require_once "view.php";
    require_once "model.php";

    use model\configuration;
    use model\api;
    use view\file;
    use view\error;

    class ruter extends configuration {
        private $url;
        private array $rules = [];
        function __construct(string $mainPage = "index.html", string $url = "", string $host="") {
            $this->main_page = $mainPage;
            $uri = strlen($url)===0 ? explode("/",$_SERVER['REQUEST_URI']) : explode("/",$url);
            $i = 0;
            while($i<3) {
                array_shift($uri); 
                $i++;
            }
            $this->url = $uri;
            $this->host = strlen($host)===0 ? $_SERVER['SERVER_NAME']: $host;
        }
        function addRule(string $url, string $file) {
            $rules = $this->rules;
            array_push($rules,array(
                "url"=>$url,
                "file"=>$file
            ));
            $this->rules = $rules;
        }
        function showRules() {
            return $this->rules;
        }
        function run() {         
            switch($this->url[0]) {
                case "":
                    $file = new file($this->main_page);
                    $file->readFile();
                    exit();
                break;
                case "website":
                    $file = new file($this->url[1]);
                    $file->readFile();
                    exit();
                break;
                case "elements":
                    $file = new file($this->url[1].".html2");
                    $file->readFile();
                    exit();
                break;
                case "api":
                    $api = new api($this->url);
                    exit();
                break;
                default:
                    //Rules
                    $fileExist = false;
                    foreach($this->rules as $rule) {
                        switch($this->url[0]) {
                            case $rule['url']:
                                $fileExist = true;
                                $file = new file($rule['file']);
                                $file->readFile();
                                exit();
                        }
                    }
                    if(!$fileExist) {
                        $file = new error(404);
                    }
                    //STOP Rules
                    //APIs

            }
        }
    }



?>