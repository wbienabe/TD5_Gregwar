<?php

namespace Sondages;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Sondages\User;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\DBAL\Connection;


class UserProvider implements UserProviderInterface {

    private $conn;

    public function __construct(Connection $conn) {
       // echo "<script>alert('userProviderConstruct')</script>";
        $this->conn = $conn;
    }

    public function loadUserByUsername($login) {
        $stmt = $this->conn->executeQuery('SELECT * FROM users WHERE login = ?', array(strtolower($login)));
        if (!$user = $stmt->fetch()) {
            throw new UsernameNotFoundException(sprintf('login "%s" does not exist.', $login));
        }

        return new User($user['login'], $user['password'], explode(',', $user['roles']));
    }

    public function refreshUser(UserInterface $user) {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getLogin());
    }

    public function supportsClass($class) {
        //return $class === 'Symfony\Component\Security\Core\User\User';
        return $class === 'User';    
    }

}
