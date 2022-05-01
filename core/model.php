<?php
    namespace model;
    require_once "functions.php";
    
    use function functions\getip;

    class configuration {
        protected $main_page;
        protected $host;
    }

    class api {
        protected $url;
        function __construct(array $url) {
            require_once "mongodb.php";
            require_once "mysql.php";
            switch($url[1]) {
                case "":
                    header("Location: ./");
                    exit();
                break;
                case "loadArticles":
                    $mongo = new mongo($mongo_host,$mongo_port,$mongo_name,$mongo_collection,$mongo_user,$mongo_pass);
                    //$mongo->change("mongo_host","localhost");
                    $mongo->downloadData();
                    if(!$mongo->getError()) {
                        $result = $mongo->getResult();
                        foreach($result as $article) {
                            unset($article->modified);
                        }
                        header("Content-type: application/json");
                        echo json_encode($result);
                    }
                    exit();
                break;
                case "prelogin":
                    $ip = getip();
                    $_SESSION['ip'] = $ip;
                    echo true;
                    exit();
                break;
                case "login":
                    if(isset($_POST['login']) && isset($_POST['pass'])) {

                    }
                    else {
                        return 403;
                    }
                    exit();
                break;
            }
        }
    }


    class mongo {
        protected string $mongo_host;
        protected string $mongo_port;
        protected string $mongo_name;
        protected string $mongo_collection;
        protected string $mongo_user;
        protected string $mongo_pass;
        protected array $mongo_filters;
        protected array $mongo_options;
        protected array $mongo_result;
        protected $mongo_error = false;
        function __construct(string $mongo_host, string $mongo_port,string $mongo_name,string $mongo_collection,string $mongo_user,string $mongo_pass,array $filters = [],array $options = []) {
            $this->mongo_host = $mongo_host;
            $this->mongo_port = $mongo_port;
            $this->mongo_name = $mongo_name;
            $this->mongo_collection = $mongo_collection;
            $this->mongo_user = $mongo_user;
            $this->mongo_pass = $mongo_pass;
            $this->mongo_options = $options;
            $this->mongo_filters = $filters;
        }
        function change(string $whatChange, $thing) {
            $flag = false;
            switch($whatChange) {
                case "mongo_host":
                case "mongo_port":
                case "mongo_name":
                case "mongo_collection":
                case "mongo_user":
                case "mongo_pass":
                    if(is_string($thing)) {
                        $flag = true;
                    }
                break;
                case "filters":
                case "options":
                    if(is_array($thing)) {
                        $flag = true;
                    }
                break;
            }
            if($flag) {
                $this->$whatChange = $thing;
            }
        }
        
        function downloadData() :bool{
            try {
                $mng = new \MongoDB\Driver\Manager("mongodb://$this->mongo_host:$this->mongo_port");
                $query = new \MongoDB\Driver\Query($this->mongo_filters,$this->mongo_options); 
                $rows = $mng->executeQuery("$this->mongo_name.$this->mongo_collection", $query);
                $data = [];
                foreach($rows as $row) {
                   array_push($data,$row);
                }
                $this->result = $data;
                return true;
            } catch (MongoDB\Driver\Exception\Exception $e) {       
                echo json_encode($e);
                $this->error = $e;
                return false;
            }
        }
        function getError() {
            return $this->mongo_error;
        }
        function getResult() :array {
            return $this->result;
        }

    }

    class mysql {
        protected string $mysqli_host;
        protected string $mysqli_port;
        protected string $mysqli_name;
        protected string $mysqli_socket;
        protected string $mysqli_user;
        protected string $mysqli_pass;
        protected string $mysqli_query;
        protected string $mysqli_table;
        protected string $mysqli_errors;
        protected string $mysqli_result;
        function __construct(string $mysqli_host="", string $mysqli_port="",string $mysqli_name="",string $mysqli_socket="",string $mysqli_user="admin",string $mysqli_pass="", string $mysqli_query = "SELECT *;", string $mysqli_table="") {
            try {
                $mysqli_connect = new mysqli_connect($mysqli_host,$mysqli_user,$mysqli_pass,$mysqli_name,$mysqli_port,$mysqli_socket);
                $flag = false;
                mysqli_connect_errno()===true ? throw new Exception(mysqli_connect_error()) : $flag = true;
                if($flag) {
                    $result = $mysqli->query("SELECT * FROM City", MYSQLI_USE_RESULT);
                }
                mysqli_close($mysqli_connect);
            }
            catch(Exception $e) {
                $this->error = $e;
            }
        }
    }




?>