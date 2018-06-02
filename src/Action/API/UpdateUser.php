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
final class UpdateUser extends AbstractAPIAction
{
    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param $c Container
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function __construct(Container $c)
    {
        $this->dm = $c->get('dm');
        $this->validator = $c->get('validator');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $user = $this->dm->getRepository(User::class)->find($args['id']);

        if ($user === null) {
            return $this->prepareError($response, [
                'message' => 'User not found',
                'data' => [],
                'code' => 0,
            ],
                404);
        }

        $this->dm->getHydratorFactory()->hydrate($user, $request->getParams());

        /** @var ConstraintViolationList $errors */
        $constraints = $this->validator->validate($user);
        if ($constraints->count()) {
            return $this->prepareFail($response, [
                'message' => 'Validation errors in your request',
                'data' => [
                    'errors' => $this->getValidationErrors($constraints),
                ],
                'code' => 0
            ], 400);
        }

        try {
            $this->dm->persist($user);
            $this->dm->flush();
        } catch (\Exception $e) {
            return $this->prepareError($response, [
                'message' => $e->getMessage()
            ], 500);
        }

        $data['user'] = $user;

        return $this->prepareSuccess($response, $data, 200);
    }
}
