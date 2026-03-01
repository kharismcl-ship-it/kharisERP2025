<?php

namespace Modules\CommunicationCentre\Filament\Components;

use Filament\Forms\Components\Field;
use Modules\CommunicationCentre\Models\CommTemplate;

class TemplatePreviewField extends Field
{
    protected string $view = 'communicationcentre::components.template-preview-field';

    protected ?CommTemplate $template = null;

    protected array $previewData = [];

    public static function make(?string $name = null): static
    {
        $instance = parent::make($name);

        return $instance->name($name ?? 'template_preview');
    }

    public function template(CommTemplate $template): static
    {
        $this->template = $template;

        return $this;
    }

    public function getTemplate(): ?CommTemplate
    {
        return $this->template;
    }

    public function previewData(array $data): static
    {
        $this->previewData = $data;

        return $this;
    }

    public function getPreviewData(): array
    {
        return $this->previewData;
    }

    public function renderTemplate(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        foreach ($this->previewData as $key => $value) {
            $content = str_replace(
                ['{{'.$key.'}}', '{'.$key.'}'],
                e($value),
                $content
            );
        }

        // Remove any remaining unmatched placeholders
        $content = preg_replace('/\{\{[^}]*\}\}/', '<span class="text-red-500">[Not Provided]</span>', $content);
        $content = preg_replace('/\{[^}]*\}/', '<span class="text-red-500">[Not Provided]</span>', $content);

        return $content;
    }
}
