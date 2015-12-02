<?php

/*
 * Gobline Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gobline\Auth\Persistence;

use Gobline\Auth\CurrentUserInterface;
use Gobline\Auth\AuthenticatableUserInterface;
use Gobline\Session\NamespacedSession;

/**
 * Allows to maintain the current user in session.
 * It acts like a decorator for a CurrentUserInterface instance.
 *
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class CurrentUser implements CurrentUserInterface, AuthenticatableUserInterface
{
    const SESSION_NAMESPACE = 'Gobline_Auth';

    private $user;
    private $session;
    private $expirationSeconds = 3600;

    /**
     * @param CurrentUserInterface $user
     */
    public function __construct(CurrentUserInterface $user)
    {
        $this->user = $user;
    }

    private function load()
    {
        $this->session = new NamespacedSession(self::SESSION_NAMESPACE);

        if ($this->session->has('id')) {
            $this->user->setId($this->session->get('id'));
        }
        if ($this->session->has('login')) {
            $this->user->setLogin($this->session->get('login'));
        }
        if ($this->session->has('role')) {
            $this->user->setRole($this->session->get('role'));
        }
        if ($this->session->has('properties')) {
            $this->user->setProperties($this->session->get('properties'));
        }

        if ($this->isAuthenticated()) {
            if ($this->isSessionExpired()) {
                $this->session->clearAll();
                $this->user->clearIdentity();
            } else {
                $_SESSION['time'] = time();
            }
        }
    }

    public function isSessionExpired()
    {
        return isset($_SESSION['time']) && time() > ($_SESSION['time'] + $this->expirationSeconds);
    }

    public function setExpirationSeconds($seconds)
    {
        $this->expirationSeconds = $seconds;
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthenticated()
    {
        if (!$this->session) {
            $this->load();
        }

        return $this->user->isAuthenticated();
    }

    /**
     * {@inheritdoc}
     */
    public function clearIdentity()
    {
        if (!$this->session) {
            $this->load();
        }

        $this->session->clearAll();
        $this->user->clearIdentity();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        if (!$this->session) {
            $this->load();
        }

        return $this->user->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        if (!$this->session) {
            $this->load();
        }

        $this->user->setId($id);
        $this->session->set('id', $id);

        $_SESSION['time'] = time();
    }

    /**
     * {@inheritdoc}
     */
    public function getLogin()
    {
        if (!$this->session) {
            $this->load();
        }

        return $this->user->getLogin();
    }

    /**
     * {@inheritdoc}
     */
    public function setLogin($login)
    {
        if (!$this->session) {
            $this->load();
        }

        $this->user->setLogin($login);
        $this->session->set('login', $login);
    }

    /**
     * {@inheritdoc}
     */
    public function getRole()
    {
        if (!$this->session) {
            $this->load();
        }

        return $this->user->getRole();
    }

    /**
     * {@inheritdoc}
     */
    public function setRole($role)
    {
        if (!$this->session) {
            $this->load();
        }

        $this->user->setRole($role);
        $this->session->set('role', $role);
    }

    /**
     * @param string $role
     *
     * @throws \InvalidArgumentException
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
        if (!$this->session) {
            $this->load();
        }

        return $this->user->hasProperty($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty(...$args)
    {
        if (!$this->session) {
            $this->load();
        }

        return $this->user->getProperty(...$args);
    }

    /**
     * {@inheritdoc}
     */
    public function addProperty($name, $value)
    {
        if (!$this->session) {
            $this->load();
        }

        $this->user->addProperty($name, $value);
        $this->session->set('properties', $this->user->getProperties());
    }

    /**
     * {@inheritdoc}
     */
    public function removeProperty($name)
    {
        if (!$this->session) {
            $this->load();
        }

        $this->user->removeProperty($name);
        $this->session->set('properties', $this->user->getProperties());
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties()
    {
        if (!$this->session) {
            $this->load();
        }

        return $this->user->getProperties();
    }

    /**
     * {@inheritdoc}
     */
    public function setProperties(array $properties)
    {
        if (!$this->session) {
            $this->load();
        }

        $this->user->setProperties($properties);
        $this->session->set('properties', $properties);
    }
}
