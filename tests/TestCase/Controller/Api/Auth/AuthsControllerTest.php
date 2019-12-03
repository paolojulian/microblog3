<?php
namespace App\Test\TestCase\Controller\Api\Auth;

use App\Controller\Api\Auth\AuthsController;
use App\Test\TestCase\Controller\Api\ApiTestCase;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\ORM\TableRegistry;

/**
 * App\Controller\Api/AuthsController Test Case
 *
 * @uses \App\Controller\Api/AuthsController
 */
class AuthsControllerTest extends ApiTestCase
{
    use IntegrationTestTrait;

    public $fixtures = ['app.Users'];

    /********************
     * LOGIN
     *******************/

    public function testLoginShouldOnlyAllowPost()
    {
        $this->get('/api/auth/login');
        $this->assertResponseCode(405);
        $this->put('/api/auth/login');
        $this->assertResponseCode(405);
        $this->patch('/api/auth/login');
        $this->assertResponseCode(405);
        $this->delete('/api/auth/login');
        $this->assertResponseCode(405);
    }

    public function testUnactivatedAccountShouldReturnMessage()
    {
        $data = [
            'username' => 'unactivated',
            'password' => 'qwe123'
        ];
        $this->post('/api/auth/login', $data);
        $this->assertResponseCode(422);
        $this->assertResponseContains('Please activate your account first');
    }

    public function testLoginShouldReturnOkAndToken()
    {
        $data = [
            'username' => 'activated',
            'password' => 'qwe123'
        ];
        $this->post('/api/auth/login', $data);
        $this->assertResponseOk();
        $this->assertResponseContains('token');
    }

    // public function testLoginShouldReturnToken()
    // {
    //     $data = [
    //         'username' => 'chefclaire',
    //         'password' => 'Sadface2'
    //     ];
    //     $this->post('/api/auth/login', $data);
    //     $this->assertResponseOk();
    //     $this->assertResponseContains('token');
    // }

    public function testLoginWithEmptyFieldsShouldReturnValidationError()
    {
        $data = [
            'username' => '',
            'password' => ''
        ];
        $this->post('/api/auth/login', $data);
        $this->assertResponseCode(422);
        $this->assertResponseContains('Username or password is incorrect');
    }

    public function testLoginWithIncorrectCredentialsShouldReturnValidationError()
    {
        $data = [
            'username' => 'chefclaire',
            'password' => 'wrongPassword'
        ];
        $this->post('/api/auth/login', $data);
        $this->assertResponseCode(422);
        $this->assertResponseContains('Username or password is incorrect');
    }

    /***************************
     ******** REGISTER *********
     **************************/

    public function testLoginWithNonExistingFields()
    {
        $data = [];
        $this->post('/api/auth/register', $data);
        $this->assertResponseCode(422);
        $this->assertResponseContains('first_name');
        $this->assertResponseContains('last_name');
        $this->assertResponseContains('email');
        $this->assertResponseContains('birthdate');
        $this->assertResponseContains('username');
        $this->assertResponseContains('password');
        $this->assertResponseContains('confirm_password');
        $this->assertResponseContains('sex');
    }

    public function testLoginWithWrongSex()
    {
        $data = [
            'sex' => 'INVALID'
        ];
        $this->post('/api/auth/register', $data);
        $this->assertResponseCode(422);
        $this->assertResponseContains('sex');
        $this->assertResponseContains('Invalid Sex');
    }

    public function testLoginWithNonAlphaNumericUsername()
    {
        $data = [
            'username' => '<script>'
        ];
        $this->post('/api/auth/register', $data);
        $this->assertResponseCode(422);
        $this->assertResponseContains('username');
        $this->assertResponseContains('Alphanumeric characters only');
    }

    /**
     * [POST]
     * [INVALID]
     */
    public function testINVALIDEmptyFieldsShowErrors()
    {
        $data = [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'birthdate' => '',
            'username' => '',
            'password' => '',
            'sex' => '',
        ];
        $this->post('/api/auth/register', $data);
        $this->assertResponseCode(422);
        $this->assertResponseContains('data');
        $this->assertResponseContains('first_name');
        $this->assertResponseContains('last_name');
        $this->assertResponseContains('email');
        $this->assertResponseContains('birthdate');
        $this->assertResponseContains('username');
        $this->assertResponseContains('password');
        $this->assertResponseContains('confirm_password');
        $this->assertResponseContains('sex');
    }

    public function testIsActiveInFieldShouldThrowBadRequest()
    {
        $data = [
            'is_activated' => 1
        ];
        $this->post('/api/auth/register', $data);
        $this->assertResponseCode(400);
    }

    /**
     * [POST]
     * [INVALID]
     */
    public function testINVALIDPasswordDoesNotMatch()
    {
        $data = [
            'password' => 'password',
            'confirm_password' => 'drowssap'
        ];
        $this->post('/api/auth/register', $data);
        $this->assertResponseCode(422);
        $this->assertResponseContains('password');
        $this->assertResponseContains('confirm_password');
    }

    /**
     * [POST]
     * [INVALID]
     */
    public function testINVALIDBirthdateExceedsCurrentDate()
    {
        $data = [
            'birthdate' => '2040-07-12',
        ];
        $this->post('/api/auth/register', $data);
        $this->assertResponseCode(422);
        $this->assertResponseContains('birthdate');
        $this->assertResponseContains('Your birthday should not be greater than today');
    }

    /**
     * [POST]
     * [INVALID]
     */
    public function testINVALIDNamesDoesNotAllowNumerical()
    {
        $data = [
            'first_name' => '134',
            'last_name' => '123',
        ];
        $this->post('/api/auth/register', $data);
        $this->assertResponseCode(422);
        $this->assertResponseContains('first_name');
        $this->assertResponseContains('last_name');
        $this->assertResponseContains('Letters only');
    }

    /**
     * [POST]
     * [INVALID]
     */
    public function testINVALIDUsername6MinChar()
    {
        $data = [
            'username' => 'qq',
        ];
        $this->post('/api/auth/register', $data);
        $this->assertResponseCode(422);
        $this->assertResponseContains('username');
        $this->assertResponseContains('6 to 20 characters only');
    }

    /**
     * [POST]
     * [INVALID]
     */
    public function testINVALIDUsername20MaxChar()
    {
        $data = [
            'username' => 'qweqweqweqweqwewqewqeqwewqewqewqeqwewq',
        ];
        $this->post('/api/auth/register', $data);
        $this->assertResponseCode(422);
        $this->assertResponseContains('username');
        $this->assertResponseContains('6 to 20 characters only');
    }

    /**
     * [POST]
     * [VALID]
     */
    public function testVALIDNamesDoesAllowSpecialChar()
    {
        $data = [
            'first_name' => '日本語のキーボード',
            'last_name' => '日本語のキーボード',
        ];
        $this->post('/api/auth/register', $data);
        $this->assertResponseCode(422);
        $this->assertResponseNotContains('first_name');
        $this->assertResponseNotContains('last_name');
        $this->assertResponseNotContains('Letters only');
    }

    public function testINVALIDduplicateUsernameOrEmailShouldReturnError()
    {
        $data = [
            'email' => 'existingemail@gmail.com',
            'username' => 'existingusername',
        ];
        $this->post('/api/auth/register', $data);
        $this->assertResponseCode(422);
        $this->assertResponseContains('username');
        $this->assertResponseContains('email');
        $this->assertResponseContains('Username already exists');
        $this->assertResponseContains('Email already exists');
    }

    public function testVALIDactivateAccountShouldActivateInDB()
    {
        $key = '70b45cd46d274bd1374324a7dcc877e4ed1600aa807188001574299686';
        $this->get("/api/auth/activate/$key");
        $usersModel = TableRegistry::getTableLocator()->get('Users');
        $user = $usersModel->find()
            ->where(['activation_key' => $key])
            ->first();
        $this->assertNotEquals($user->is_activated, false);
        $this->assertRedirect('/');
    }

    public function testVALIDNoConfirmPasswordShouldShow()
    {
        $data = [
            'first_name' => 'Paolo Vincent',
            'last_name' => 'Julian',
            'email' => 'noconfirm@gmail.com',
            'birthdate' => '1994-12-07',
            'sex' => 'M',
            'username' => 'noconfirm',
            'password' => 'qwe123',
        ];
        $this->post('/api/auth/register', $data);
        $this->assertResponseCode(422);
    }

    public function testVALIDregisterUserShouldHashPassword()
    {
        $data = [
            'first_name' => 'Paolo Vincent',
            'last_name' => 'Julian',
            'email' => 'scottypipz@gmail.com',
            'birthdate' => '1994-12-07',
            'sex' => 'M',
            'username' => 'chefpipz',
            'password' => 'qwe123',
            'confirm_password' => 'qwe123',
        ];
        $this->post('/api/auth/register', $data);
        $usersModel = TableRegistry::getTableLocator()->get('Users');
        $user = $usersModel->find()
            ->where(['username' => $data['username']])
            ->first();
        $this->assertNotEquals($data['password'], $user->password);
        $this->assertResponseOk();
    }

    public function testAccessMeWithoutTokenShould401()
    {
        $this->get('/api/auth/me');
        $this->assertResponseCode(401);
    }
}
