<?php
namespace E9\User\Action\Web;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

/**
 * Class AuthAction
 * @package E9\User\Action\Web
 */
final class DisplayForgotPasswordPage
{
    /**
     * @var Twig
     */
    private $view;

    /**
     * @param $c Container
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function __construct(Container $c)
    {
        $this->view = $c->get('view');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $args) : Response
    {
        $this->view->render($response, 'mod-users/views/user/forgot-password.twig');

        return $response;
    }
}
