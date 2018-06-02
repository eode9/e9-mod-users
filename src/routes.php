<?php

/** Web routes */
$app->get('/user/login', \E9\User\Action\Web\DisplayLoginPage::class)->setName('user-login-page');
$app->post('/user/auth', \E9\User\Action\Web\AuthenticateUser::class)->setName('user-auth');

$app->get('/user/logout', \E9\User\Action\Web\Logout::class)->setName('user-logout');

$app->get('/user/profile', \E9\User\Action\Web\DisplayProfilePage::class)->setName('user-profile');
$app->post('/user/profile', \E9\User\Action\Web\SaveUserProfile::class)->setName('user-save-profile');

$app->get('/user/settings', \E9\User\Action\Web\DisplaySettingsPage::class)->setName('user-settings');
$app->post('/user/settings', \E9\User\Action\Web\SaveUserSettings::class)->setName('user-save-settings');

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

/** API routes */
$app->options('/api/v1/users/auth', \E9\User\Action\API\AuthenticateUser::class);
$app->post('/api/v1/users/auth', \E9\User\Action\API\AuthenticateUser::class);

$app->options('/api/v1/users/register', \E9\User\Action\API\RegisterUser::class);
$app->post('/api/v1/users/register', \E9\User\Action\API\RegisterUser::class);

$app->options('/api/v1/users/send-reset-password-email', \E9\User\Action\API\SendResetPasswordEmail::class);
$app->post('/api/v1/users/send-reset-password-email', \E9\User\Action\API\SendResetPasswordEmail::class);

$app->options('/api/v1/new-password', \E9\User\Action\API\SetNewPassword::class);
$app->get('/api/v1/new-password', \E9\User\Action\API\SetNewPassword::class);

/**
 * API User routes
 */
$app->options('/api/v1/users', \E9\User\Action\API\GetUsers::class);
$app->post('/api/v1/users', \E9\User\Action\API\CreateUser::class);
$app->get('/api/v1/users', \E9\User\Action\API\GetUsers::class);

$app->options('/api/v1/users/{id}', \E9\User\Action\API\GetUser::class);
$app->get('/api/v1/users/{id}', \E9\User\Action\API\GetUser::class);
$app->patch('/api/v1/users/{id}', \E9\User\Action\API\UpdateUser::class);
$app->delete('/api/v1/users/{id}', \E9\User\Action\API\DeleteUser::class);
