<?php

class Utility{

    public function validateSignUp($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }

    public function validateNumbers($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        $data = filter_var($data, FILTER_VALIDATE_INT);
        return $data;
    }

    public function validateEmail($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        $data = filter_var($data, FILTER_SANITIZE_EMAIL);
        $data = filter_var($data, FILTER_VALIDATE_EMAIL);
        return $data;
        
    }

    public function response($x, $y, $v) {
        $arr = array(
            'success' => $x,
            'message' => $y,
            'data' => $v
        );
        $array = json_encode($arr);
        echo $array;
        exit();
    }

    public function randomPass($n) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    
    
}
?>
