<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

/**
 * UserHandler component
 */
class UserHandlerComponent extends Component
{
    public $components = ['MailHandler'];
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Handles the sending of mail for account activation
     * Assumes everything in data is already validated
     * 
     * @param array $data - User Object
     */
    public function sendActivationMail(array $data, string $serverName)
    {
        $this->MailHandler->sendActivationMail(
            $data['email'],
            [
                'fullName' => $data['first_name'] . ' ' . $data['last_name'],
                'activationUrl' => $serverName . '/' . $data['activation_key']
            ]
        );
        return true;
    }
}
