<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UsersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UsersTable Test Case
 */
class UsersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\UsersTable
     */
    public $Users;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Users') ? [] : ['className' => UsersTable::class];
        $this->Users = TableRegistry::getTableLocator()->get('Users', $config);
    }

    public function testSearchingWithSQLCommandWillReturnEmpty()
    {
        $text = 'DELETE * FROM users';
        $page = 1;
        $result = $this->Users->searchUser($text, $page);
        $this->assertEquals(isset($result['list']), true);
        $this->assertEquals($result['list'], []);
    }

    public function testSearchingWithSpecialCharsWillReturnEmpty()
    {
        $text = '(*@#&!(*#&@!\\';
        $page = 1;
        $result = $this->Users->searchUser($text, $page);
        $this->assertEquals(isset($result['list']), true);
        $this->assertEquals($result['list'], []);
    }

    public function testSearchValidNameWillNotIncludeUnactivatedUsers()
    {
        $text = 'activated';
        $page = 1;
        $result = $this->Users->searchUser($text, $page);
        $this->assertEquals(isset($result['list']), true);
        $expected = [
            [
                'id' => 200002,
                'username' => 'activated',
                'first_name' => 'Julian',
                'last_name' => 'Paolo Vincent',
                'avatar_url' => null,
            ],
            [
                'id' => 200012,
                'username' => 'anotherActivated',
                'first_name' => 'Julian',
                'last_name' => 'Paolo Vincent',
                'avatar_url' => null,
            ],
        ];
        $this->assertEquals($result['list'], $expected);
    }

    public function testSearchValidNameWillReturnTotalCount()
    {
        $text = 'activated';
        $page = 1;
        $result = $this->Users->searchUser($text, $page);
        $this->assertEquals(isset($result['totalCount']), true);
        $this->assertEquals($result['totalCount'], 2);
    }

    public function testUpdateUserWillReturnEntity()
    {
        $data = [];
        $userId = 200002;
        $user = $this->Users->updateUser($userId, $data);
        $this->assertInstanceOf('\Cake\ORM\Entity', $user);
    }

    public function testUpdateUserWithNonExistingDataWillHaveEntityError()
    {
        $data = [];
        $userId = 200002;
        $user = $this->Users->updateUser($userId, $data);
        $this->assertInstanceOf('\Cake\ORM\Entity', $user);
        $this->assertEquals($user->hasErrors(), true);
    }

    public function testUpdateUserWithEmptyFieldsWillReturnError()
    {
        $data = [
            'username' => '',
            'first_name' => '',
            'last_name' => '',
            'birthdate' => '',
        ];
        $userId = 200002;
        $user = $this->Users->updateUser($userId, $data);
        $this->assertInstanceOf('\Cake\ORM\Entity', $user);
        $this->assertEquals($user->hasErrors(), true);
        $errors = $user->errors();
        $this->assertEquals(isset($errors['username']), true);
        $this->assertEquals(isset($errors['first_name']), true);
        $this->assertEquals(isset($errors['last_name']), true);
        $this->assertEquals(isset($errors['birthdate']), true);
    }

    public function testUpdateUsernameWithLessCharWIllReturnError()
    {
        $data = [
            'username' => 'ere',
        ];
        $userId = 200002;
        $user = $this->Users->updateUser($userId, $data);
        $this->assertInstanceOf('\Cake\ORM\Entity', $user);
        $this->assertEquals($user->hasErrors(), true);
        $errors = $user->errors();
        $this->assertEquals(isset($errors['username']), true);
    }

    public function testUpdateUsernameWithMoreCharWillReturnError()
    {
        $data = [
            'username' => 'wqeqweqwewqeqwewqeqweqwewqeqweqqwqeqweqwewqeqweq',
        ];
        $userId = 200002;
        $user = $this->Users->updateUser($userId, $data);
        $this->assertInstanceOf('\Cake\ORM\Entity', $user);
        $this->assertEquals($user->hasErrors(), true);
        $errors = $user->errors();
        $this->assertEquals(isset($errors['username']), true);
    }

    public function testUpdatingUserWithNonUniqueWillReturnError()
    {
        $data = [
            'username' => 'unactivated',
        ];
        $userId = 200002;
        $user = $this->Users->updateUser($userId, $data);
        $this->assertInstanceOf('\Cake\ORM\Entity', $user);
        $this->assertEquals($user->hasErrors(), true);
        $errors = $user->errors();
        $this->assertEquals(isset($errors['username']), true);
    }

    public function testUpdateUserWithMaxCharsWillREturnError()
    {
        $data = [
            'first_name' => 'dkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjfffffffffdkjsalkfjklasdjfdkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjfffffffffdkjsalkfjklasdjf',
            'last_name' => 'dkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjdkjsalkfjklasdjfffffffffdkjsalkfjklasdjf',
        ];
        $userId = 200002;
        $user = $this->Users->updateUser($userId, $data);
        $this->assertInstanceOf('\Cake\ORM\Entity', $user);
        $this->assertEquals($user->hasErrors(), true);
        $errors = $user->errors();
        $this->assertEquals(isset($errors['first_name']), true);
        $this->assertEquals(isset($errors['last_name']), true);
    }

    public function testUpdateUserWillReturnEntityNoErrors()
    {
        $data = [
            'username' => 'newUsernameha',
            'first_name' => 'Paolo Vincent',
            'last_name' => 'Julian',
            'birthdate' => '1994-07-12'
        ];
        $userId = 200002;
        $user = $this->Users->updateUser($userId, $data);
        $this->assertInstanceOf('\Cake\ORM\Entity', $user);
        $this->assertEquals($user->hasErrors(), false);
    }

    public function testUpdateUserWithWrongOldPasswordWillReturnError()
    {
        $data = [
            'old_password' => 'fldjaklfjaklsdj',
        ];
        $userId = 200002;
        $user = $this->Users->updateUser($userId, $data);
        $this->assertInstanceOf('\Cake\ORM\Entity', $user);
        $this->assertEquals($user->hasErrors(), true);
        $errors = $user->errors();
        $this->assertEquals(isset($errors['old_password']), true);
    }

    public function testUpdateUserWithOldPasswordWillRequirePassword()
    {
        $data = [
            'old_password' => 'fldjaklfjaklsdj',
        ];
        $userId = 200002;
        $user = $this->Users->updateUser($userId, $data);
        $this->assertInstanceOf('\Cake\ORM\Entity', $user);
        $this->assertEquals($user->hasErrors(), true);
        $errors = $user->errors();
        $this->assertEquals(isset($errors['old_password']), true);
        $this->assertEquals(isset($errors['password']), true);
        $this->assertEquals(isset($errors['confirm_password']), true);
    }

    public function testMismatchNewPasswordWillReturnError()
    {
        $data = [
            'old_password' => 'qwe123',
            'password' => 'hahahaha',
            'confirm_password' => 'qhawe123',
        ];
        $userId = 200002;
        $user = $this->Users->updateUser($userId, $data);
        $this->assertInstanceOf('\Cake\ORM\Entity', $user);
        $this->assertEquals($user->hasErrors(), true);
        $errors = $user->errors();
        $this->assertEquals(isset($errors['confirm_password']), true);
    }
}
