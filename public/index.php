<?php

use Hackathon\Database;
use Hackathon\Query;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Symfony\Component\VarDumper\VarDumper;
use Slim\Psr7\Response as Psr7Response;

require_once './../vendor/autoload.php';

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

$database = new Database(
    getenv('DATABASE_HOST'),
    getenv('DATABASE_NAME'),
    getenv('DATABASE_USER'),
    getenv('DATABASE_PASS')
);

$authMiddleware =  function (Request $request, RequestHandlerInterface $handler) {
    if (!isset($request->getHeaders()['Authorization'][0])) {
        $response = new Psr7Response();
        $response->getBody()->write(
            json_encode(['error' => 'Token não informado'])
        );
        return $response->withHeader('Content-Type', 'application/json')
                 ->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST);
    }
    return $handler->handle($request);
};

$logMiddleware = function (Request $request, RequestHandlerInterface $handler) {
    $inicio = microtime(true);
    $response = $handler->handle($request);    
    $fim = microtime(true);
    file_put_contents("../log/access_log",
        sprintf("%s [%s] %s %s %ss\n",
            date("d/m/Y H:i:s"),
            $request->getMethod(),
            $request->getUri(),
            $response->getStatusCode(),
            round($fim-$inicio, 2)            
        ),
        FILE_APPEND
    );
    return $response;
};


$query = new Query($database);


$app->get('/', function(Request $request, Response $response){
    $response->getBody()->write("Primeira rota");
    return $response;
});

$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name!");
    return $response;
});

$app->get('/produtos', function (Request $request, Response $response) use ($query){
    $response->getBody()->write(json_encode($query->get_produtos()));
    return $response->withHeader('Content-Type', 'application/json')
             ->withStatus(200);
});

$app->post('/produto', function (Request $request, Response $response) use ($query) {
    
    $produtoRequest = json_decode($request->getBody()->getContents());
    
    
    $id = $query->post_produtos($produtoRequest);
exit;
    $newMateria = $query->find($id, Materia::class);
    
    $response->getBody()->write(json_encode($newMateria));
    return $response
             ->withHeader('Content-Type', 'application/json')
             ->withStatus(201);

})->add($logMiddleware);

$app->get('/server', function (Request $request, Response $response) {
    $response->getBody()->write(
        json_encode([
                    'IP' => $request->getServerParams()['SERVER_ADDR']
                    ])
    );
    return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
});


$app->run();