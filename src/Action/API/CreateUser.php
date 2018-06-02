<?php

namespace E9\User\Action\API;

use Doctrine\ODM\MongoDB\DocumentManager;
use E9\Core\Action\AbstractAPIAction;
use E9\User\Document\User;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class AuthenticateUser
 * @package E9\User\Action\API
 */
final class CreateUser extends AbstractAPIAction
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
        $data = array();

        $user = new User();

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

        $response->withAddedHeader('Location', sprintf('/v1/users/%s', $user->id));
        return $this->prepareSuccess($response, $data, 201);
    }
}
