<?php
namespace E9\User\Action\Web;

use Doctrine\ODM\MongoDB\DocumentManager;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @package E9\User\Action\Web
 */
final class SendResetPasswordEmail
{
    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * @param $c Container
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function __construct($c)
    {
        $this->dm = $c->get('dm');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $args) : Response
    {
        // @todo

        return $response;
    }
}
