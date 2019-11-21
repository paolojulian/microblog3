<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;
use Cake\Auth\DefaultPasswordHasher;

/**
 * UsersFixture
 */
class UsersFixture extends TestFixture
{
    public $import = ['table' => 'users'];

    public function init()
    {
        $password = (new DefaultPasswordHasher)->hash('qwe123');
        $this->records = [
            [
                'id' => 200000,
                'username' => 'unactivated',
                'first_name' => 'Julian',
                'last_name' => 'Paolo Vincent',
                'email' => 'Lorem ipsum dolor sit amet',
                'birthdate' => '2010-11-20',
                'password' => $password,
                'sex' => 'M',
                'role' => 'USER',
                'avatar_url' => null,
                'is_activated' => false,
                'activation_key' => '70b45cd46d274bd1374324a7dcc877e4ed1600aa807188001574299686',
                'created' => '2019-11-20 03:47:52',
                'modified' => '2019-11-20 03:47:52',
                'deleted' => null
            ],
            [
                'id' => 200001,
                'username' => 'existingusername',
                'first_name' => 'Julian',
                'last_name' => 'Paolo Vincent',
                'email' => 'existingemail@gmail.com',
                'birthdate' => '2010-11-20',
                'password' => 'dasdsa',
                'sex' => 'M',
                'role' => 'USER',
                'avatar_url' => null,
                'is_activated' => false,
                'activation_key' => '70b45cd46d274bd1374324a7dcc877e4ed1600aa807188001574299686',
                'created' => '2019-11-20 03:47:52',
                'modified' => '2019-11-20 03:47:52',
                'deleted' => null
            ],
            [
                'id' => 200002,
                'username' => 'activated',
                'first_name' => 'Julian',
                'last_name' => 'Paolo Vincent',
                'email' => 'activated@gmail.com',
                'birthdate' => '2010-11-20',
                'password' => $password,
                'sex' => 'M',
                'role' => 'USER',
                'avatar_url' => null,
                'is_activated' => true,
                'activation_key' => '70b45cd46d274bd1374324a7dcc877e4ed1600aa807188001574299686',
                'created' => '2019-11-20 03:47:52',
                'modified' => '2019-11-20 03:47:52',
                'deleted' => null
            ],
        ];
        parent::init();
    }

}
