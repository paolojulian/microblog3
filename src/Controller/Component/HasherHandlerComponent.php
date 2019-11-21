<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Utility\Security;

/**
 * HasherHandler component
 */
class HasherHandlerComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Generates a random hash
     * 
     * @return string
     */
    public function generateRand($key = 'J0hNM4Y3R')
    {
        $timeStr = str_replace("0.", "", microtime());
        $timeStr = str_replace(" ", "", $timeStr);
        return Security::hash($key).$timeStr;
    }
}
