<?php

namespace Modules\CommunicationCentre\Filament\Components;

use Filament\Forms\Components\Field;
use Modules\CommunicationCentre\Models\CommTemplate;

class TemplatePreview extends Field
{
    protected string $view = 'communicationcentre::components.template-preview';

    public static function make(?string $name = null): static
    {
        $instance = parent::make($name);

        return $instance->name($name ?? 'template_preview');
    }

    public function getTemplateData(CommTemplate $template, array $data = []): array
    {
        $previewData = [
            'subject' => $this->renderTemplate($template->subject, $data),
            'body' => $this->renderTemplate($template->body, $data),
            'channel' => $template->channel,
            'template' => $template,
        ];

        return $previewData;
    }

    protected function renderTemplate(?string $content, array $data): string
    {
        if (empty($content)) {
            return '';
        }

        foreach ($data as $key => $value) {
            $content = str_replace(
                ['{{'.$key.'}}', '{'.$key.'}'],
                $value,
                $content
            );
        }

        // Remove any remaining unmatched placeholders
        $content = preg_replace('/\{\{[^}]*\}\}/', '[Not Provided]', $content);
        $content = preg_replace('/\{[^}]*\}/', '[Not Provided]', $content);

        return $content;
    }
}
