<?php
    namespace model;
    require_once "functions.php";
    require_once "view.php";

    use view\representation;    
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
                    $mysql = new mysql('127.0.0.1','arts','root','', "SELECT * from articles;", "");
                    $mysql->ask();
                    $result = $mysql->getResult();
                    $show = new representation($result,"json");
                    exit();

                    // $mongo = new mongo($mongo_host,$mongo_port,$mongo_name,$mongo_collection,$mongo_user,$mongo_pass);
                    // //$mongo->change("mongo_host","localhost");
                    // $mongo->downloadData();
                    // if(!$mongo->getError()) {
                    //     $result = $mongo->getResult();
                    //     foreach($result as $article) {
                    //         unset($article->modified);
                    //     }
                    //     header("Content-type: application/json");
                    //     echo json_encode($result);
                    // }
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
                        $mysql = new mysql('127.0.0.1','arts','root','',"SELECT * from login WHERE user ='".$_POST['login']."' OR mail='".$_POST['login']."';",'');
                        $mysql->ask();
                        $result = $mysql->getResult();
                        header("Content-type: appliaction/json");
                        if(count($result)==0) {
                            echo json_encode(array(
                                "login"=>"Incorrect user"
                            ));
                        }
                        else {
                            
                            $salt = $result['salt'];
                            $direction = $result['direction'];
                            $pass = $result['pass'];
                            $direction==0 ? $newPass = $salt.$_POST['pass']: $newPass = $_POST['pass'].$salt;
                            if(password_verify($newPass, $pass)) {
                                echo json_encode(array(
                                    "login"=>"success"));
                            }
                            else {
                                echo json_encode(array(
                                    "login"=>"incorrect pass"));
                            }
                        }
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
        
        function downloadData() :bool {
            try {
                $mng = new \MongoDB\Driver\Manager("mongodb://$this->mongo_user:$this->mongo_pass@$this->mongo_host:$this->mongo_port/$this->mongo_user");
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
        protected string $mysqli_name;
        protected string $mysqli_user;
        protected string $mysqli_pass;
        protected string $mysqli_query;
        protected string $mysqli_errors;
        protected  $mysqli_result;
        function __construct(string $mysqli_host="", string $mysqli_name="",string $mysqli_user="root",string $mysqli_pass="", string $mysqli_query = "SELECT *;", string $mysqli_table="") {
            $this->mysqli_host = $mysqli_host;
            $this->mysqli_name = $mysqli_name;
            $this->mysqli_user = $mysqli_user;
            $this->mysqli_pass = $mysqli_pass;
            $this->mysqli_query = $mysqli_query;
        }

        function ask() {
            try {
                $mysqli = new \mysqli($this->mysqli_host,$this->mysqli_user,$this->mysqli_pass,$this->mysqli_name);
                $flag = false;
                $mysqli->connect_errno===true ? $flag = false : $flag = true;
                if($flag) {
                    if(!$result = $mysqli->query($this->mysqli_query)) {
                        echo $mysqli->error;
                        Throw new Exception($mysqli->error);
                    }
                    else 
                    {
                        $rows = $result->num_rows;
                        if($rows==1) {
                            $score = $result->fetch_assoc();
                            $this->mysqli_result = $score;
                        }
                        else if($rows==0) {
                            $this->mysqli_result = [];
                        }
                    }
                    
                }
                mysqli_free_result($result);
                mysqli_close($mysqli);
            }
            catch(Exception $e) {
                $this->mysqli_errors = $e;
            }
        }

        function isError() {
            if(empty($this->mysqli_errors)) {
                return false;
            }
            else {
                return true;
            }
        }

        function getError(){ 
            return $this->mysqli_errors;
        }

        function getResult() {
            return $this->mysqli_result;
        }
    }




?>