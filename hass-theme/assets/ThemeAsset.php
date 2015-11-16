<?php
/**
 *
* HassCMS (http://www.hassium.org/)
*
* @link http://github.com/hasscms for the canonical source repository
* @copyright Copyright (c) 2014-2099 Hassium Software LLC.
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/
namespace hass\theme\assets;

/**
* @package hass\admin
* @author zhepama <zhepama@gmail.com>
* @since 0.1.0
 */
class ThemeAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@hass/theme/media';
    public $css = [
        'theme.css',
    ];
    public $depends = [
        '\hass\admin\assets\AdminAsset'
    ];
}
