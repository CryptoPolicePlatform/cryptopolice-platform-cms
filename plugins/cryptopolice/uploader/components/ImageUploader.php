<?php namespace Cryptopolice\Uploader\Components;

use System\Models\File;
use ApplicationException;
use Cms\Classes\ComponentBase;
use RainLab\Builder\Classes\ComponentHelper;

class ImageUploader extends ComponentBase
{

    use \Cryptopolice\Uploader\Traits\ComponentUtils;

    public $maxSize;
    public $imageWidth;
    public $imageHeight;
    public $imageMode;
    public $previewFluid;
    public $placeholderText;

    /**
     * @var array Options used for generating thumbnails.
     */
    public $thumbOptions = [
        'mode' => 'crop',
        'extension' => 'auto'
    ];

    /**
     * Supported file types.
     * @var array
     */
    public $fileTypes;

    /**
     * @var bool Has the model been bound.
     */
    protected $isBound = false;

    /**
     * @var bool Is the related attribute a "many" type.
     */
    public $isMulti = false;

    /**
     * @var Collection
     */
    public $fileList;

    /**
     * @var Model
     */
    public $singleFile;

    public function componentDetails()
    {
        return [
            'name' => 'Image Uploader',
            'description' => 'Upload an image with preview'
        ];
    }

    public function defineProperties()
    {
        return [
            'placeholderText' => [
                'title' => 'Placeholder text',
                'description' => 'Wording to display when no image is uploaded',
                'default' => 'Click or drag images to upload',
                'type' => 'string',
            ],
            'maxSize' => [
                'title' => 'Max file size (MB)',
                'description' => 'The maximum file size that can be uploaded in megabytes.',
                'default' => '5',
                'type' => 'string',
            ],
            'fileTypes' => [
                'title' => 'Supported file types',
                'description' => 'File extensions separated by commas (,) or star (*) to allow all types.',
                'default' => '.gif,.jpg,.jpeg,.png',
                'type' => 'string',
            ],
            'imageWidth' => [
                'title' => 'Image preview width',
                'description' => 'Enter an amount in pixels, eg: 100',
                'default' => '100',
                'type' => 'string',
            ],
            'imageHeight' => [
                'title' => 'Image preview height',
                'description' => 'Enter an amount in pixels, eg: 100',
                'default' => '100',
                'type' => 'string',
            ],
            'imageMode' => [
                'title' => 'Image preview mode',
                'description' => 'Thumb mode for the preview, eg: exact, portrait, landscape, auto or crop',
                'default' => 'crop',
                'type' => 'string',
            ],
            'modelClass' => [
                'title' => 'Model Class',
                'type' => 'dropdown',
                'group' => 'Model',
                'showExternalParam' => false
            ],
            'modelKeyColumn' => [
                'title' => 'Key column',
                'description' => 'Model column to use as a record identifier for fetching the record from the database.',
                'type' => 'autocomplete',
                'depends' => ['modelClass'],
                'default' => 'picture',
                'group' => 'Model',
                'validation' => [
                    'required' => [
                        'message' => 'The key column name is required'
                    ]
                ],
                'showExternalParam' => false
            ],
            'identifierValue' => [
                'title' => 'Identifier value',
                'description' => 'Identifier value to load the record from the database. Specify a fixed value or URL parameter name.',
                'type' => 'string',
                'default' => '{{ :id }}',
                'group' => 'Model',
                'validation' => [
                    'required' => [
                        'message' => 'The identifier value is required'
                    ]
                ]
            ],
            'deferredBinding' => [
                'title' => 'Use deferred binding',
                'description' => 'If checked the associated model must be saved for the upload to be bound.',
                'type' => 'checkbox',
                'group' => 'Model',
            ],
        ];
    }

    public function init()
    {
        $this->fileTypes        = $this->processFileTypes(true);
        $this->maxSize          = $this->property('maxSize');
        $this->imageWidth       = $this->property('imageWidth');
        $this->imageHeight      = $this->property('imageHeight');
        $this->imageMode        = $this->property('imageMode');
        $this->previewFluid     = $this->property('previewFluid');
        $this->placeholderText  = $this->property('placeholderText');

        $this->displayColumn    = $this->page['displayColumn'] = $this->property('displayColumn');
        $this->modelKeyColumn   = $this->page['modelKeyColumn'] = $this->property('modelKeyColumn');
        $this->identifierValue  = $this->page['identifierValue'] = $this->property('identifierValue');

        $this->thumbOptions['mode'] = $this->imageMode;

        $modelClassName = $this->property('modelClass');
        $model = new $modelClassName();
        $bind = $this->property('deferredBinding') ? $model : $model->where('id', $this->property('identifierValue'))->first();
        $this->bindModel($this->property('modelKeyColumn'), $bind);
    }

    public function onRun()
    {
        $this->addCss('assets/css/uploader.css');
        $this->addJs('assets/vendor/dropzone/dropzone.js');
        $this->addJs('assets/js/uploader.js');

        if ($result = $this->checkUploadAction()) {
            return $result;
        }

        $this->fileList = $fileList = $this->getFileList();
        $this->singleFile = $fileList->first();
    }

    public function getCssBlockDimensions()
    {
        return $this->getCssDimensions('block');
    }

    /**
     * Returns the CSS dimensions for the uploaded image,
     * uses auto where no dimension is provided.
     * @param string $mode
     * @return string
     */
    public function getCssDimensions($mode = null)
    {
        if (!$this->imageWidth && !$this->imageHeight) {
            return '';
        }

        $cssDimensions = '';

        if ($mode == 'block') {
            $cssDimensions .= ($this->imageWidth)
                ? 'width: ' . $this->imageWidth . 'px;'
                : 'width: ' . $this->imageHeight . 'px;';

            $cssDimensions .= ($this->imageHeight)
                ? 'height: ' . $this->imageHeight . 'px;'
                : 'height: auto;';
        } else {
            $cssDimensions .= ($this->imageWidth)
                ? 'width: ' . $this->imageWidth . 'px;'
                : 'width: auto;';

            $cssDimensions .= ($this->imageHeight)
                ? 'height: ' . $this->imageHeight . 'px;'
                : 'height: auto;';
        }

        return $cssDimensions;
    }

    /**
     * Adds the bespoke attributes used internally by this widget.
     * - thumbUrl
     * - pathUrl
     * @return System\Models\File
     */
    protected function decorateFileAttributes($file)
    {
        $path = $thumb = $file->getPath();

        if ($this->isMulti) {
            $thumb = $file->getThumb(63, 63, $this->thumbOptions);
        } elseif ($this->imageWidth || $this->imageHeight) {
            $thumb = $file->getThumb($this->imageWidth, $this->imageHeight, $this->thumbOptions);
        }

        $file->pathUrl = $path;
        $file->thumbUrl = $thumb;

        return $file;
    }

    public function onRender()
    {
        if (!$this->isBound) {
            throw new ApplicationException('There is no model bound to the uploader!');
        }

        if ($populated = $this->property('populated')) {
            $this->setPopulated($populated);
        }
    }

    public function onRemoveAttachment()
    {
        if (($file_id = post('file_id')) && ($file = File::find($file_id))) {
            $this->model->{$this->attribute}()->remove($file, $this->getSessionKey());
        }
    }

    public function getModelClassOptions()
    {
        return ComponentHelper::instance()->listGlobalModels();
    }
}