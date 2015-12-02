<?php

/*
 * Gobline Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gobline\Auth\Provider\Gobline;

use Gobline\Container\ContainerInterface;
use Gobline\Container\ServiceConfiguratorInterface;
use Gobline\Auth\Persistence\CurrentUser;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class CurrentUserConfigurator implements ServiceConfiguratorInterface
{
    public function configure($user, array $config, ContainerInterface $container)
    {
        $persistence = isset($config['persistence']) ? $config['persistence'] : null;
        $roleUnauthenticated = isset($config['roleUnauthenticated']) ? $config['roleUnauthenticated'] : null;

        if ($roleUnauthenticated) {
            $user->setRoleUnauthenticated($roleUnauthenticated);
        }

        if (!$persistence) {
            return $user;
        }

        if ($persistence === 'session') {
            return new CurrentUser($user);
        } 

        throw new \RuntimeException('$persistence "'.$persistence.'" unknown');
    }
}
