<?php

namespace Modules\CommunicationCentre\Services;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Models\CommTemplate;

class TemplateValidationService
{
    /**
     * Extract all variables from template content.
     */
    public function extractVariables(string $templateContent): array
    {
        $variables = [];

        // Match all {{variable}} patterns
        preg_match_all('/\{\{\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\}\}/', $templateContent, $matches);

        if (! empty($matches[1])) {
            $variables = array_unique($matches[1]);
        }

        return $variables;
    }

    /**
     * Validate that all required variables are provided.
     */
    public function validateTemplateData(CommTemplate $template, array $data): array
    {
        $errors = [];

        // Extract variables from both subject and body
        $subjectVariables = $this->extractVariables($template->subject ?? '');
        $bodyVariables = $this->extractVariables($template->body ?? '');

        $allVariables = array_unique(array_merge($subjectVariables, $bodyVariables));

        // Check for missing variables
        $missingVariables = [];
        foreach ($allVariables as $variable) {
            if (! array_key_exists($variable, $data) || $data[$variable] === null || $data[$variable] === '') {
                $missingVariables[] = $variable;
            }
        }

        if (! empty($missingVariables)) {
            $errors['missing_variables'] = $missingVariables;
        }

        // Check for unused variables (warnings only)
        $unusedVariables = [];
        foreach ($data as $key => $value) {
            if (! in_array($key, $allVariables)) {
                $unusedVariables[] = $key;
            }
        }

        if (! empty($unusedVariables)) {
            $errors['unused_variables'] = $unusedVariables;
        }

        return $errors;
    }

    /**
     * Get validation rules for template variables.
     */
    public function getValidationRules(CommTemplate $template): array
    {
        $subjectVariables = $this->extractVariables($template->subject ?? '');
        $bodyVariables = $this->extractVariables($template->body ?? '');

        $allVariables = array_unique(array_merge($subjectVariables, $bodyVariables));

        $rules = [];
        foreach ($allVariables as $variable) {
            $rules[$variable] = 'required';
        }

        return $rules;
    }

    /**
     * Validate template data with Laravel validation.
     */
    public function validateWithLaravel(CommTemplate $template, array $data): array
    {
        $rules = $this->getValidationRules($template);

        $validator = validator($data, $rules);

        if ($validator->fails()) {
            return $validator->errors()->toArray();
        }

        return [];
    }

    /**
     * Get human-readable error messages.
     */
    public function getErrorMessage(array $validationErrors): string
    {
        if (empty($validationErrors)) {
            return '';
        }

        $messages = [];

        if (isset($validationErrors['missing_variables'])) {
            $messages[] = 'Missing required variables: '.implode(', ', $validationErrors['missing_variables']);
        }

        if (isset($validationErrors['unused_variables'])) {
            $messages[] = 'Unused variables provided: '.implode(', ', $validationErrors['unused_variables']);
        }

        // Laravel validation errors
        foreach ($validationErrors as $field => $errors) {
            if (is_array($errors) && $field !== 'missing_variables' && $field !== 'unused_variables') {
                $messages[] = $field.': '.implode(', ', $errors);
            }
        }

        return implode('; ', $messages);
    }

    /**
     * Validate template before sending.
     */
    public function validateBeforeSend(CommTemplate $template, array $data, bool $throwException = true): bool
    {
        $errors = $this->validateTemplateData($template, $data);

        if (! empty($errors)) {
            $errorMessage = $this->getErrorMessage($errors);

            Log::warning('Template validation failed', [
                'template_id' => $template->id,
                'template_code' => $template->code,
                'errors' => $errors,
                'provided_data' => array_keys($data),
            ]);

            if ($throwException) {
                throw new \Exception("Template validation failed: {$errorMessage}");
            }

            return false;
        }

        return true;
    }

    /**
     * Get template variables with metadata.
     */
    public function getTemplateVariables(CommTemplate $template): array
    {
        $subjectVariables = $this->extractVariables($template->subject ?? '');
        $bodyVariables = $this->extractVariables($template->body ?? '');

        $allVariables = array_unique(array_merge($subjectVariables, $bodyVariables));

        return [
            'subject_variables' => $subjectVariables,
            'body_variables' => $bodyVariables,
            'all_variables' => $allVariables,
            'count' => count($allVariables),
        ];
    }
}
