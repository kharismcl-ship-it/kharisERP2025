<?php

use Modules\CommunicationCentre\Models\CommTemplate;

it('can create templates with different languages', function () {
    // Create templates with different languages
    $englishTemplate = CommTemplate::create([
        'code' => 'test_welcome',
        'channel' => 'email',
        'provider' => 'laravel_mail',
        'name' => 'Welcome Email',
        'subject' => 'Welcome {{name}}!',
        'body' => 'Hello {{name}}, welcome to our service!',
        'language' => 'en',
        'is_active' => true,
    ]);

    $frenchTemplate = CommTemplate::create([
        'code' => 'test_welcome',
        'channel' => 'email',
        'provider' => 'laravel_mail',
        'name' => 'Email de Bienvenue',
        'subject' => 'Bienvenue {{name}} !',
        'body' => 'Bonjour {{name}}, bienvenue sur notre service !',
        'language' => 'fr',
        'is_active' => true,
    ]);

    expect($englishTemplate)->not->toBeNull()
        ->and($frenchTemplate)->not->toBeNull()
        ->and($englishTemplate->language)->toBe('en')
        ->and($frenchTemplate->language)->toBe('fr');
});

it('prefers language specific templates')->markTestIncomplete();
it('language field is accessible in filament forms')->markTestIncomplete();
