<?php

//app.request.resquestURI
//use Symfony\Component\HttpFoundation\Request;
$loader = include('vendor/autoload.php');
$loader->add('', 'src');
$app = new Silex\Application();
$app['debug'] = true;
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path'=>__DIR__ . '/views'));
$app['model'] = new Sondages\Model(
        '127.0.0.1', // Hôte
        'iut_gregwar_td5', // Base de données
        'root', // Utilisateur
        ''     // Mot de passe
);
/*
  $app->register(new Silex\Provider\DoctrineServiceProvider(), array(
  'db.options' => array(
  'driver' => 'pdo_mysql',
  'dbhost' => '127.0.0.1',
  'dbname' => 'td5_gregwar',
  'user' => 'root',
  'password' => '',
  ),
  ));
  $app['userManager'] = $app->share(function() use ($app) {
  return new Sondages\UserManager($app['db'], $app);
  }); */
$app->get('/', function() use ($app) {
    return $app['twig']->render('home.html.twig', array(
                'user'=>$app['model']->checkConnection($app['session']->get('user'), $app)
    ));
})->bind('home');
/*
  $app->register(new Silex\Provider\SecurityServiceProvider(), array(
  'security.firewalls' => array(
  'foo' => array('pattern' => '^/foo'), // Example of an url available as anonymous user
  'default' => array(
  'pattern' => '^.*$',
  'anonymous' => true, // Needed as the login path is under the secured area
  'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
  'logout' => array('logout_path' => '/logout'), // url to call for logging out
  'users' => $app->share(function() use ($app) {
  return new Sondages\UserProvider($app['db']);
  }),
  ),
  ),
  'security.access_rules' => array(
  array('^/create', 'ROLE_USER'),
  array('^/foo', ''), // This url is available as anonymous user
  )
  )); */
$app->match('/login', function() use ($app) {
    if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
        if (!empty(filter_input(INPUT_POST, 'login') && !empty(filter_input(INPUT_POST, 'password')))) {
            $app['model']->login(filter_input(INPUT_POST, 'login'), filter_input(INPUT_POST, 'password'), $app);
        }
    }
    return $app['twig']->render('login.html.twig', array(
                'user'=>$app['model']->checkConnection($app['session']->get('user'))
    ));
})->bind('login');
$app->match('/logout', function() use ($app) {
    $app['session']->clear();
    return $app->redirect('/');
})->bind('logout');
$app->match('/register', function() use ($app) {
    if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'POST') {
        if ((null !== filter_input(INPUT_POST, 'login')) && (null !== filter_input(INPUT_POST, 'password'))) {
            $boolRegister = $app['model']->register(filter_input(INPUT_POST, 'login'), filter_input(INPUT_POST, 'password'));
            return $app['twig']->render('register.html.twig', array(
                        'register'=>$boolRegister,
                        'missingField'=>false)
            );
        }
    }
    return $app['twig']->render('register.html.twig', array(
                'missingField'=>true));
})->bind('register');
// Ajouter un sondage
/* $app->match('/create', function() use ($app) {
  if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'POST') {
  if (!empty(filter_input(INPUT_POST, 'question')) &&
  !empty(filter_input(INPUT_POST, 'answer1')) &&
  !empty(filter_input(INPUT_POST, 'answer2'))) {
  $question = filter_input(INPUT_POST, 'question');
  $answer1 = filter_input(INPUT_POST, 'answer1');
  $answer2 = filter_input(INPUT_POST, 'answer2');
  $answer3 = filter_input(INPUT_POST, 'answer3');
  return $app['twig']->render('create.html.twig', array(
  'formOK'=>$app['model']->addPoll($question, $answer1, $answer2, $answer3, $app['session']->get('user'))));
  }
  else {
  return $app['twig']->render('create.html.twig', array(
  'formOK'=>false));
  }
  }
  return $app['twig']->render('create.html.twig');
  })->bind('create'); */
$app->match('/create', function() use ($app) {
    /* echo "<script>alert('[" . filter_input(INPUT_POST, 'answer2') . "]')</script>";
      if ((filter_input(INPUT_POST, 'answer3') == '')) {
      //echo "<script>alert('[" . filter_input(INPUT_POST, 'answer3') . "]')</script>";
      echo "<script>alert('ya pas');</script>";
      } */
    if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'POST') {
        if (!empty(filter_input(INPUT_POST, 'question')) &&
                !empty(filter_input(INPUT_POST, 'answer1')) &&
                !empty(filter_input(INPUT_POST, 'answer2'))) {
            /* while (filter_input(INPUT_POST, 'answer' . $nbAnswers) !== '') {
              echo "<script>alert('[" . filter_input(INPUT_POST, 'answer' . $nbAnswers) . "]')</script>";
              echo "<script>alert('" . $nbAnswers . "')</script>";
              $nbAnswers ++;
              } */
            $reponses = array();
            for ($nbReponses = 1; $nbReponses <= 10; $nbReponses++) {
                $reponse = filter_input(INPUT_POST, 'answer' . $nbReponses);
                if ($reponse != '') {
                    array_push($reponses, $reponse);
                }
            }
            $question = filter_input(INPUT_POST, 'question');
            return $app['twig']->render('create.html.twig', array(
                        'formOK'=>$app['model']->addPoll($question, implode("|", $reponses), $app['session']->get('user'))));
        }
        else {
            return $app['twig']->render('create.html.twig', array(
                        'formOK'=>false));
        }
    }
    return $app['twig']->render('create.html.twig');
})->bind('create');

$app->match('/polls', function() use ($app) {
    return $app['twig']->render('polls.html.twig', array(
                'polls'=>$app['model']->getPolls(),
                'user'=>$app['model']->checkConnection($app['session']->get('user'))
    ));
})->bind('polls');

$app->match('/pollsId/{id}', function($id) use ($app) {
    if (!empty(filter_input(INPUT_POST, 'answer'))) {
        if (!$app['model']->didIAnswer($app['session']->get('user'), $id)) {
            $app['model']->addAnswer($app['session']->get('user'), $id, filter_input(INPUT_POST, 'answer'));
        }
        else {
            echo "<script>alert('Tout doux, vous avez déjà voté');</script>";
        }
    }
    $answers = $app['model']->getPossiblesAnswers($id);
    $total = $app['model']->getAnswersCount($id);
    return $app['twig']->render('pollsId.html.twig', array(
                'userAnswered'=>$app['model']->didIAnswer($app['session']->get('user'), $id),
                'polls'=>$app['model']->getPollsFromId($id),
                'answers'=>$answers,
                'answersNb'=>$app['model']->getAnswersPct($answers, $id),
                'total'=>$app['model']->getTotalAnswers($total)
    ));
})->bind('pollsId');

$app->match('/myPolls', function() use ($app) {
    return $app['twig']->render('myPolls.html.twig', array(
                'user'=>$app['model']->checkConnection($app['session']->get('user')),
                'sondages'=>$app['model']->getMyPolls($app['session']->get('user'))
    ));
})->bind('myPolls');
/*
  $app->post('/user/{action}', function ($action) use ($app) {
  switch ($action) {
  case 'createUser':
  $result = $app['userManager']->insertNewUser(filter_input(INPUT_POST, 'login'), filter_input(INPUT_POST, 'password'), filter_input(INPUT_POST, 'role'));
  $returnArray = array(
  'insertingResult' => $result
  );
  return json_encode($returnArray);
  case 'deleteUser':
  $result = $app['userManager']->deleteUser(filter_input(INPUT_POST, 'id'));
  $returnArray = array(
  'deletingResult' => $result
  );
  return json_encode($returnArray);
  default:
  return false;
  }
  })->bind('userAction'); */
// Fait remonter les erreurs
$app->error(function($error) {
    throw $error;
});
//$app->boot();
$app->run();
