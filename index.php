<?php
    // echo phpinfo();
    require_once "core/controller.php";

    use controller\ruter;

    $ruter = new ruter;
    $ruter->addRule("app","app.html");
    $ruter->run();
    exit();
    
?>