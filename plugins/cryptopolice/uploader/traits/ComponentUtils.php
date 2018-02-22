<?php namespace Cryptopolice\uploader\Traits;

use Input;
use Event;
use Request;
use Response;
use Validator;
use ValidationException;
use ApplicationException;
use System\Models\File;
use October\Rain\Support\Collection;
use Exception;

trait ComponentUtils
{

    /**
     * @var Model
     */
    public $model;

    /**
     * @var string
     */
    public $attribute;

    /**
     * @var string
     */
    public $sessionKey;

    public function bindModel($attribute, $model)
    {
        if (is_callable($model))
            $model = $model();

        $this->model = $model;
        $this->attribute = $attribute;

        if ($this->model) {
            $relationType = $this->model->getRelationType($attribute);
            $this->isMulti = ($relationType == 'attachMany' || $relationType == 'morphMany');
            $this->isBound = true;
        }
    }

    public function setPopulated($model)
    {
        $list = $this->isMulti ? $model : new Collection([$model]);

        $list->each(function($file) {
            $this->decorateFileAttributes($file);
        });

        $this->fileList = $list;
        $this->singleFile = $list->first();
    }

    public function isPopulated()
    {
        if (!$this->fileList) {
            return false;
        }

        return $this->fileList->count() > 0;
    }

    public function getFileList()
    {
        /*
         * Use deferred bindings
         */
        if ($sessionKey = $this->getSessionKey()) {
            $list = $deferredQuery = $this->model
                ->{$this->attribute}()
                ->withDeferred($sessionKey)
                ->orderBy('id', 'desc')
                ->get();
        }
        else {
            $list = $this->model
                ->{$this->attribute}()
                ->orderBy('id', 'desc')
                ->get();
        }

        if (!$list) {
            $list = new Collection;
        }

        /*
         * Decorate each file with thumb
         */
        $list->each(function($file) {
            $this->decorateFileAttributes($file);
        });

        return $list;
    }

    protected function checkUploadAction()
    {

        if (!($uniqueId = Request::header('X-OCTOBER-FILEUPLOAD')) || $uniqueId != $this->alias) {
            return;
        }

        try {
            if (!Input::hasFile('file_data')) {
                throw new ApplicationException('File missing from request');
            }

            $uploadedFile = Input::file('file_data');

            $validation = Validator::make(
                ['file_data' => $uploadedFile], // this is the single image instance
                ['file_data' => 'required|image|mimes:jpg,jpeg,png|max:500'] // this is the rule for a single image
            );

            if ($validation->fails()) {
                throw new ValidationException($validation);
            }

            if (!$uploadedFile->isValid()) {
                throw new ApplicationException(sprintf('File %s is not valid.', $uploadedFile->getClientOriginalName()));
            }

            $file = new File;
            $file->data = $uploadedFile;
            $file->is_public = true;
            $file = $this->decorateFileAttributes($file);

            $file->save();

            if ($this->isMulti) {
                $this->model->{$this->attribute}()->add($file, $this->getSessionKey());
            } else {
                $this->model->{$this->attribute}()->save($file, $this->getSessionKey());
            }


            $result = [
                'id' => $file->id,
                'thumb' => $file->thumbUrl,
                'path' => $file->pathUrl
            ];

            return Response::json($result, 200);

        }
        catch (Exception $ex) {
            return Response::json($ex->getMessage(), 400);
        }
    }

    public function getSessionKey()
    {
        return !!$this->property('deferredBinding')
            ? post('_session_key', $this->sessionKey)
            : null;
    }

}