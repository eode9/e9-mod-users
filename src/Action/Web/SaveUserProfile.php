<?php
namespace E9\User\Action\Web;

use Doctrine\ODM\MongoDB\DocumentManager;
use E9\User\Document\User;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AuthAction
 * @package E9\User\Action\Web
 */
final class SaveUserProfile
{
    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * @var User
     */
    private $user;

    /**
     * HomeAction constructor.
     * @param $c Container
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function __construct($c)
    {
        $this->user = $c->get('user');
        $this->dm = $c->get('dm');
    }

    public function __invoke(Request $request, Response $response, $args) : Response
    {
        $this->dm->getHydratorFactory()->hydrate($this->user, $request->getParams());
        $this->dm->flush();

        return $response->withRedirect('/');
    }
}
