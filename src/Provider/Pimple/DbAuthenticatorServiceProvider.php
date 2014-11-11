<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Auth\Provider\Pimple;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Mendo\Auth\Authenticator\Db\DbAuthenticator;
use Mendo\Auth\Authenticator\Db\TableMetadata;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class DbAuthenticatorServiceProvider implements ServiceProviderInterface
{
    private $reference;

    public function __construct($reference = 'authenticator.db')
    {
        $this->reference = $reference;
    }

    public function register(Container $container)
    {
        $reference = $this->reference;

        $container[$this->reference.'.column.id'] = 'id';
        $container[$this->reference.'.column.login'] = 'email';
        $container[$this->reference.'.column.password'] = 'password';
        $container[$this->reference.'.column.password.encryption'] = 'bcrypt';
        $container[$this->reference.'.column.role'] = null;
        $container[$this->reference.'.requiredValues'] = [];

        $container[$this->reference] = function ($c) {
            if (empty($c[$this->reference.'.pdo'])) {
                throw new \Exception('Db dependency not specified');
            }
            if (empty($c[$c[$this->reference.'.pdo']])) {
                throw new \Exception('Dependency "'.$this->reference.'.pdo" not found');
            }
            if (empty($c[$this->reference.'.table'])) {
                throw new \Exception('Table name not specified');
            }

            $metadata = new TableMetadata($c[$this->reference.'.table']);
            $metadata
                ->setColumnId($c[$this->reference.'.column.id'])
                ->setColumnLogin($c[$this->reference.'.column.login'])
                ->setColumnPassword($c[$this->reference.'.column.password'])
                ->setPasswordEncryption($c[$this->reference.'.column.password.encryption'])
                ->setRequiredValues($c[$this->reference.'.requiredValues']);
            if (!empty($c[$this->reference.'.column.role'])) {
                $metadata->setColumnRole($c[$this->reference.'.column.role']);
            }

            return new DbAuthenticator($c[$c[$this->reference.'.pdo']], $metadata);
        };
    }
}
