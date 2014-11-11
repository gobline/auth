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
 * Represents the user making the requests.
 * It allows the server to identify the requester.
 *
 * An authenticated user is a user who has an id different than null.
 *
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
interface CurrentUserInterface extends AuthenticatableUserInterface
{
    /**
     * @return bool
     */
    public function isAuthenticated();

    /**
     *
     */
    public function clearIdentity();

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return mixed
     */
    public function getLogin();

    /**
     * @return string
     */
    public function getRole();

    /**
     * @param string $role
     *
     * @throws \InvalidArgumentException
     */
    public function setRoleUnauthenticated($role);

    /**
     * @param mixed $name
     *
     * @return bool
     */
    public function hasProperty($name);

    /**
     * This method takes one or two arguments.
     * The first argument is the session variable you want to get.
     * The second optional argument is the default value you want to get back
     * in case the session variable hasn't been found.
     * If the second argument is omitted and the variable hasn't been found,
     * an exception will be thrown.
     *
     * @param mixed $args
     *
     * @return mixed
     */
    public function getProperty(...$args);

    /**
     * @param mixed $name
     * @param mixed $value
     *
     * @throws \InvalidArgumentException
     */
    public function addProperty($name, $value);

    /**
     * @param mixed $name
     */
    public function removeProperty($name);

    /**
     * @return array
     */
    public function getProperties();
}
