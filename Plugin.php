<?php namespace x0\Toolbox;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public function pluginDetails()
    {
        return [
            'name'        => 'ToolBox',
            'description' => 'Вспомогательные плагины',
            'author'      => 'x0pavel',
            'icon'        => 'icon-flash'
        ];
    }

    public function registerComponents()
    {
        return [
            '\x0\Toolbox\Components\UrlParams' => 'UrlParams'
        ];
    }
}
