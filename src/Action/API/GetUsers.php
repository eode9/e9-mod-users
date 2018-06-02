<?php

namespace E9\User\Action\API;

use Doctrine\ODM\MongoDB\DocumentManager;
use E9\Core\Action\AbstractAPIAction;
use E9\User\Document\User;
use Firebase\JWT\JWT;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tuupola\Base62;

/**
 * Class AuthenticateUser
 * @package E9\User\Action\API
 */
final class GetUsers extends AbstractAPIAction
{
    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * @param $c Container
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function __construct(Container $c)
    {
        $this->dm = $c->get('dm');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $order = [];
        $limit = 20;
        $page = $request->getParams('page', 1);
        $offset = ($page-1) * $limit;

        $total = $this->dm->getRepository(User::class)->findBy([])->count();
        $users = $this->dm->getRepository(User::class)->findBy([], $order, $limit, $offset);

        $response->withAddedHeader('Pagination-Count', $total);
        $response->withAddedHeader('Pagination-Page', ceil($total / 20));
        $response->withAddedHeader('Pagination-Limit', 20);

        return $this->prepareSuccess($response, [
            'users' => $users
        ], 200);
    }
}
