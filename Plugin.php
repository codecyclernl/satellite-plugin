<?php namespace Codecycler\Satellite;

use Backend;
use System\Classes\PluginBase;
use Codecycler\Satellite\Console\Transmit;

/**
 * Satellite Plugin Information File
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
            'name'        => 'Satellite',
            'description' => 'Communicates with Mission Control',
            'author'      => 'Codecycler',
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
        $this->registerConsoleCommand('satellite.transmit', Transmit::class);
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'codecycler.satellite.manager_settings' => [
                'tab' => 'Satellite',
                'label' => 'Manage settings'
            ],
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'Satellite Settings',
                'description' => 'Manage satellite based settings.',
                'category'    => 'system::lang.system.categories.system',
                'icon'        => 'icon-rocket',
                'class'       => 'Codecycler\Satellite\Models\Settings',
                'order'       => 500,
                'keywords'    => 'satellite mission control monitoring',
                'permissions' => ['codecycler.satellite.manager_settings'],
            ]
        ];
    }

    public function registerSchedule($schedule)
    {
        $schedule
            ->command('satellite:transmit')
            ->everyMinute();
    }
}
