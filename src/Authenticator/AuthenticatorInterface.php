<?php

/*
 * Gobline Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gobline\Auth\Authenticator;

use Gobline\Auth\AuthenticatableUserInterface;

/**
 * Authenticates a user.
 *
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
interface AuthenticatorInterface
{
    /**
     * Authenticates the user by setting the user id, login and optionally the role
     * after a successful authentication.
     *
     * @param AuthenticatableUserInterface $user
     *
     * @throws \UnexpectedValueException
     *
     * @return bool
     */
    public function authenticate(AuthenticatableUserInterface $user);
}
