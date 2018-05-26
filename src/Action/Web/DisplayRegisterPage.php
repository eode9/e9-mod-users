<?php
namespace E9\User\Action\Web;

use E9\User\Document\User;
use Slim\Container;
use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

/**
 * Class AuthAction
 * @package E9\User\Action\Web
 */
final class DisplayRegisterPage
{
    /**
     * @var Twig
     */
    private $view;

    /**
     * @var Messages
     */
    private $flash;

    /**
     * @var User|null
     */
    private $user;

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
            $this->flash->addMessage('errors', 'You are already logged');
            return $response->withRedirect('/', 302);
        }

        $this->view->render($response, 'mod-users/views/user/register.twig', array(
            'csrf_name' => $request->getAttribute('csrf_name'),
            'csrf_value' => $request->getAttribute('csrf_value'),
            'messages' => $this->flash->getMessages()
        ));

        return $response;
    }
}
