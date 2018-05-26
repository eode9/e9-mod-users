<?php
namespace E9\User\Action\Web;

use E9\User\Document\User;
use Slim\Container;
use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

/**
 * Class LoginAction
 * @package E9\User\Action\Web
 */
final class DisplayLoginPage
{
    /**
     * @var Twig
     */
    private $view;

    /**
     * @var User|null
     */
    private $user;

    /**
     * @var Messages
     */
    private $flash;

    /**
     * @param $c Container
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function __construct(Container $c)
    {
        $this->view = $c->get('view');
        $this->user = $c->get('user');
        $this->flash = $c->get('flash');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $args) : Response
    {
        if ($this->user) {
            return $response->withRedirect('/', 302);
        }

        $this->view->render($response, 'mod-users/views/user/login.twig', array(
            'csrf_name' => $request->getAttribute('csrf_name'),
            'csrf_value' => $request->getAttribute('csrf_value'),
            'messages' => $this->flash->getMessages()
        ));

        return $response;
    }
}
