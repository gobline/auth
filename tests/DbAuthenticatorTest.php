<?php

/*
 * Gobline Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Gobline\Auth\CurrentUser;
use Gobline\Auth\Authenticator\Db\DbAuthenticator;
use Gobline\Auth\Authenticator\Db\TableMetadata;
use \PDO;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class DbAuthenticatorTest extends PHPUnit_Framework_TestCase
{
    private $pdo;

    public function setUp()
    {
        $dsn = 'sqlite:'.__DIR__.'./resources/db.sqlite';
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];
        $this->pdo = new PDO($dsn, null, null, $options);

        $sql = 'DROP TABLE IF EXISTS users';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $sql =
            'CREATE TABLE IF NOT EXISTS users
            (
                id INTEGER NOT null PRIMARY KEY AUTOINCREMENT,
                first_name VARCHAR(40) NOT null,
                last_name VARCHAR(40) NOT null,
                email VARCHAR(255) NOT null,
                password VARCHAR(60) NOT null,
                nb_login INTEGER DEFAULT 0,
                UNIQUE (id)
            );';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $this->insert($this->pdo, 'users',
            [
                'first_name' => 'Mathieu',
                'last_name' => 'Decaffmeyer',
                'email' => 'mdecaffmeyer@gmail.com',
                'password' => '123456',
            ]
        );

        $this->insert($this->pdo, 'users',
            [
                'first_name' => 'Foo',
                'last_name' => 'Bar',
                'email' => 'foo.bar@example.com',
                'password' => 'qwerty',
            ]
        );
    }

    public function testDbAuthenticator()
    {
        $currentUser = new CurrentUser();

        $metadata = new TableMetadata('users');
        $metadata
            ->setColumnId('id')
            ->setColumnLogin('email')
            ->setColumnPassword('password')
            ->setPasswordEncryption('clear');

        $authenticator = new DbAuthenticator($this->pdo, $metadata);

        $authenticator->setIdentity('mdecaffmeyer@gmail.com');
        $authenticator->setCredential('123456');

        $this->assertTrue($authenticator->authenticate($currentUser));

        $this->assertTrue($currentUser->isAuthenticated());
        $this->assertEquals(1, $currentUser->getId());
        $this->assertSame('mdecaffmeyer@gmail.com', $currentUser->getLogin());

        $authenticator->setIdentity('mdecaffmeyer@gmail.com');
        $authenticator->setCredential('654321');
        $this->assertFalse($authenticator->authenticate($currentUser));
    }

    private function insert(PDO $pdo, $tableName, array $arrNameValuePairs)
    {
        $sql = 'INSERT INTO '.$tableName.'(';
        $prefix = '';
        foreach ($arrNameValuePairs as $key => $value) {
            $sql .= $prefix.$key;
            $prefix = ', ';
        }
        $sql .= ') VALUES (';
        $prefix = ':';
        foreach ($arrNameValuePairs as $key => $value) {
            $sql .= $prefix.$key;
            $prefix = ', :';
        }
        $sql .= ')';
        $stmt = $pdo->prepare($sql);
        foreach ($arrNameValuePairs as $param => $value) {
            $stmt->bindValue(':'.$param, $value);
        }
        $stmt->execute();
    }
}
