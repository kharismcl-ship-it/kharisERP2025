<?php

namespace Modules\CommunicationCentre\Livewire;

use Livewire\Component;
use Modules\CommunicationCentre\Models\CommTemplate;

class TemplatePreview extends Component
{
    public ?CommTemplate $template = null;

    public array $data = [];

    public string $previewSubject = '';

    public string $previewBody = '';

    protected $listeners = ['updatePreview' => 'updatePreview'];

    public function mount(?CommTemplate $template = null)
    {
        $this->template = $template;
        $this->updatePreview();
    }

    public function updatePreview(array $data = [])
    {
        $this->data = array_merge($this->data, $data);

        if ($this->template) {
            $this->previewSubject = $this->renderTemplate($this->template->subject);
            $this->previewBody = $this->renderTemplate($this->template->body);
        }
    }

    protected function renderTemplate(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        foreach ($this->data as $key => $value) {
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

    public function render()
    {
        return view('communicationcentre::livewire.template-preview');
    }
}
