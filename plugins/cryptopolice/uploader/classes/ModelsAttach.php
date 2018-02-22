<?php namespace Cryptopolice\uploader\Classes;

use Str;
use File;
use Exception;
use DirectoryIterator;
use System\Classes\PluginManager;
use Cryptopolice\uploader\Classes\PluginCode;
use Cryptopolice\uploader\Classes\ModelFileParser;

class ModelsAttach{
	public static function getModelsWithAttach(){
		$plugins = array_keys(PluginManager::instance()->getPlugins());
		$modelsList = [];

		foreach ($plugins as $plugin){
			$pluginObj = new PluginCode($plugin);
			$models = self::listPluginModels($pluginObj);

			foreach ($models as $model) {
				$obj = $model['namespace'].'\\'.$model['class'];
				$modelsList[$obj] = $model['class'].' - '.$plugin;
			}
		}

		return $modelsList;
	}

	public static function getModelsWithProperties(){
		$models = self::getModelsWithAttach();
		$results = [];

		foreach ($models as $model => $name) {
			$obj = new $model;

			if(count($att = $obj->attachOne))
				foreach ($att as $key => $value)
					$results[$model][$key] = $key.' [One]';

			if(count($att = $obj->attachMany))
				foreach ($att as $key => $value)
					$results[$model][$key] = $key.' [Many]';
		}

		return $results;
	}

	private static function listPluginModels($pluginObj){
		$modelsDirectoryPath = $pluginObj->toPluginDirectoryPath().'/models';
		$pluginNamespace = $pluginObj->toPluginNamespace();
		$modelsDirectoryPath = File::symbolizePath($modelsDirectoryPath);

		if (!File::isDirectory($modelsDirectoryPath))
			return [];

		$parser = new ModelFileParser();
		$result = [];

		foreach (new DirectoryIterator($modelsDirectoryPath) as $fileInfo) {


			if (!$fileInfo->isFile())
				continue;

			if ($fileInfo->getExtension() != 'php')
				continue;

			$filePath = $fileInfo->getPathname();
			$contents = File::get($filePath);

			$modelInfo = $parser->extractModelInfoFromSource($contents);
			if (!$modelInfo)
				continue;

			if (!Str::startsWith($modelInfo['namespace'], $pluginNamespace.'\\'))
				continue;

			$modelName = $modelInfo['namespace'].'\\'.$modelInfo['class'];

			try{
				$modelObj = new $modelName;
			}catch(Exception $ex){ 
				continue; 
			}
			
			if(!(count($modelObj->attachOne) || count($modelObj->attachMany)))
				continue;

			if($modelObj instanceof \Backend\Models\ImportModel)
				continue;
			if($modelObj instanceof \Backend\Models\ExportModel)
				continue;
				
			$result[] = $modelInfo;
		}
		return $result;
	}
}
?>