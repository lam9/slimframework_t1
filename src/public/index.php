<?php
/**
 * Created by PhpStorm.
 * User: lmo
 * Date: 01.11.17
 * Time: 12:45
 */


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require 'TicketMapper.class.php';

/**
 * définition de la configuration de l'application
 */
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db']['host']   = "localhost";
$config['db']['user']   = "root";
$config['db']['pass']   = "root";
$config['db']['dbname'] = "exampleapp";


$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();

/**
 * Configuration de monolog dans le container
 *
 */
$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};


/**
 * Configuration de l'accès à la base de donnée
 */
$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};


/***
 * Configuration des template de vue
 */
$container['view'] = new \Slim\Views\PhpRenderer("../templates/");

/**
 * Création des routes
 */

$app->get('/tickets', function (Request $request, Response $response) {
    $this->logger->addInfo("Ticket list");
    $mapper = new TicketMapper($this->db);
    $tickets = $mapper->getTickets();

    // Récuperation de paramètres get.
    $param = $request->getQueryParams();


   // $response->getBody()->write(var_export($tickets, true));
    //$response->getBody()->write(var_dump($tickets).var_dump($param));

    $response = $this->view->render($response, "tickets.phtml", ["tickets" => $tickets, "router" => $this->router]);
    return $response;
});


$app->get('/ticket/{id}', function (Request $request, Response $response, $args) {
    $ticket_id = (int)$args['id'];
    $mapper = new TicketMapper($this->db);
    $ticket = $mapper->getTicketById($ticket_id);
    $response->getBody()->write(var_dump($ticket));
    return $response;
})->setName("ticket-detail");


/**
 * tester avec REST client
 */
$app->post('/ticket/new', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $ticket_data = [];
    $ticket_data['title'] = filter_var($data['title'], FILTER_SANITIZE_STRING);
    $ticket_data['description'] = filter_var($data['description'], FILTER_SANITIZE_STRING);
    // ...
    $response->getBody()->write(var_dump($ticket_data));
    return $response;
});

$app->get('/', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("index.php");
    var_dump($_SERVER['DOCUMENT_ROOT']);
    var_dump($_SERVER['REQUEST_URI']);
    var_dump($_SERVER['ENV_HTACCESS_READING']);
    $this->logger->addInfo('quelque chose à notifier'); // Ajout d'une entére dans les logs
    return $response;
});


$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    return $response;
});
$app->run();