<?php namespace Polilla\Demo;

use Backend;
use System\Classes\PluginBase;

/**
 * Demo Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Demo',
            'description' => 'No description provided yet...',
            'author'      => 'Polilla',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Polilla\Demo\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'polilla.demo.some_permission' => [
                'tab' => 'Demo',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [
            'demo' => [
                'label' => 'Demo',
                'url' => Backend::url('polilla/demo/invoices'),
                'icon' => 'icon-diamond',
                'permissions' => ['polilla.demo.*'],
                'order' => 500,
                'sideMenu' => [
                    'invoices' => [
                        'label'       => 'Invoices',
                        'url'         => Backend::url('polilla/demo/invoices'),
                        'icon'        => 'icon-diamond',
                        'permissions' => ['polilla.demo.*'],
                        'order'       => 500,
                    ],
                    'items' => [
                        'label'       => 'Items',
                        'url'         => Backend::url('polilla/demo/items'),
                        'icon'        => 'icon-clone',
                        'permissions' => ['polilla.demo.*'],
                        'order'       => 500,
                    ],
                ]
            ],
        ];
    }
}
