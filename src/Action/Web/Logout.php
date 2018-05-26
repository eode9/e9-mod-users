<?php
namespace E9\User\Action\Web;

use RKA\Session;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class Logout
 * @package E9\User\Action\Web
 */
final class Logout
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @param $c Container
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function __construct(Container $c)
    {
        $this->session = $c->get('session');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $args) : Response
    {
        $this->session->delete('user');

        return $response->withRedirect('/', 302);
    }
}
