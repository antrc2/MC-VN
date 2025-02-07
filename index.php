<?php
    require_once 'vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    use App\Controllers\AccountController;
    $acc = new AccountController();


    $parsedUrl = parse_url($_SERVER['REQUEST_URI']);
    $uri = isset($parsedUrl['path']) ? trim($parsedUrl['path'], "/") : "/";
    $uri = $uri === "" ? "/" : $uri; 

    if ($uri == "/"){
        $acc->listAccount();
    }


?>