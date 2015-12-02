<?php

/*
 * Gobline Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gobline\Auth;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class CurrentUser implements CurrentUserInterface, AuthenticatableUserInterface
{
    private $id;
    private $login;
    private $role;
    private $roleUnauthenticated; // role of unauthenticated user
    private $properties = [];

    /**
     * {@inheritdoc}
     */
    public function isAuthenticated()
    {
        return $this->id !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function clearIdentity()
    {
        $this->id = null;
        $this->login = null;
        $this->role = null;
        $this->properties = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        if ($this->id !== null) {
            throw new \RuntimeException('Attempt to alter id from "'.$this->id.'" to "'.$id.'" (can\'t change id once set)');
        }

        if ((string) $id === '') {
            throw new \InvalidArgumentException('$id cannot be empty');
        }

        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogin($login)
    {
        if ((string) $login === '') {
            throw new \InvalidArgumentException('$login cannot be empty');
        }

        $this->login = $login;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole()
    {
        if (!$this->isAuthenticated() && $this->role === null) {
            return $this->roleUnauthenticated;
        }

        return $this->role;
    }

    /**
     * {@inheritdoc}
     */
    public function setRole($role)
    {
        $role = (string) $role;
        if ($role === '') {
            throw new \InvalidArgumentException('$role cannot be empty');
        }

        $this->role = $role;
    }

    /**
     * @param string $role
     *
     * @throws \InvalidArgumentException
     */
    public function setRoleUnauthenticated($role)
    {
        $role = (string) $role;
        if ($role === '') {
            throw new \InvalidArgumentException('$role cannot be empty');
        }

        $this->roleUnauthenticated = $role;
    }

    /**
     * {@inheritdoc}
     */
    public function hasProperty($name)
    {
        if ((string) $name === '') {
            throw new \InvalidArgumentException('$name cannot be empty');
        }

        return array_key_exists($name, $this->properties);
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty(...$args)
    {
        switch (count($args)) {
            default:
                throw new \InvalidArgumentException('getProperty() takes one or two arguments');
            case 1:
                if (!$this->hasProperty($args[0])) {
                    throw new \InvalidArgumentException('Property "'.$args[0].'" not found');
                }

                return $this->properties[$args[0]];
            case 2:
                if (!$this->hasProperty($args[0])) {
                    return $args[1];
                }

                return $this->properties[$args[0]];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addProperty($name, $value)
    {
        if ((string) $name === '') {
            throw new \InvalidArgumentException('$name cannot be empty');
        }

        $this->properties[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function removeProperty($name)
    {
        unset($this->properties[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * {@inheritdoc}
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;
    }
}
