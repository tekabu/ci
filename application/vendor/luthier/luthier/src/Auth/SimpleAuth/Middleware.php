<?php

/*
 * Luthier CI
 *
 * (c) 2018 Ingenia Software C.A
 *
 * This file is part of Luthier CI, a plugin for CodeIgniter 3. See the LICENSE
 * file for copyright information and license details
 */

namespace Luthier\Auth\SimpleAuth;

use Luthier\Auth\Middleware as AuthMiddleware;
use Luthier\Auth\UserInterface;
use Luthier\Auth\SimpleAuth\Middleware\RememberMeMiddleware;
use Luthier\Route;

/**
 * SimpleAuth implementation of the Controller-based authentication
 * 
 * @author Anderson Salas <anderson@ingenia.me>
 */
class Middleware extends AuthMiddleware
{
    /**
     * {@inheritDoc}
     * 
     * @see \Luthier\Auth\Middleware::preLogin()
     */
    public function preLogin(Route $route)
    {
        if(
            $route->getName()     == config_item('auth_login_route') &&
            $route->requestMethod == 'POST' &&
            config_item('simpleauth_enable_brute_force_protection') === true
        )
        {
            ci()->load->database();

            $loginAttemptCount = ci()->db->where('ip', $_SERVER['REMOTE_ADDR'])
                ->where('created_at >=', date('Y-m-d H:i:s', time() - (60 * 30)) ) // 30 minutes
                ->where('created_at <=', date('Y-m-d H:i:s', time()))
                ->count_all_results(config_item('simpleauth_login_attempts_table'));

            if($loginAttemptCount >= 4)
            {
                ci()->session->set_flashdata('_auth_messages', [ 'danger' =>  'ERR_LOGIN_ATTEMPT_BLOCKED' ]);

                return redirect(route(config_item('auth_login_route')));
            }
        }
    }

    /**
     * {@inheritDoc}
     * 
     * @see \Luthier\Auth\Middleware::onLoginSuccess()
     */
    public function onLoginSuccess(UserInterface $user)
    {
        if( config_item('simpleauth_enable_remember_me') === true )
        {
            ci()->middleware->run( new RememberMeMiddleware(), 'store');
        }

        return redirect(
            route_exists(config_item('auth_login_route_redirect'))
                ? route(config_item('auth_login_route_redirect'))
                : base_url()
        );
    }

    /**
     * {@inheritDoc}
     * 
     * @see \Luthier\Auth\Middleware::onLoginFailed()
     */
    public function onLoginFailed($username)
    {
        ci()->load->database();

        if( config_item('simpleauth_enable_brute_force_protection') === true )
        {
            ci()->db->insert(
                config_item('simpleauth_login_attempts_table'),
                [
                    'username'   => $username,
                    'ip'         => $_SERVER['REMOTE_ADDR']
                ]
            );
        }
    }

    /**
     * {@inheritDoc}
     * 
     * @see \Luthier\Auth\Middleware::onLoginInactiveUser()
     */
    public function onLoginInactiveUser(UserInterface $user)
    {
        return;
    }

    /**
     * {@inheritDoc}
     * 
     * @see \Luthier\Auth\Middleware::onLoginUnverifiedUser()
     */
    public function onLoginUnverifiedUser(UserInterface $user)
    {
        return;
    }

    /**
     * {@inheritDoc}
     * 
     * @see \Luthier\Auth\Middleware::onLogout()
     */
    public function onLogout()
    {
        ci()->middleware->run( new RememberMeMiddleware(), 'destroy');
    }
}