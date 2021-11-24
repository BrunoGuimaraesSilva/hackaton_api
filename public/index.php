<?php

use Hackathon\Database;
use Hackathon\Produto;
use Hackathon\Empresa;
use Hackathon\Categoria;

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


$produtoQuery = new Produto($database);
$empresaQuery = new Empresa($database);
$categoriaQuery = new Categoria($database);

$app->get('/produtos', function (Request $request, Response $response) use ($produtoQuery){
    $response->getBody()->write(json_encode($produtoQuery->getAll()));
    return $response->withHeader('Content-Type', 'application/json')
             ->withStatus(200);
});

$app->get('/produto/{id}', function (Request $request, Response $response, $args) use ($produtoQuery) {
    
    $id = $args['id'];
    $produto = $produtoQuery->getById($id);
    if (is_null($produto)) {
        $response->getBody()->write('Not Found');
        return $response->withStatus(404);
    }

    $response->getBody()->write(json_encode($produto));
    return $response
              ->withHeader('Content-Type', 'application/json');
});

$app->post('/produto', function (Request $request, Response $response) use ($produtoQuery) {
    
    $produtoRequest = json_decode($request->getBody()->getContents());
    $id = $produtoQuery->post($produtoRequest);
    $newProduto = $produtoQuery->getById($id);
    
    $response->getBody()->write(json_encode($newProduto));
    return $response
             ->withHeader('Content-Type', 'application/json')
             ->withStatus(201);

})->add($logMiddleware);

$app->put('/produto/{id}', function (Request $request, Response $response, array $args) use ($produtoQuery) {
    
    $id = $args['id'];
    
    $materia = $produtoQuery->post($id);
    if (is_null($materia)) {
        return $response->withStatus(404);
    }

    $materiaRequest = json_decode($request->getBody()->getContents());
    
    $materia->nome = $materiaRequest->nome;
    $materia->dia = $materiaRequest->dia;
    $materia->horario = $materiaRequest->horario;

    unset($materia->dataatualizacao);

    $produtoQuery->update($materia);

    $response->getBody()->write(json_encode($produtoQuery->post($id)));
    return $response->withHeader('Content-Type', 'application/json')
                    ->withStatus(200); 

});

$app->delete('/produto/{id}', function (Request $request, Response $response, $args) use ($produtoQuery) {
    $produto = $produtoQuery->delete($args['id']);
    if (is_null($produto)) {
        return $response->withStatus(404);
    }
    $produtoQuery->delete($produto);
    return $response->withStatus(204);
});

$app->get('/empresas', function (Request $request, Response $response) use ($empresa){
    $response->getBody()->write(json_encode($empresa->get()));
    return $response->withHeader('Content-Type', 'application/json')
             ->withStatus(200);
});

$app->get('/categorias', function (Request $request, Response $response) use ($categoria){
    $response->getBody()->write(json_encode($categoria->get()));
    return $response->withHeader('Content-Type', 'application/json')
             ->withStatus(200);
});





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