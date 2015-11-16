<?php
/**
 *
* HassCMS (http://www.hassium.org/)
*
* @link http://github.com/hasscms for the canonical source repository
* @copyright Copyright (c) 2014-2099 Hassium Software LLC.
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/
namespace hass\helpers;

/**
* @package hass\helpers
* @author zhepama <zhepama@gmail.com>
* @since 0.1.0
 */
class HookEvent extends  \League\Event\Event
{

    /**
     *
     * @var \hass\helpers\Parameters
     */
    public $parameters;


    public function __construct($name)
    {
        parent::__construct($name);
        $this->parameters = new \hass\helpers\Parameters();
    }

}
