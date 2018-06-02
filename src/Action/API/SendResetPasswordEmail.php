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
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $email = $request->getParam('email');
        if ($email === null) {
            return $this->prepareError($response, 'Email is required', 400, array());
        }

        /** @var User $user */
        $user = $this->dm->getRepository(User::class)->findBy(['email' => $email]);
        $user->resetPasswordToken = Base62::encode(random_bytes(16));

        try {
            $this->dm->flush();
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
}
