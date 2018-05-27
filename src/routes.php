<?php

$app->get('/user/login', \E9\User\Action\Web\DisplayLoginPage::class)->setName('user-login-page');
$app->post('/user/auth', \E9\User\Action\Web\AuthenticateUser::class)->setName('user-auth');

$app->get('/user/logout', \E9\User\Action\Web\Logout::class)->setName('user-logout');

$app->get('/user/profile', \E9\User\Action\Web\DisplayProfilePage::class)->setName('user-profile');
$app->post('/user/profile', \E9\User\Action\Web\SaveUserProfile::class)->setName('user-save-profile');

$app->group('/user/register', function () {
    $this->get('/activate/{id}/{token}', \E9\User\Action\Web\ActivateUser::class)->setName('user-activate');
    $this->get('[/]', \E9\User\Action\Web\DisplayRegisterPage::class)->setName('user-register-page');
    $this->post('[/]', \E9\User\Action\Web\RegisterUser::class)->setName('user-register');
});

$app->group('/user/forgot-email', function () {
    $this->get('[/]', \E9\User\Action\Web\DisplayForgotPasswordPage::class)->setName('user-forgot-password');
    $this->post('[/]', \E9\User\Action\Web\SendResetPasswordEmail::class)->setName('user-send-reset-password');
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