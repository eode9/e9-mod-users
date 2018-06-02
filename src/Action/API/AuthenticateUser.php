<?php

namespace E9\User\Action\API;

use Doctrine\ODM\MongoDB\DocumentManager;
use E9\Core\Action\AbstractAPIAction;
use E9\User\Document\User;
use Firebase\JWT\JWT;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Tuupola\Base62;

/**
 * Class AuthenticateUser
 * @package E9\User\Action\API
 */
final class AuthenticateUser extends AbstractAPIAction
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
     * @api {post} api/v1/auth Auth entity
     * @apiVersion 1.0.0
     * @apiName Authentication
     * @apiGroup Auth
     *
     * @apiSampleRequest https://app.contactless.io/api/v1/auth
     *
     * @apiParam {String} [api_key] API key
     * @apiParam {String} [api_secret] API secret key
     * @apiParam {String} [type] api
     *
     * @apiSuccess {String} JSON Web Token
     * @apiSuccess {Array} Logged entity
     *
     */

    /**
     * Authenticate Entity
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $data = array();

        /** @var User $user */
        $user = $this->dm->getRepository(User::class)->findOneBy([
            'email' => $request->getParam('email'),
            'password' => md5(getenv('APP_SALT') . $request->getParam('password'))
        ]);

        if ($user === null) {
            return $this->prepareError($response, 'Invalid credentials', array(), 401);
        }

        // @todo
        $scope = [
            'user.logout'
        ];

//        $data['user'] = $user;
        $data['scope'] = $scope;

        $now = new \DateTime();
        $future = new \DateTime('now +1 days');
        $jti = Base62::encode(random_bytes(16));
        $payload = [
            'iat' => $now->getTimestamp(),
            'exp' => $future->getTimestamp(),
            'jti' => $jti,
            'sub' => $user->email,
            'scope' => $scope
        ];
        $token = JWT::encode($payload, getenv('JWT_SECRET'));
        $data['token'] = $token;

        return $this->prepareSuccess($response, $data, 200);
    }
}
