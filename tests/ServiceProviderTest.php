<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Mendo\Auth\Provider\Pimple\AuthServiceProvider;
use Mendo\Auth\Provider\Pimple\DbAuthenticatorServiceProvider;
use Pimple\Container;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class ServiceProviderTest extends PHPUnit_Framework_TestCase
{
    public function testAuthProvider()
    {
        $container = new Container();

        $container->register(new AuthServiceProvider());

        $this->assertInstanceOf('Mendo\Auth\CurrentUserInterface', $container['auth']);
    }

    public function testDbAuthenticatorProvider()
    {
        $dsn = 'sqlite:'.__DIR__.'./resources/db.sqlite';
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];
        $pdo = new PDO($dsn, null, null, $options);

        $container = new Container();

        $container['pdo'] = $pdo;

        $container->register(new DbAuthenticatorServiceProvider('dbAuthenticator'));
        $container['dbAuthenticator.pdo'] = 'pdo';
        $container['dbAuthenticator.table'] = 'test';

        $this->assertInstanceOf('Mendo\Auth\Authenticator\AuthenticatorInterface', $container['dbAuthenticator']);
    }
}
