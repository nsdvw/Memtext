<?php
namespace Memtext\Handler;

use Slim\Handlers\NotFound; 
use Slim\Views\Twig; 
use Psr\Http\Message\ServerRequestInterface as Request; 
use Psr\Http\Message\ResponseInterface as Response;

class NotFoundHandler extends NotFound
{
    private $view;

    public function __construct(Twig $view)
    { 
        $this->view = $view; 
    }

    public function __invoke(Request $request, Response $response)
    {
        $this->view->render($response, 'not_found.twig');
        return $response->withStatus(404); 
    }
}
