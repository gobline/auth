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

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class TableMetadata
{
    private $tableName;
    private $columnId = 'id';
    private $columnLogin = 'email';
    private $columnPassword = 'password';
    private $columnRole;
    private $requiredValues = [];
    private $passwordEncryption = 'bcrypt';

    /**
     * @param string $tableName
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($tableName)
    {
        $this->tableName = (string) $tableName;
        if ($this->tableName === '') {
            throw new \InvalidArgumentException('$tableName cannot be empty');
        }
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getColumnId()
    {
        return $this->columnId;
    }

    /**
     * @param string $columnId
     *
     * @throws \InvalidArgumentException
     *
     * @return TableMetadata
     */
    public function setColumnId($columnId)
    {
        $columnId = (string) $columnId;
        if ($columnId === '') {
            throw new \InvalidArgumentException('$columnId cannot be empty');
        }

        $this->columnId = $columnId;

        return $this;
    }

    /**
     * @return string
     */
    public function getColumnLogin()
    {
        return $this->columnLogin;
    }

    /**
     * @param string $columnLogin
     *
     * @throws \InvalidArgumentException
     *
     * @return TableMetadata
     */
    public function setColumnLogin($columnLogin)
    {
        $columnLogin = (string) $columnLogin;
        if ($columnLogin === '') {
            throw new \InvalidArgumentException('$columnLogin cannot be empty');
        }

        $this->columnLogin = $columnLogin;

        return $this;
    }

    /**
     * @return string
     */
    public function getColumnPassword()
    {
        return $this->columnPassword;
    }

    /**
     * @param string $columnPassword
     *
     * @throws \InvalidArgumentException
     *
     * @return TableMetadata
     */
    public function setColumnPassword($columnPassword)
    {
        $columnPassword = (string) $columnPassword;
        if ($columnPassword === '') {
            throw new \InvalidArgumentException('$columnPassword cannot be empty');
        }

        $this->columnPassword = $columnPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function getColumnRole()
    {
        return $this->columnRole;
    }

    /**
     * @param string $columnRole
     *
     * @throws \InvalidArgumentException
     *
     * @return TableMetadata
     */
    public function setColumnRole($columnRole)
    {
        $columnRole = (string) $columnRole;
        if ($columnRole === '') {
            throw new \InvalidArgumentException('$columnRole cannot be empty');
        }

        $this->columnRole = $columnRole;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequiredValues()
    {
        return $this->requiredValues;
    }

    /**
     * @param array $requiredValues
     *
     * @return TableMetadata
     */
    public function setRequiredValues(array $requiredValues)
    {
        $this->requiredValues = $requiredValues;

        return $this;
    }

    /**
     * @return string
     */
    public function getPasswordEncryption()
    {
        return $this->passwordEncryption;
    }

    /**
     * @param string $passwordEncryption
     *
     * @throws \InvalidArgumentException
     *
     * @return TableMetadata
     */
    public function setPasswordEncryption($passwordEncryption)
    {
        $passwordEncryption = (string) $passwordEncryption;
        if ($passwordEncryption === '') {
            throw new \InvalidArgumentException('$passwordEncryption cannot be empty');
        }

        $this->passwordEncryption = $passwordEncryption;

        return $this;
    }
}
