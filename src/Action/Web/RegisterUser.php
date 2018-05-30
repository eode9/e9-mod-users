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
 * Class AuthAction
 * @package E9\User\Action\Web
 */
final class RegisterUser
{
    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * @var Messages
     */
    private $flash;

    /**
     * @var Session
     */
    private $session;

    /**
     * HomeAction constructor.
     * @param $c Container
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function __construct($c)
    {
        $this->view = $c->get('view');
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

        $user = new User();
        $this->dm->getHydratorFactory()->hydrate($user, $request->getParams());
        $user->password = md5(getenv('APP_SALT') . $request->getParam('password'));
        $this->dm->persist($user);
        $this->dm->flush();

        if (!$user) {
            $this->flash->addMessage('errors', 'Unable to create account. Please check and submit again');
            return $response->withRedirect('/user/register', 302);
        }

        $this->session->set('user_id', json_encode($user->id));

        return $response->withRedirect('/');
    }
}
