<?php

class Account {

    protected static $pdo;
    
    protected static $table;
   
    public static function setPdo($pdo) {
        self::$pdo = $pdo;
    }

    public static function setTable($table) {
        self::$table = $table;
    }
    
    public static function checkIfMailExists($mail): bool {
        $stmt = null;
        try {
            $stmt = self::$pdo->prepare("SELECT COUNT(*) FROM " . self::$table . " WHERE mail = :mail");
            $stmt->bindParam(':mail', $mail);

            if ($stmt->execute()) {
                $count = $stmt->fetchColumn();
                return $count > 0;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            return false;
        } finally {
            if ($stmt) {
                $stmt->closeCursor();
            }
        }
    }

    public static function register(object $data) {
        $passHash = password_hash($data->pword, PASSWORD_BCRYPT);

        $fields = [
            'name' => $data->name,
            'mail' => $data->mail,
            'phone' => $data->phone,
            'pword' => $passHash,
            'created_at' => date("Y-m-d H:i:s"),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'lat' => $data->lat,
            'lon' => $data->lon
        ];

        $columns = implode(', ', array_keys($fields));
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));

        $stmt = null;
        try {
            $stmt = self::$pdo->prepare("INSERT INTO " . self::$table . " ($columns) VALUES ($placeholders)");

            $i = 1;
            foreach ($fields as $value) {
                $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($i, $value, $type);
                $i++;
            }

            $stmt->execute();

            return [
                'name' => $data->name,
                'mail' => $data->mail
            ];
        } catch (PDOException $e) {
            http_response_code(500);
            return 'Internal Server Error: ' . $e->getMessage();
        } finally {
            if ($stmt) {
                $stmt->closeCursor();
            }
        }
    }

    public static function getData($Id) {
        $stmt = null;
        try {
            $sql = "SELECT * FROM " . self::$table . " WHERE id = :Id";
            $stmt = self::$pdo->prepare($sql);
            $stmt->bindParam(':Id', $Id);

            if ($stmt->execute()) {
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                return $data ? $data : null;
            } else {
                die("Query failed: " . $stmt->errorInfo()[2]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            return null;
        } finally {
            if ($stmt) {
                $stmt->closeCursor();
            }
        }
    }

    public static function login($data) {
        $mail = $data->mail;
        $pword = $data->pword;

        $stmt = null;
        try {
            $stmt = self::$pdo->prepare("SELECT pword FROM " . self::$table . " WHERE mail = :mail");
            $stmt->bindParam(':mail', $mail);

            if ($stmt->execute()) {
                $row = $stmt->fetch();

                if ($row && password_verify($pword, $row['pword'])) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            return 'Internal Server Error: ' . $e->getMessage();
        } finally {
            if ($stmt) {
                $stmt->closeCursor();
            }
        }
    }

    public static function resetPassword($newpword, $mail) {
        $stmt = null;
        try {
            $passHash = password_hash($newpword, PASSWORD_BCRYPT);
            $stmt = self::$pdo->prepare("UPDATE " . self::$table . " SET pword = :pword WHERE mail = :mail");
            $stmt->bindParam(':pword', $passHash, PDO::PARAM_STR);
            $stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            http_response_code(500);
            return false;
        } finally {
            if ($stmt) {
                $stmt->closeCursor();
            }
        }
    }

    
    public static function forgotPword(object $data)
    {
        
        $utility = new Utility();

        $checkIfMailExists = self::checkIfMailExists($data->mail);
        if (!$checkIfMailExists) {
            $utility->response(false, 'Email does not exist', null);
            return false;
        }
        $n=8;
        $token = $utility->randomPass($n);
        $passHash = password_hash($token, PASSWORD_BCRYPT);

        if (!self::resetPassword($passHash, $data->mail)) {
            $utility->response(false, $_SESSION['err'], null);
            return false;
        }

        $mailer = new Mailer();
        $userData = self::getData($data->userId);

        try {
            if ($mailer->sendPasswordToUser($userData['mail'], $userData['name'], $token)) {
                return true;
            }
        } catch (PDOException $e) {
            $_SESSION['err'] = $e->getMessage();
        } finally {
            unset($mailer);
        }
    }


}
?>
