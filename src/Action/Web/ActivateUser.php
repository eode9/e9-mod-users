<?php
namespace E9\User\Action\Web;

use Doctrine\ODM\MongoDB\DocumentManager;
use E9\User\Document\User;
use RKA\Session;
use Slim\Container;
use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AuthenticateUser
 * @package E9\User\Action\Web
 */
final class ActivateUser
{
    /**
     * @var Messages
     */
    private $flash;

    /**
     * @var DocumentManager
     */
    private $dm;

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
        $this->flash = $c->get('flash');
        $this->dm = $c->get('dm');
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
        if (false === $request->getAttribute('csrf_result')) {
            $this->flash->addMessage('errors', 'Invalid form security. Please submit the form again.');

            return $response->withRedirect('/user/login', 302);
        }

        $user = $this->dm->getRepository(User::class)->findOneBy([
            'email' => $request->getParam('email'),
            'password' => md5(getenv('APP_SALT') . $request->getParam('password'))
        ]);

        if (!$user) {
            $this->flash->addMessage('errors', 'Username or password seems incorrect. Please check and submit again');
            return $response->withRedirect('/user/login', 302);
        }

        $this->session->set('user', json_encode($user));

        return $response->withRedirect('/');
    }
}
