<?php

namespace Contactless\Core\Action\API;

use Contactless\Core\Common\AbstractAPIAction;
use Contactless\Core\Entity\User;
use \Slim\Http\Request;
use \Slim\Http\Response;

use Firebase\JWT\JWT;
use Tuupola\Base62;

final class APIAuthAction extends AbstractAPIAction
{

    /**
     * Get User info from reset password key
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getUserInfoFromResetPasswordKey(Request $request, Response $response, array $args) : Response
    {
        $key = $request->getParam('key');
        if ($key === null) {
            return $this->prepareError($response, 'Reset password key is required', 400, array());
        }

        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['passwordKey' => $key]);
        if ($user === null) {
            return $this->prepareError($response, 'Reset password key not valid.', array(), 404);
        }

        return $this->prepareSuccess($response, ['fullname' => $user->getFullName()], 200);
    }

    /**
     * Reset password
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function resetPassword(Request $request, Response $response, array $args) : Response
    {
        $key = $request->getParam('key');
        if ($key === null) {
            return $this->prepareError($response, 'Reset password key is required', 400, array());
        }

        $password = $request->getParam('password');
        if ($password === null) {
            return $this->prepareError($response, 'New password is required', 400, array());
        }

        switch ($request->getParam('type')) {
            case 'user':
                /** @var User $user */
                $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['passwordKey' => $key]);
                if ($user === null) {
                    return $this->prepareError($response, 'Reset password key not valid', array(), 404);
                }
                try {
                    $user->password = md5(APP_SALT . $password);
                    $this->getEntityManager()->persist($user);
                    $this->getEntityManager()->flush();
                } catch (\Exception $e) {
                    return $this->prepareError($response, 'Unable to reset password ', array(), 500);
                }
                break;

            default:
                return $this->prepareError($response, 'Undefined type to register', array(), 422);
        }

        return $this->prepareSuccess($response, ['reset' => true], 200);
    }
}
