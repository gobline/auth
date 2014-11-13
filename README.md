# Auth Component - Mendo Framework

Most oftenly, your server needs a way to identify the user making the current request.
The Mendo Auth component provides a means for representing the user making the request and 
for authenticating this user against a DB or other datasource.

* the ```CurrentUserInterface``` allows the server to **identify** the user making the current request.
* the ```AuthenticatorInterface``` allows the server to **authenticate** the user with provided credentials.

## Usage

In order to authenticate a user, you will first need to create a ```CurrentUser``` object.

```php
$user = new Mendo\Auth\CurrentUser();
```

Once we have a user, we can authenticate the latter using an adapter implementing ```AuthenticatorInterface```.
One implementation is already provided, authenticating the user against a database: ```DbAuthenticator```.

Firstly, we will have to set up the adapter. The ```DbAuthenticator``` requires a 
[PDO](http://php.net/manual/en/class.pdo.php) instance and 
an instance of ```TableMetadata``` wrapping some metadata about the DB table we are authenticating against.

```php
use Mendo\Auth\Authenticator\Db\TableMetadata;
use Mendo\Auth\Authenticator\Db\DbAuthenticator;

$authenticator = new DbAuthenticator($pdo, new TableMetadata('users'));
```

By default, ```TableMetadata``` assumes that 
* the column ```id``` contains the **id** of the user 
* the column ```email``` contains the **login** of the user 
* the column ```password``` contains the **password** of the user 
* the password encryption is **bcrypt** (possible values are: *bcrypt*, *md5*, *sha1* or *clear*)

Customize these metadata for your database.

```php
use Mendo\Auth\Authenticator\Db\TableMetadata;
use Mendo\Auth\Authenticator\Db\DbAuthenticator;

$metadata = new TableMetadata('users');
$metadata
    ->setColumnId('user_id')
    ->setColumnLogin('user_email')
    ->setColumnRole('user_type')
    ->setColumnPassword('user_password')
    ->setPasswordEncryption('md5');

$authenticator = new DbAuthenticator($pdo, $metadata);
```

*You will also notice that besides the id, login and password, 
it is possible to specify a column containing the role (or group, type) of the user.
This is especially useful when combining the Mendo Auth component with an ACL component
(which is usually the case).*

Now that we have set up our adapter, it's time to authenticate the user.

```php
$authenticator->setIdentity('mdecaffmeyer@gmail.com');
$authenticator->setCredential('123456');

$user->isAuthenticated(); // returns false

if ($authenticator->authenticate($user)) {
	echo $user->getId(); // prints the user id from DB

	$user->isAuthenticated(); // returns true
}
```

**The authenticator sets the id and the login of the user.** 
It can additionally add the user's role (for ACL) and other column data.

**An authenticated user is a user whose id is not null.**

### Persist the user's data among requests

While the above works perfectly for the first request, the user's data will be lost on subsequent requests.
One way to achieve persistence is to store the user data in session. 
```Mendo\Auth\Persistence\Session``` acts like a decorator over ```Mendo\Auth\CurrentUser``` (or any 
```Mendo\Auth\CurrentUserInterface``` implementation), allowing to persist the data in session.

```php
$user = new Mendo\Auth\CurrentUser();
$user = new Mendo\Auth\Persistence\Session($user);
```

## Installation

You can install Mendo Auth using the dependency management tool [Composer](https://getcomposer.org/).
Run the *require* command to resolve and download the dependencies:

```
composer require mendoframework/auth
```