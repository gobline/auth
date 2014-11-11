<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Auth;

/**
 * This interface is used by the authenticators' authenticate() method
 * to set the id, login and optionally the role after
 * a successful authentication.
 *
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
interface AuthenticatableUserInterface
{
    /**
     * @param mixed $id
     */
    public function setId($id);

    /**
     * @param mixed $login
     */
    public function setLogin($login);

    /**
     * @param string $role
     */
    public function setRole($role);

    /**
     * @param array $properties
     */
    public function setProperties(array $properties);
}
