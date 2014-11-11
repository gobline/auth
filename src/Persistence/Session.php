<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Auth\Persistence;

use Mendo\Auth\CurrentUserInterface;
use Mendo\Session\NamespacedSession;

/**
 * Allows to maintain the current user in session.
 * It acts like a decorator for a CurrentUserInterface instance.
 *
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Session implements CurrentUserInterface
{
    const SESSION_NAMESPACE = 'Mendo_Auth';

    private $user;
    private $session;

    /**
     * @param CurrentUserInterface $user
     */
    public function __construct(CurrentUserInterface $user)
    {
        $this->session = new NamespacedSession(self::SESSION_NAMESPACE);

        if ($this->session->has('id')) {
            $user->setId($this->session->get('id'));
        }
        if ($this->session->has('login')) {
            $user->setLogin($this->session->get('login'));
        }
        if ($this->session->has('role')) {
            $user->setRole($this->session->get('role'));
        }
        if ($this->session->has('properties')) {
            $user->setProperties($this->session->get('properties'));
        }

        $this->user = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthenticated()
    {
        return $this->user->isAuthenticated();
    }

    /**
     * {@inheritdoc}
     */
    public function clearIdentity()
    {
        $this->session->clearAll();
        $this->user->clearIdentity();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->user->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->user->setId($id);
        $this->session->set('id', $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getLogin()
    {
        return $this->user->getLogin();
    }

    /**
     * {@inheritdoc}
     */
    public function setLogin($login)
    {
        $this->user->setLogin($login);
        $this->session->set('login', $login);
    }

    /**
     * {@inheritdoc}
     */
    public function getRole()
    {
        return $this->user->getRole();
    }

    /**
     * {@inheritdoc}
     */
    public function setRole($role)
    {
        $this->user->setRole($role);
        $this->session->set('role', $role);
    }

    /**
     * {@inheritdoc}
     */
    public function setRoleUnauthenticated($role)
    {
        $this->user->setRoleUnauthenticated($role);
    }

    /**
     * {@inheritdoc}
     */
    public function hasProperty($name)
    {
        return $this->user->hasProperty($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty(...$args)
    {
        return $this->user->getProperty(...$args);
    }

    /**
     * {@inheritdoc}
     */
    public function addProperty($name, $value)
    {
        $this->user->addProperty($name, $value);
        $this->session->set('properties', $this->user->getProperties());
    }

    /**
     * {@inheritdoc}
     */
    public function removeProperty($name)
    {
        $this->user->removeProperty($name);
        $this->session->set('properties', $this->user->getProperties());
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties()
    {
        return $this->user->getProperties();
    }

    /**
     * {@inheritdoc}
     */
    public function setProperties(array $properties)
    {
        $this->user->setProperties($properties);
        $this->session->set('properties', $properties);
    }
}
