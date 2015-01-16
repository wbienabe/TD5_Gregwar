<?php

namespace Sondages;

class Model {

    protected $pdo;

    public function __construct($host, $database, $user, $password) {
        try {
            $this->pdo = new \PDO('mysql:dbname=' . $database . ';host=' . $host, $user, $password);
            $this->pdo->exec('SET CHARSET UTF8');
        }
        catch (\PDOException $exception) {
            die('Impossible de se connecter au serveur MySQL : ' . $exception->getMessage());
        }
    }

    public function checkConnection($id) {
        $sql = 'SELECT * FROM users WHERE id = ?';
        $query = $this->pdo->prepare($sql);
        $query->execute(array($id));
        return $this->fetchOne($query);
    }

    public function checkRegister($id) {
        $sql = 'SELECT COUNT(*) as nb FROM users WHERE login = ?';
        $query = $this->pdo->prepare($sql);
        $query->execute(array(htmlspecialchars($_POST['login'])));
        return $query->fetch();
    }

    public function getPolls() {
        //$sql = 'SELECT question, answer1, answer2, 3, COUNT(*) as nbPolls FROM polls GROUP BY question, answer1, answer2, answer3';
        $sql = 'SELECT * FROM polls';
        return $this->pdo->query($sql);
    }

    public function getPollsFromId($id) {
        /* $sql = 'SELECT * FROM polls WHERE id=?';
          $query = $this->pdo->prepare($sql);
          $query->execute(array($id));
          return $query->fetch(); */
        // moche mais je truc de dessus marche pas.. pas compris
        $sql = 'SELECT * FROM polls WHERE id = ' . $id;
        return $this->pdo->query($sql);
    }

    public function getIdFromPoll($poll) {
        $sql = 'SELECT id FROM polls WHERE question = ?';
        $query = $this->pdo->prepare($sql);
        $query->execute(array($poll));
        //var_dump($query->fetch());
        return $query->fetch();
    }

    public function getIdFromUser($user) {
        $sql = 'SELECT * FROM users WHERE login = ?';
        $query = $this->pdo->prepare($sql);
        $query->execute(array($user));
        return $query->fetch()["id"];
    }

    public function didIAnswer($user, $pollId) {
        $id = $this->getIdFromUser($user);

        $sql = 'SELECT * FROM answers WHERE poll_id = ? AND user_id = ?';
        $query = $this->pdo->prepare($sql);
        $query->execute(array($pollId, $id));
        if ($query->rowCount()) {
            return true;
        }
        return false;
    }

    public function login($login, $password, $app) {
        $sql = 'SELECT * FROM users WHERE login = ?';
        $query = $this->pdo->prepare($sql);
        $query->execute(array(htmlspecialchars($login)));
        if ($query->rowCount()) {
            $user = $query->fetch();
            if ($user['password'] == md5($password)) {
                $app['session']->set('user', $user['login']);
            }
        }
    }

    public function register($login, $password) {
        $sql = 'SELECT * FROM users WHERE login = ? and password = ?';
        $query = $this->pdo->prepare($sql);
        $query->execute(array(htmlspecialchars($login), md5(htmlspecialchars($password))));
        if ($query->rowCount()) {
            return false;
        }
        else {
            $sql = 'INSERT INTO users (login, password) VALUES (?, ?)';
            $query = $this->pdo->prepare($sql);
            $query->execute(array(htmlspecialchars($login), md5(htmlspecialchars($password))));
            return true;
        }
    }

    protected function fetchOne(\PDOStatement $query) {
        if ($query->rowCount() != 1) {
            return false;
        }
        else {
            return $query->fetch();
        }
    }

    /* function getTotal($pollId) {
      $answers = array();
      foreach (array(1, 2, 3) as $answer) {
      $sql = 'SELECT COUNT(*) as nb FROM answers WHERE poll_id = ? AND answer = ?';
      $query = $this->pdo->prepare($sql);
      $query->execute(array($pollId, $answer));

      $result = $query->fetch();
      $answers[$answer] = $result['nb'];
      }
      $total = array_sum($answers);
      return array('total'=>$total, 'answers'=>$answers);
      } */

    function getTotalAnswers($total) {
       // var_dump(count($total));
        return count($total);
    }

    function getCountOfAnswer($id, $cpt) {
        $sql = 'SELECT COUNT(answer) as nb FROM answers WHERE poll_id = ? AND answer = ?';
        $query = $this->pdo->prepare($sql);
        $query->execute(array($id, $cpt));
        $result = $query->fetch();
        //print_r($result);
        if ($result) {
            return $result['nb'];
        }
        return 0;
    }

    function getPossiblesAnswers($pollId) {
        $sql = 'SELECT answers FROM polls WHERE id = ?';
        $query = $this->pdo->prepare($sql);
        $query->execute(array($pollId));
        $result = $query->fetch();
        if ($result) {
            return explode("|", $result['answers']);
        }
        return 0;
    }

    function getAnswersPct($answers, $id) {
        $nbTab = array();
        $cpt = 1;
        foreach ($answers as $answer) {
            array_push($nbTab, $this->getCountOfAnswer($id, $cpt));
            $cpt++;
        }
        return $nbTab;
    }

    function getAnswersCount($pollId) {
        $countTab = array();
        $sql = 'SELECT answer FROM answers WHERE poll_id = ?';
        $query = $this->pdo->prepare($sql);
        $query->execute(array($pollId));
        while ($res = $query->fetch()) {
            array_push($countTab, $res["answer"]);
        }
        return $countTab;
    }

    function addPoll($question, $answers, $auteur) {
        $sql = 'INSERT INTO polls (question, answers, auteur_sondage) VALUES (?, ?, ?)';
        $query = $this->pdo->prepare($sql);
        $query->execute(array($question, $answers, $auteur));
        return true;
    }

    function addAnswer($user, $pollId, $answer) {
        $userId = $this->getIdFromUser($user);
        $sql = 'INSERT INTO answers (user_id, poll_id, answer) VALUES (?, ?, ?)';
        $query = $this->pdo->prepare($sql);
        $query->execute(array($userId, $pollId, $answer));
    }
}