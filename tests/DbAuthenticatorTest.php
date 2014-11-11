<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Mendo\Auth\CurrentUser;
use Mendo\Auth\Authenticator\Db\DbAuthenticator;
use Mendo\Auth\Authenticator\Db\TableMetadata;
use \PDO;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class DbAuthenticatorTest extends PHPUnit_Framework_TestCase
{
    private $authenticator;

    public function setUp()
    {
        $dsn = 'sqlite:'.__DIR__.'./resources/db.sqlite';
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];
        $pdo = new PDO($dsn, null, null, $options);

        $sql = 'DROP TABLE IF EXISTS users';
        $stmt = $pdo->prepare($sql);
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
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $this->insertDb($pdo, 'users',
            [
                'first_name' => 'Mathieu',
                'last_name' => 'Decaffmeyer',
                'email' => 'mdecaffmeyer@gmail.com',
                'password' => '123456',
            ]
        );

        $this->insertDb($pdo, 'users',
            [
                'first_name' => 'Foo',
                'last_name' => 'Bar',
                'email' => 'foo.bar@example.com',
                'password' => 'qwerty',
            ]
        );

        $metadata = new TableMetadata('users');
        $metadata
            ->setColumnId('id')
            ->setColumnLogin('email')
            ->setColumnPassword('password')
            ->setPasswordEncryption('clear');

        $this->authenticator = new DbAuthenticator($pdo, $metadata);
    }

    public function testDbAuthenticator()
    {
        $currentUser = new CurrentUser();

        $this->authenticator->setIdentity('mdecaffmeyer@gmail.com');
        $this->authenticator->setCredential('123456');

        $this->assertTrue($this->authenticator->authenticate($currentUser));

        $this->assertTrue($currentUser->isAuthenticated());
        $this->assertEquals(1, $currentUser->getId());
        $this->assertSame('mdecaffmeyer@gmail.com', $currentUser->getLogin());

        $this->authenticator->setIdentity('mdecaffmeyer@gmail.com');
        $this->authenticator->setCredential('654321');
        $this->assertFalse($this->authenticator->authenticate($currentUser));
    }

    private function insertDb($db, $tableName, array $arrNameValuePairs)
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
        $stmt = $db->prepare($sql);
        foreach ($arrNameValuePairs as $param => $value) {
            $stmt->bindValue(':'.$param, $value);
        }
        $stmt->execute();
    }
}
