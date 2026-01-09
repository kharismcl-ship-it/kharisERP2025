<?php

namespace Modules\Hostels\View\Components;

use Illuminate\View\Component;

class ImageUpload extends Component
{
    public string $label;

    public ?string $model;

    public $value;

    public ?string $remove;

    public string $accept;

    public string $promptHeading;

    public string $promptText;

    public string $previewAlt;

    public function __construct(
        string $label = '',
        ?string $model = null,
        $value = null,
        ?string $remove = null,
        string $accept = 'image/*',
        string $promptHeading = 'Upload Image',
        string $promptText = 'JPG, PNG, WEBP up to 2MB',
        string $previewAlt = 'Image Preview'
    ) {
        $this->label = $label;
        $this->model = $model;
        $this->value = $value;
        $this->remove = $remove;
        $this->accept = $accept;
        $this->promptHeading = $promptHeading;
        $this->promptText = $promptText;
        $this->previewAlt = $previewAlt;
    }

    public function render()
    {
        return view('hostels::components.image-upload');
    }
}
