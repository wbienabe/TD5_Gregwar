<?php

namespace Sondages;

use Doctrine\DBAL\Connection;

final class UserManager {

    private $db;
    private $app;

    public function __construct(Connection $db, $app) {
        
        $this->db = $db;
        $this->app = $app;
    }

    public function insertNewUser($login, $password, $role) {
        echo "<script>alert('insert new user')</script>";
        $exists = $this->checkIfUserExistByLogin($login);
        if (!$exists) {
            $this->db->insert('users', array(
                'login' => $login,
                'password' => md5($password),
                'role' => $role
            ));
            return true;
        } else {
            return false;
        }
    }

    public function getAllUsers() {
        $request = $this->db->query("SELECT * FROM users");
        return $request->fetchAll();
    }

    public function checkIfUserExistByLogin($login) {
        $sql = "SELECT * FROM users WHERE users.login = ?";
        $request = $this->db->prepare($sql);
        $request->bindValue(1, $login);
        $request->execute();
        $result = $request->fetch();
        if ($result != false) {
            return true;
        } else {
            return false;
        }
    }

}
