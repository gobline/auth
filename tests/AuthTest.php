<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Mendo\Auth\Authenticator\AuthenticatorInterface;
use Mendo\Auth\CurrentUser;
use Mendo\Auth\AuthenticatableUserInterface;
use Mendo\Auth\Persistence\Session;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class AuthTest extends PHPUnit_Framework_TestCase
{
    private $authenticator;

    public function setUp()
    {
        $this->authenticator = new SimplePhpArrayAuthenticator(
            [
                'user1' => 'password1',
                'user2' => 'password2',
                'user3' => 'password3',
            ]
        );
    }

    public function testUnauthenticatedCurrentUser()
    {
        $currentUser = new CurrentUser();

        $this->assertFalse($currentUser->isAuthenticated());

        $this->assertNull($currentUser->getId());
        $this->assertNull($currentUser->getLogin());
        $this->assertSame([], $currentUser->getProperties());
        $this->assertNull($currentUser->getRole());
    }

    public function testAuthenticationSuccess()
    {
        $currentUser = new CurrentUser();

        $this->authenticator->setIdentity('user1');
        $this->authenticator->setCredential('password1');

        $this->assertTrue($this->authenticator->authenticate($currentUser));

        $this->assertTrue($currentUser->isAuthenticated());
        $this->assertSame('user1', $currentUser->getId());
        $this->assertSame('user1', $currentUser->getLogin());
    }

    public function testAuthenticationFail()
    {
        $currentUser = new CurrentUser();

        $this->authenticator->setIdentity('user1');
        $this->authenticator->setCredential('password2');

        $this->assertFalse($this->authenticator->authenticate($currentUser));

        $this->assertFalse($currentUser->isAuthenticated());
        $this->assertNull($currentUser->getId());
        $this->assertNull($currentUser->getLogin());
    }

    public function testClearIdentity()
    {
        $currentUser = new CurrentUser();

        $this->authenticator->setIdentity('user1');
        $this->authenticator->setCredential('password1');

        $this->authenticator->authenticate($currentUser);

        $this->assertTrue($currentUser->isAuthenticated());
        $this->assertSame('user1', $currentUser->getId());
        $this->assertSame('user1', $currentUser->getLogin());

        $currentUser->clearIdentity();

        $this->assertFalse($currentUser->isAuthenticated());
        $this->assertNull($currentUser->getId());
        $this->assertNull($currentUser->getLogin());
    }

    public function testCurrentUserProperties()
    {
        $currentUser = new CurrentUser();

        $currentUser->addProperty('foo', 'bar');

        $this->assertSame('bar', $currentUser->getProperty('foo'));

        $currentUser->removeProperty('foo');

        $this->assertFalse($currentUser->hasProperty('foo'));

        $this->assertSame('grault', $currentUser->getProperty('corge', 'grault'));

        $this->setExpectedException('\InvalidArgumentException', 'not found');
        $currentUser->getProperty('corge');
    }

    public function testCurrentUserRoleUnauthenticated()
    {
        $currentUser = new CurrentUser();

        $this->assertNull($currentUser->getRole());

        $currentUser->setRoleUnauthenticated('unauthenticated');

        $this->assertSame('unauthenticated', $currentUser->getRole());
    }

    public function testSessionDecorator()
    {
        @session_destroy();
        @session_start();

        $currentUser = new Session(new CurrentUser());

        $this->authenticator->setIdentity('user1');
        $this->authenticator->setCredential('password1');

        $this->assertTrue($this->authenticator->authenticate($currentUser));

        $this->assertTrue($currentUser->isAuthenticated());
        $this->assertSame('user1', $currentUser->getId());
        $this->assertSame('user1', $currentUser->getLogin());
    }
}

class SimplePhpArrayAuthenticator implements AuthenticatorInterface
{
    private $array;
    private $identity = null;
    private $credential = null;

    public function __construct(array $data)
    {
        $this->array = $data;
    }

    public function setIdentity($identity)
    {
        $this->identity = $identity;
    }

    public function setCredential($credential)
    {
        $this->credential = $credential;
    }

    public function authenticate(AuthenticatableUserInterface $user)
    {
        if ($this->identity === null) {
            throw new \UnexpectedValueException('Identity must be set (prior call to setIdentity() is necessary)');
        }
        if ($this->credential === null) {
            throw new \UnexpectedValueException('Credential must be set (prior call to setCredential() is necessary)');
        }

        if (!array_key_exists($this->identity, $this->array)) {
            return false;
        }

        if ($this->credential !== $this->array[$this->identity]) {
            return false;
        }

        $user->setId($this->identity);
        $user->setLogin($this->identity);

        return true;
    }
}
