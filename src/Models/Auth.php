<?php

namespace Src\Models;

use Src\Dao\UserDaoMysql;
use Src\Utils\Redirect;
use Src\Utils\Filter;

class Auth{

    public $pdo;
    public $base;
    public $userDao;

    public function __construct(\PDO $driver, $base){
        $this->pdo = $driver;
        $this->base = $base;
        $this->userDao = new UserDaoMysql($driver);
    }

    public function checkAuthentication($protectionStatus=false){

        if(isset($_SESSION['token'])){

            $token = Filter::general($_SESSION['token']);
            $userInfo = $this->userDao->findByToken($token);

            if($userInfo){
                return $userInfo;
            }else{
                $this->isProtectedPage($protectionStatus);
            }
        }else{
            $this->isProtectedPage($protectionStatus);
        }
    }

    public function isProtectedPage($status){
        if($status){
            Redirect::local($this->base, "login.php");
        }else{
            return false;
        }
    }

    public function buildPasswordHash($password){
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function buildToken(){
        return md5(time().rand(0,999));
    }
}