<?php

namespace E9\User\Action\API;

use \Slim\Http\Request;
use \Slim\Http\Response;

use Psr\Log\LoggerInterface;
use App\Core\Resource\UserService;

final class APIUserAction extends APIAbstractAction
{
    /**
     * @api {get} api/v1/users/{uuid} Get User details
     * @apiVersion 1.0.0
     * @apiName GetUser
     * @apiGroup User
     * @apiParam {String} User UUID.
     * @apiSuccess {String} uuid User UUID
     * @apiSuccess {String} fullname User full name
     * @apiSuccess {String} company User company
     * @apiSuccess {String} position User position
     * @apiSuccess {String} email User email
     * @apiSuccess {String} backup_email User backup email
     * @apiSuccess {String} mobile_phone User mobile phone
     * @apiSuccess {String} office_phone User office phone
     * @apiSuccess {String} web_link User web link
     * @apiSuccess {String} about_me User about me text
     * @apiSuccess {DateTime} created_at User creation date
     * @apiSuccess {DateTime} updated_at User last update date
     * @apiSuccess {Array} workspaces User workspaces and details
     * @apiSuccess {Array} events User events and details
     */

    /**
     * Get User from UUID
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getUser(Request $request, Response $response, array $args)
    {
        $user_uuid = array_key_exists('uuid', $args) ? $args['uuid'] : false;

        if ($user_uuid === false) {
            return $this->prepareError($response, 'User UUID is required', 400, array());
        }

        $user = $this->getUserResource()->getUser($user_uuid);

        if ($user === false) {
            return $this->prepareError($response, 'User not found', 404, array());
        }

        return $this->prepareSuccess($response, ['user' => $user], 200);
    }

    /**
     * Check if email exist in database
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function userExists(Request $request, Response $response, array $args)
    {
        $email = $request->getParam('email', null);

        if ($email === null) {
            return $this->prepareError($response, 'Email is required', 400, array());
        }

        $user = $this->getUserResource()->getUserByEmail($email);

        $exists  = false;
        if ($user !== null) {
            $exists = true;
        }

        return $this->prepareSuccess($response, ['exists' => $exists ], 200);
    }


    /**
     * Lock session User
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function lockSession(Request $request, Response $response, array $args)
    {
        $user_uuid = array_key_exists('uuid', $args) ? $args['uuid'] : false;

        if ($user_uuid === false) {
            return $this->prepareError($response, 'User UUID is required', 400, array());
        }

        $user = $this->userResource->getUser($user_uuid);

        if ($user === null) {
            return $this->prepareError($response, 'User not found', array(), 404);
        }

        if (!$this->userResource->lockSession($user)) {
            return $this->prepareError($response, 'Unable to lock session', array(), 500);
        }

        return $this->prepareSuccess($response, ['locked' => true], 200);
    }

    /**
     * @api {put} api/v1/users/{uuid} Update user details
     * @apiVersion 1.0.0
     * @apiName UpdateUser
     * @apiGroup User
     * @apiParam {String} email User email
     * @apiParam {String} password User password
     * @apiParam {String} fullname User full name
     * @apiParam {String} company User company
     * @apiParam {String} position User position
     * @apiParam {String} email User email
     * @apiParam {String} backup_email User backup email
     * @apiParam {String} mobile_phone User mobile phone
     * @apiParam {String} office_phone User office phone
     * @apiParam {String} web_link User web link
     * @apiParam {String} about_me User about me text
     * @apiSuccess (200) {String} uuid User UUID
     * @apiSuccess (200) {String} fullname User full name
     * @apiSuccess (200) {String} company User company
     * @apiSuccess (200) {String} position User position
     * @apiSuccess (200) {String} email User email
     * @apiSuccess (200) {String} backup_email User backup email
     * @apiSuccess (200) {String} mobile_phone User mobile phone
     * @apiSuccess (200) {String} office_phone User office phone
     * @apiSuccess (200) {String} web_link User web link
     * @apiSuccess (200) {String} about_me User about me text
     * @apiSuccess (200) {DateTime} created_at User creation date
     * @apiSuccess (200) {DateTime} updated_at User last update date
     * @apiSuccess (200) {Array} workspaces User workspaces and details
     * @apiSuccess (200) {Array} events User events and details
     */

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function updateUser(Request $request, Response $response, array $args)
    {
        $user_uuid = array_key_exists('uuid', $args) ? $args['uuid'] : false;

        if ($user_uuid === false) {
            return $this->prepareError($response, 'Bad request. User key is required.', array(), 406);
        }

        $user = $this->userResource->getUser($user_uuid);

        if ($user === null) {
            return $this->prepareError($response, 'User not found', array(), 404);
        }

        $user = $this->userResource->hydrate($user, $request->getParams());

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

        return $this->prepareSuccess($response, ['user' => $user], 200);
    }

    /**
     * @api {put} api/v1/users/{uuid} Update user details
     * @apiVersion 1.0.0
     * @apiName UpdateUser
     * @apiGroup User
     * @apiParam {String} email User email
     * @apiParam {String} password User password
     * @apiParam {String} fullname User full name
     * @apiParam {String} company User company
     * @apiParam {String} position User position
     * @apiParam {String} email User email
     * @apiParam {String} backup_email User backup email
     * @apiParam {String} mobile_phone User mobile phone
     * @apiParam {String} office_phone User office phone
     * @apiParam {String} web_link User web link
     * @apiParam {String} about_me User about me text
     * @apiSuccess (200) {String} uuid User UUID
     * @apiSuccess (200) {String} fullname User full name
     * @apiSuccess (200) {String} company User company
     * @apiSuccess (200) {String} position User position
     * @apiSuccess (200) {String} email User email
     * @apiSuccess (200) {String} backup_email User backup email
     * @apiSuccess (200) {String} mobile_phone User mobile phone
     * @apiSuccess (200) {String} office_phone User office phone
     * @apiSuccess (200) {String} web_link User web link
     * @apiSuccess (200) {String} about_me User about me text
     * @apiSuccess (200) {DateTime} created_at User creation date
     * @apiSuccess (200) {DateTime} updated_at User last update date
     * @apiSuccess (200) {Array} workspaces User workspaces and details
     * @apiSuccess (200) {Array} events User events and details
     */

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function unlockSession(Request $request, Response $response, array $args)
    {
        $user_uuid = array_key_exists('uuid', $args) ? $args['uuid'] : false;

        if ($user_uuid === false) {
            return $this->prepareError($response, 'Bad request. User key is required.', array(), 406);
        }

        $user = $this->userResource->getUser($user_uuid);

        if ($this->userResource->unlock($user, $request->getParam('password')) === false) {
            return $this->prepareError($response, 'Password does not match', array(
                '_errors' => 'Password does not match'
            ), 401);
        }

        try {
            $this->userResource->save($user);
        } catch (\Exception $e) {
            return $this->prepareError($response, $e->getMessage(), array(), 500);
        }

        return $this->prepareSuccess($response, ['user' => $user], 200);
    }
}
