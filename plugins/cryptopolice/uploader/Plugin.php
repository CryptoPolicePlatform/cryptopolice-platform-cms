<?php namespace Cryptopolice\Uploader;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{

	public function pluginDetails(){
		return [
			'name' => 'FileUploader',
			'description' => 'Set a file uploader in frontend',
			'author' => 'CryptoPolice',
			'icon' => 'icon-upload',
		];
	}

	public function registerComponents()
	{
		return [
			'Cryptopolice\uploader\Components\ImageUploader' => 'Uploader',
		];
	}
}