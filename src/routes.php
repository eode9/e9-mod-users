<?php

/**
 * User routes
 */

$app->get('/user/login', UserAction::class . ':login')->setName('user-login');
$app->post('/user/auth', UserAction::class . ':auth')->setName('user-auth');

$app->get('/user/logout', UserAction::class . ':logout')->setName('user-logout');

$app->get('/user/profile', UserAction::class . ':profile');
$app->post('/user/profile', UserAction::class . 'UserAction:save');

$app->group('/user/register', function () {
    $this->get('/activate/{id}/{token}', UserAction::class . ':activate');
    $this->get('[/]', UserAction::class . ':register')->setName('user-register');
    $this->post('/create', \App\Core\Action\UserAction::class . ':create');
});

$app->group('/user/forgot-email', function () {
    $this->get('[/]', 'App\Action\User\Forgot:main');
    $this->post('[/]', 'App\Action\User\Forgot:main');
    $this->get('/reset/{id}/{token}', 'App\Action\User\Forgot:reset_password');
});

// Social Signin
$app->map(['GET', 'POST'], '/google', 'App\Action\User\Social:google');
$app->map(['GET', 'POST'], '/fb', 'App\Action\User\Social:facebook');
$app->map(['GET', 'POST'], '/social/signup', 'App\Action\User\Social:signup');
$app->map(['GET', 'POST'], '/social/signup/success', 'App\Action\User\Social:success');
$app->group('/callback', function () {
    $this->map(['GET', 'POST'], '[/]', 'App\Action\User\Social:callback');
});

/**
 * API Auth && Registration
 */
$app->post('/api/v1/auth', App\Core\Action\API\Auth\AuthAction::class);
$app->post('/api/v1/register', 'App\Core\Action\API\APIAuthAction:register');

$app->options('/api/v1/forgot-password', 'App\Core\Action\API\APIAuthAction:forgotPassword');
$app->put('/api/v1/forgot-password', 'App\Core\Action\API\APIAuthAction:forgotPassword');

$app->options('/api/v1/reset-password-info', 'App\Core\Action\API\APIAuthAction:getUserInfoFromResetPasswordKey');
$app->get('/api/v1/reset-password-info', 'App\Core\Action\API\APIAuthAction:getUserInfoFromResetPasswordKey');

$app->options('/api/v1/reset-password', 'App\Core\Action\API\APIAuthAction:resetPassword');
$app->put('/api/v1/reset-password', 'App\Core\Action\API\APIAuthAction:resetPassword');

/**
 * API User routes
 */
$app->options('/api/v1/users/{uuid}', 'App\Core\Action\API\APIUserAction:getUser');
$app->get('/api/v1/users/{uuid}', 'App\Core\Action\API\APIUserAction:getUser');
$app->put('/api/v1/users/{uuid}', 'App\Core\Action\API\APIUserAction:updateUser');

//$app->patch('/api/v1/users/{key}', 'App\Core\Action\API\APIUserAction:updateUser');
//$app->delete('/api/v1/users/{key}', 'App\Core\Action\API\APIUserAction:deleteUser');