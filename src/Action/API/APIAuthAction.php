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
    public function auth(Request $request, Response $response, array $args): Response
    {

    }

    /**
     * @api {post} api/v1/users Register a new user
     * @apiVersion 1.0.0
     * @apiName Registration
     * @apiGroup Auth
     * @apiParam {String} email User email
     * @apiParam {String} password User password
     * @apiParam {String} type Type (user, etc.)
     * @apiSuccess (201) {String} uuid User UUID
     * @apiSuccess (201) {String} fullname User full name
     * @apiSuccess (201) {String} company User company
     * @apiSuccess (201) {String} position User position
     * @apiSuccess (201) {String} email User email
     * @apiSuccess (201) {String} backup_email User backup email
     * @apiSuccess (201) {String} mobile_phone User mobile phone
     * @apiSuccess (201) {String} office_phone User office phone
     * @apiSuccess (201) {String} web_link User web link
     * @apiSuccess (201) {String} about_me User about me text
     * @apiSuccess (201) {DateTime} created_at User creation date
     * @apiSuccess (201) {DateTime} updated_at User last update date
     * @apiSuccess (201) {Array} workspaces User workspaces and details
     * @apiSuccess (201) {Array} events User events and details
     */

    /**
     * Register a user
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function register(Request $request, Response $response, array $args): Response
    {
        $data = array();

        switch ($request->getParam('type')) {
            case 'user':
                $user = new User();
                $this->getEntityManager()->getRepository(User::class)->hydrate($user, $request->getParams());

                // Validation
                $constraints = $this->getValidator()->validate($user);
                if ($constraints->count()) {
                    return $this->prepareError($response, 'Please fix issues', [
                        '_errors' => $this->getValidationErrors($constraints),
                        'user' => $user
                    ], 422);
                }

                try {
                    $this->getEntityManager()->persist($user);
                    $this->getEntityManager()->flush();
                } catch (\Exception $e) {
                    return $this->prepareError($response, $e->getMessage(), array(), 500);
                }

                // Send email @todo
                // ...


                $data['user'] = $user;

                $data['scopes'] = [
                    'user.logout'
                ];

                break;

            case 'crew':

            default:
                return $this->prepareError($response, 'Undefined type to register', array(), 422);

        }

        return $this->prepareSuccess($response, $data, 201);
    }

    /**
     * @api {post} api/v1/reset-password Register a new user
     * @apiVersion 1.0.0
     * @apiName Reset password
     * @apiGroup Auth
     * @apiParam {String} email User email
     * @apiParam {String} type Type (user, etc.)
     * @apiSuccess (201) {String} uuid User UUID
     * @apiSuccess (201) {String} fullname User full name
     * @apiSuccess (201) {String} company User company
     * @apiSuccess (201) {String} position User position
     * @apiSuccess (201) {String} email User email
     * @apiSuccess (201) {String} backup_email User backup email
     * @apiSuccess (201) {String} mobile_phone User mobile phone
     * @apiSuccess (201) {String} office_phone User office phone
     * @apiSuccess (201) {String} web_link User web link
     * @apiSuccess (201) {String} about_me User about me text
     * @apiSuccess (201) {DateTime} created_at User creation date
     * @apiSuccess (201) {DateTime} updated_at User last update date
     * @apiSuccess (201) {Array} workspaces User workspaces and details
     * @apiSuccess (201) {Array} events User events and details
     */

    /**
     * Reset password
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function forgotPassword(Request $request, Response $response, array $args) : Response
    {
        $email = $request->getParam('email');
        if ($email === null) {
            return $this->prepareError($response, 'Email is required', 400, array());
        }

        /** @var User $user */
        $user = $this->getEntityManager()->getRepository(User::class)->findBy(['email' => $email]);
        $user->setResetPasswordKey(Base62::encode(random_bytes(16)));

        try {
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            return $this->prepareError($response, $e->getMessage(), array(), 500);
        }

        $this->sendResetPassword($user);

        return $this->prepareSuccess($response, ['user' => $user], 200);
    }

    /**
     * @param User $user
     */
    private function sendResetPassword(User $user)
    {
        // @todo
        $this->getMailer()->setFrom('no-reply@contactless.io', 'Contactless.io');
        $this->mail->addAddress($user->getEmail(), $user->getFullName() === null ? $user->getEmail() : $user->getFullName());
        $this->mail->isHTML(true);

        $this->mail->Subject = 'Reset password';

        $this->mail->Body = $this->view->fetch(
            'views/user/emails/reset-password.twig',
            array(
                'front_base_url' => FRONT_BASE_URL,
                'user' => $user
            )
        );

        $result = $this->mail->send();

        if (!$result) {
            // @todo error 500
        }
    }

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
