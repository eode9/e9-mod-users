<?php

namespace E9\User\Action\API;

use E9\Core\Action\AbstractAPIAction;
use Slim\Http\Request;
use Slim\Http\Response;

final class AuthAction extends AbstractAPIAction
{
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
    public function register(Request $request, Response $response, array $args)
    {
        $data = array();

        switch ($request->getParam('type')) {
            case 'user':

                $user = $this->userResource->createUser($request->getParams());

                // Validation
                $errors = $this->userResource->validate($user);

                if (is_array($errors) && count($errors)) {
                    return $this->prepareError($response, 'Validation failed', array(
                        '_errors' => $errors
                    ), 422);
                }

                // Save
                try {
                    $this->userResource->save($user);
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
    public function forgotPassword(Request $request, Response $response, array $args)
    {
        $data = array();


        switch ($request->getParam('type')) {
            case 'user':

                $email = $request->getParam('email', null);

                if ($email === null) {
                    return $this->prepareError($response, 'Email is required', 400, array());
                }

                $user = $this->userResource->getUserByEmail($email);

                $user->setResetPasswordKey(Base62::encode(random_bytes(16)));

                // Save
                try {
                    $this->userResource->save($user);
                } catch (\Exception $e) {
                    return $this->prepareError($response, $e->getMessage(), array(), 500);
                }

                // Send email @todo
                $result = $this->sendResetPassword($user);

                $data['sent'] = true;

                break;

            case 'crew':

            default:
                return $this->prepareError($response, 'Undefined type to register', array(), 422);

        }

        return $this->prepareSuccess($response, $data, 200);
    }

    /**
     * @param User $user
     */
    private function sendResetPassword(User $user)
    {
        $this->mail->setFrom('no-reply@app.io', 'App.io');
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
    public function getUserInfoFromResetPasswordKey(Request $request, Response $response, array $args)
    {
        $key = $request->getParam('key', null);

        if ($key === null) {
            return $this->prepareError($response, 'Reset password key is required', 400, array());
        }

        $user = $this->userResource->getUserByResetPasswordKey($key);

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
    public function resetPassword(Request $request, Response $response, array $args)
    {
        $key = $request->getParam('key', null);
        $password = $request->getParam('password', null);

        if ($key === null) {
            return $this->prepareError($response, 'Reset password key is required', 400, array());
        }

        if ($password === null) {
            return $this->prepareError($response, 'New password is required', 400, array());
        }

        switch ($request->getParam('type')) {
            case 'user':

                $user = $this->userResource->getUserByResetPasswordKey($key);

                if ($user === null) {
                    return $this->prepareError($response, 'Reset password key not valid', array(), 404);
                }

                if (!$this->userResource->setNewPassword($user, $password)) {
                    return $this->prepareError($response, 'Unable to reset password ', array(), 500);
                }

                break;

            default:
                return $this->prepareError($response, 'Undefined type to register', array(), 422);
        }

        return $this->prepareSuccess($response, ['reset' => true], 200);
    }

}
