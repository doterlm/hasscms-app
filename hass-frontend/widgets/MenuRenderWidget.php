<?php

/**
 * HassCMS (http://www.hassium.org/)
 *
 * @link http://github.com/hasscms for the canonical source repository
 * @copyright Copyright (c) 2016-2099 Hassium Software LLC.
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
namespace hass\frontend\widgets;

use yii\base\InvalidConfigException;
use hass\frontend\models\Menu;
use hass\helpers\Hook;

use hass\helpers\NestedSetsTree;
use Yii;
use hass\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use hass\helpers\Serializer;

/**
 *
 * @package hass\package_name
 * @author zhepama <zhepama@gmail.com>
 * @since 0.1.0
 *       
 */
class MenuRenderWidget extends \yii\widgets\Menu
{
    public $currentAbsoluteUrl;

    public $activateParents = true;

    public $slug;
    
    public $showParentUrl = false;

    public function init()
    {
        parent::init();
        
        if ($this->slug == null) {
            throw new InvalidConfigException("slug不能为空");
        }
        
        $this->currentAbsoluteUrl = \Yii::$app->getRequest()->getAbsoluteUrl();
        
        $collection = Menu::findBySlug($this->slug);
        $createCallbacks = Hook::trigger(\hass\menu\Module::EVENT_MENU_LINK_CREATE)->parameters;
        
        $this->items = NestedSetsTree::generateTree($collection, function ($item) use($createCallbacks) {
            list ($item['label'], $item["url"]) = call_user_func($createCallbacks[$item['module']], $item['name'], $item['original']);
            $item["options"] = Serializer::unserializeToArray($item["options"]);
            return $item;
        }, 'items');
    }

    /**
     * 这里添加一个判断标准..即当有子的时候...是否显示该url
     * @param array $item the menu item to be rendered. Please refer to [[items]] to see what data might be in the item.
     * @return string the rendering result
     */
    protected function renderItem($item)
    {
        if (!isset($item['url']) ||($this->showParentUrl == false &&isset($item["items"])&& count($item["items"])>0)) {
            $template = ArrayHelper::getValue($item, 'template', $this->labelTemplate);

            return strtr($template, [
                '{label}' => $item['label'],
            ]);
        }

        $template = ArrayHelper::getValue($item, 'template', $this->linkTemplate);
        
        return strtr($template, [
            '{url}' => Html::encode(Url::to($item['url'])),
            '{label}' => $item['label'],
        ]);
    }
    
    protected function isItemActive($item)
    {
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = Yii::getAlias($item['url'][0]);
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }
            if (ltrim($route, '/') !== $this->route) {
                return false;
            }
            unset($item['url']['#']);
            if (count($item['url']) > 1) {
                $params = $item['url'];
                unset($params[0]);
                foreach ($params as $name => $value) {
                    if ($value !== null && (! isset($this->params[$name]) || $this->params[$name] != $value)) {
                        return false;
                    }
                }
            }
            
            return true;
        } elseif (isset($item['url'])) {
            
            $url = $item["url"];
            if (($pos = strpos($url, ':')) === false || !ctype_alpha(substr($url, 0, $pos))) {
                // turn relative URL into absolute
                $url = \Yii::$app->getUrlManager()->getHostInfo() . '/' . ltrim($url, '/');
            }
            
            if($url == $this->currentAbsoluteUrl)
            {
                return true;
            }
            
        }
        
        return false;
    }
}