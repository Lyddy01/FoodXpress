<?php

class User extends Account {

    protected static $table = 'tblusers'; // Assuming your user table name is 'users'
   
    protected static $pdo;

    private static function setup()
    {
        if (!self::$pdo) {
            $db = new Db();
            self::$pdo = $db->connect();
        }

        parent::setPdo(self::$pdo);
        parent::setTable(self::$table);
    }

    public static function checkIfUserMailExists($mail) {
        self::setup();
        return parent::checkIfMailExists($mail);
    }

    public static function registerUser(object $data) {
        self::setup();
        
        return parent::register($data);
    }

    public static function loginUser($data) {
        self::setup();
        return parent::login($data);
    }

    public static function getUserData($userId) {
        self::setup();
        return parent::getData($userId);
    }

    public static function resetUserPassword($newpword, $mail) {
        self::setup();
        return parent::resetPassword($newpword, $mail);
    }

    public static function userForgotPword($data)
    {
        self::setup();
        return parent::forgotPword($data);
    }

    
}
?>
