<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Auth\Authenticator\Db;

use Mendo\Auth\Authenticator\AuthenticatorInterface;
use Mendo\Auth\AuthenticatableUserInterface;
use \PDO;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class DbAuthenticator implements AuthenticatorInterface
{
    private $pdo;
    private $table;

    private $identity;
    private $credential;

    public function __construct(PDO $pdo, TableMetadata $table)
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    /**
     * @param string $identity
     *
     * @throws \InvalidArgumentException
     *
     * @return DbAuthenticator
     */
    public function setIdentity($identity)
    {
        $identity = (string) $identity;
        if ($identity === '') {
            throw new \InvalidArgumentException('$identity cannot be empty');
        }

        $this->identity = $identity;

        return $this;
    }

    /**
     * @param string $credential
     *
     * @throws \InvalidArgumentException
     *
     * @return DbAuthenticator
     */
    public function setCredential($credential)
    {
        $credential = (string) $credential;
        if ($credential === '') {
            throw new \InvalidArgumentException('$credential cannot be empty');
        }

        $this->credential = $credential;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(AuthenticatableUserInterface $user)
    {
        if ($this->identity === null) {
            throw new \UnexpectedValueException('Identity must be set (prior call to setIdentity() is necessary)');
        }
        if ($this->credential === null) {
            throw new \UnexpectedValueException('Credential must be set (prior call to setCredential() is necessary)');
        }
        $identity = $this->identity;
        $credential = $this->credential;
        $this->identity = null;
        $this->credential = null;

        $sql = 'SELECT * FROM '.$this->table->getTableName().' WHERE '.
            $this->table->getColumnLogin().' = :login';
        foreach ($this->table->getRequiredValues() as $field => $value) {
            $sql .= ' AND '.$field.' = :'.$field;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':login', $identity);
        foreach ($this->table->getRequiredValues() as $field => $value) {
            $stmt->bindValue(':'.$field, $value);
        }
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return false;
        }

        $passwordStored = $data[$this->table->getColumnPassword()];

        switch ($this->table->getPasswordEncryption()) {
            default:
                throw new \UnexpectedValueException('Password encryption "'.$this->table->getPasswordEncryption().'" invalid (possible values are "bcrypt", "md5", "sha1" or "clear")');
            case 'bcrypt':
            case 'crypt':
                if (!password_verify($credential, $passwordStored)) {
                    return false;
                }
                break;
            case 'md5':
                if ($passwordStored !== md5($credential)) {
                    return false;
                }
                break;
            case 'sha1':
                if ($passwordStored !== sha1($credential)) {
                    return false;
                }
                break;
            case 'clear':
                if ($passwordStored !== $credential) {
                    return false;
                }
                break;
        }

        $user->setId($data[$this->table->getColumnId()]);
        $user->setLogin($identity);
        if ($this->table->getColumnRole()) {
            $user->setRole($data[$this->table->getColumnRole()]);
        }
        unset($data[$this->table->getColumnPassword()]);
        $user->setProperties($data);

        return true;
    }
}
