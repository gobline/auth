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
use Mendo\Auth\CurrentUser;
use Mendo\Auth\Persistence\Session;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class AuthServiceProvider implements ServiceProviderInterface
{
    private $reference;

    public function __construct($reference = 'auth')
    {
        $this->reference = $reference;
    }

    public function register(Container $container)
    {
        $container[$this->reference.'.roleUnauthenticated'] = 'unauthenticated';
        $container[$this->reference.'.session'] = true;

        $container[$this->reference] = function ($c) {
            $user = new CurrentUser();
            $user->setRoleUnauthenticated($c[$this->reference.'.roleUnauthenticated']);

            if ($c[$this->reference.'.session']) {
                $user = new Session($user);
            }

            return $user;
        };
    }
}
