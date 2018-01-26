<?php namespace CryptoPolice\KYC;

use Backend;
use System\Classes\PluginBase;

/**
 * KYC Plugin Information File
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
            'name'        => 'K.Y.C.',
            'description' => 'Know your customer plugin...',
            'author'      => 'CryptoPolice',
            'icon'        => 'icon-user-secret'
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
     * @return array
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
        return [
            'CryptoPolice\KYC\Components\KYC' => 'kyc',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'cryptopolice.kyc.some_permission' => [
                'tab'   => 'KYC',
                'label' => 'Some permission'
            ],
            'cryptopolice.kyc.access_settings' => [
                'tab'   => 'settings',
                'label' => 'Access settings permission'
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
        return []; // Remove this line to activate

        return [
            'kyc' => [
                'label'       => 'KYC',
                'url'         => Backend::url('cryptopolice/kyc/mycontroller'),
                'icon'        => 'icon-user-secret',
                'permissions' => ['cryptopolice.kyc.*'],
                'order'       => 500,
            ],
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'Face Api',
                'description' => 'Microsoft Azure',
                'icon'        => 'icon-user-secret',
                'class'       => 'CryptoPolice\KYC\Models\Settings',
                'permissions' => ['cryptopolice.kyc.access_settings'],
            ]
        ];
    }
}
