<?php

namespace E9\User\Action\Web;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class UserAction
 * @package E9\User\Action\Web
 */
final class UserAction
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function register(Request $request, Response $response, $args) : Response
    {
        if ($this->getCurrentUser()) {
            $this->getFlash()->addMessage('errors', 'You are already logged');
            return $response->withRedirect('/', 302);
        }

        $this->getView()->render($response, 'views/user/register.twig', array(
            'csrf_name' => $request->getAttribute('csrf_name'),
            'csrf_value' => $request->getAttribute('csrf_value'),
            'messages' => $this->getFlash()->getMessages()
        ));

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     */
    public function createAccount(Request $request, Response $response, array $args) : Response
    {
        if (false === $request->getAttribute('csrf_result')) {
            $this->getFlash()->addMessage('errors', 'Invalid form security. Please submit the form again.');
            return $response->withRedirect('/user/login', 302);
        }

        $user = $this->getUserResource()->create($request->getParams());
        $user->password = md5(getenv('APP_SALT') . $request->getParam('password'));
        $this->getUserResource()->save($user);

        if (!$user) {
            $this->getFlash()->addMessage('errors', 'Unable to create account. Please check and submit again');
            return $response->withRedirect('/user/register', 302);
        }

        $this->getSession()->set('user', json_encode($user));

        return $response->withRedirect('/');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function login(Request $request, Response $response, $args) : Response
    {
        if ($this->getCurrentUser()) {
            return $response->withRedirect('/home', 302);
        }

        $this->getView()->render($response, 'views/user/login.twig', array(
            'csrf_name' => $request->getAttribute('csrf_name'),
            'csrf_value' => $request->getAttribute('csrf_value'),
            'messages' => $this->getFlash()->getMessages()
        ));

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     */
    public function auth(Request $request, Response $response, array $args) : Response
    {
        if (false === $request->getAttribute('csrf_result')) {
            $this->getFlash()->addMessage('errors', 'Invalid form security. Please submit the form again.');

            return $response->withRedirect('/user/login', 302);
        }

        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy([
            'email' => $request->getParam('email'),
            'password' => md5(getenv('APP_SALT') . $request->getParam('password'))
        ]);

        if (!$user) {
            $this->getFlash()->addMessage('errors', 'Username or password seems incorrect. Please check and submit again');
            return $response->withRedirect('/user/login', 302);
        }

        $this->getSession()->set('user', json_encode($user));

        return $response->withRedirect('/');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function logout(Request $request, Response $response, $args) : Response
    {
        $this->getSession()->delete('user');

        return $response->withRedirect('/', 302);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function profile(Request $request, Response $response, $args) : Response
    {
        $this->getView()->render($response, 'views/user/profile.twig', array(
            'csrf_name' => $request->getAttribute('csrf_name'),
            'csrf_value' => $request->getAttribute('csrf_value'),
            'messages' => $this->getFlash()->getMessages()
        ));

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function saveProfile(Request $request, Response $response, $args) : Response
    {
        $user = $this->getCurrentUser();

        $this->getUserResource()->hydrate($user, $request->getParams());

        $user->password = md5(getenv('APP_SALT') . $request->getParam('password'));

        $this->getUserResource()->save($user);

        return $response->withRedirect('/process');
    }
}
