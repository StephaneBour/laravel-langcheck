<?php

namespace Sinnbeck\LangCheck\Test;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Sinnbeck\LangCheck\TranslationLocator;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

class TranslationLocatorTest extends TestCase
{
    protected $translationLocator;
    protected $knownTranslations = [
        'auth.failed' => 'These credentials do not match our records.',
        'auth.throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
        'pagination.previous' => '&laquo; Previous',
        'pagination.next' => 'Next &raquo;',
        'passwords.reset' => 'Your password has been reset!',
        'passwords.sent' => 'We have emailed your password reset link!',
        'passwords.throttled' => 'Please wait before retrying.',
        'passwords.token' => 'This password reset token is invalid.',
        'passwords.user' => 'We can\'t find a user with that email address.',
        'validation.accepted' => 'The :attribute must be accepted.',
        'validation.active_url' => 'The :attribute is not a valid URL.',
        'validation.after' => 'The :attribute must be a date after :date.',
        'validation.after_or_equal' => 'The :attribute must be a date after or equal to :date.',
        'validation.alpha' => 'The :attribute may only contain letters.',
        'validation.alpha_dash' => 'The :attribute may only contain letters, numbers, dashes and underscores.',
        'validation.alpha_num' => 'The :attribute may only contain letters and numbers.',
        'validation.array' => 'The :attribute must be an array.',
        'validation.before' => 'The :attribute must be a date before :date.',
        'validation.before_or_equal' => 'The :attribute must be a date before or equal to :date.',
        'validation.between.numeric' => 'The :attribute must be between :min and :max.',
        'validation.between.file' => 'The :attribute must be between :min and :max kilobytes.',
        'validation.between.string' => 'The :attribute must be between :min and :max characters.',
        'validation.between.array' => 'The :attribute must have between :min and :max items.',
        'validation.boolean' => 'The :attribute field must be true or false.',
        'validation.confirmed' => 'The :attribute confirmation does not match.',
        'validation.date' => 'The :attribute is not a valid date.',
        'validation.date_equals' => 'The :attribute must be a date equal to :date.',
        'validation.date_format' => 'The :attribute does not match the format :format.',
        'validation.different' => 'The :attribute and :other must be different.',
        'validation.digits' => 'The :attribute must be :digits digits.',
        'validation.digits_between' => 'The :attribute must be between :min and :max digits.',
        'validation.dimensions' => 'The :attribute has invalid image dimensions.',
        'validation.distinct' => 'The :attribute field has a duplicate value.',
        'validation.email' => 'The :attribute must be a valid email address.',
        'validation.ends_with' => 'The :attribute must end with one of the following: :values.',
        'validation.exists' => 'The selected :attribute is invalid.',
        'validation.file' => 'The :attribute must be a file.',
        'validation.filled' => 'The :attribute field must have a value.',
        'validation.gt.numeric' => 'The :attribute must be greater than :value.',
        'validation.gt.file' => 'The :attribute must be greater than :value kilobytes.',
        'validation.gt.string' => 'The :attribute must be greater than :value characters.',
        'validation.gt.array' => 'The :attribute must have more than :value items.',
        'validation.gte.numeric' => 'The :attribute must be greater than or equal :value.',
        'validation.gte.file' => 'The :attribute must be greater than or equal :value kilobytes.',
        'validation.gte.string' => 'The :attribute must be greater than or equal :value characters.',
        'validation.gte.array' => 'The :attribute must have :value items or more.',
        'validation.image' => 'The :attribute must be an image.',
        'validation.in' => 'The selected :attribute is invalid.',
        'validation.in_array' => 'The :attribute field does not exist in :other.',
        'validation.integer' => 'The :attribute must be an integer.',
        'validation.ip' => 'The :attribute must be a valid IP address.',
        'validation.ipv4' => 'The :attribute must be a valid IPv4 address.',
        'validation.ipv6' => 'The :attribute must be a valid IPv6 address.',
        'validation.json' => 'The :attribute must be a valid JSON string.',
        'validation.lt.numeric' => 'The :attribute must be less than :value.',
        'validation.lt.file' => 'The :attribute must be less than :value kilobytes.',
        'validation.lt.string' => 'The :attribute must be less than :value characters.',
        'validation.lt.array' => 'The :attribute must have less than :value items.',
        'validation.lte.numeric' => 'The :attribute must be less than or equal :value.',
        'validation.lte.file' => 'The :attribute must be less than or equal :value kilobytes.',
        'validation.lte.string' => 'The :attribute must be less than or equal :value characters.',
        'validation.lte.array' => 'The :attribute must not have more than :value items.',
        'validation.max.numeric' => 'The :attribute may not be greater than :max.',
        'validation.max.file' => 'The :attribute may not be greater than :max kilobytes.',
        'validation.max.string' => 'The :attribute may not be greater than :max characters.',
        'validation.max.array' => 'The :attribute may not have more than :max items.',
        'validation.mimes' => 'The :attribute must be a file of type: :values.',
        'validation.mimetypes' => 'The :attribute must be a file of type: :values.',
        'validation.min.numeric' => 'The :attribute must be at least :min.',
        'validation.min.file' => 'The :attribute must be at least :min kilobytes.',
        'validation.min.string' => 'The :attribute must be at least :min characters.',
        'validation.min.array' => 'The :attribute must have at least :min items.',
        'validation.not_in' => 'The selected :attribute is invalid.',
        'validation.not_regex' => 'The :attribute format is invalid.',
        'validation.numeric' => 'The :attribute must be a number.',
        'validation.password' => 'The password is incorrect.',
        'validation.present' => 'The :attribute field must be present.',
        'validation.regex' => 'The :attribute format is invalid.',
        'validation.required' => 'The :attribute field is required.',
        'validation.required_if' => 'The :attribute field is required when :other is :value.',
        'validation.required_unless' => 'The :attribute field is required unless :other is in :values.',
        'validation.required_with' => 'The :attribute field is required when :values is present.',
        'validation.required_with_all' => 'The :attribute field is required when :values are present.',
        'validation.required_without' => 'The :attribute field is required when :values is not present.',
        'validation.required_without_all' => 'The :attribute field is required when none of :values are present.',
        'validation.same' => 'The :attribute and :other must match.',
        'validation.size.numeric' => 'The :attribute must be :size.',
        'validation.size.file' => 'The :attribute must be :size kilobytes.',
        'validation.size.string' => 'The :attribute must be :size characters.',
        'validation.size.array' => 'The :attribute must contain :size items.',
        'validation.starts_with' => 'The :attribute must start with one of the following: :values.',
        'validation.string' => 'The :attribute must be a string.',
        'validation.timezone' => 'The :attribute must be a valid zone.',
        'validation.unique' => 'The :attribute has already been taken.',
        'validation.uploaded' => 'The :attribute failed to upload.',
        'validation.url' => 'The :attribute format is invalid.',
        'validation.uuid' => 'The :attribute must be a valid UUID.',
        'validation.custom.attribute-name.rule-name' => 'custom-message',
    ];

    public function setup(): void
    {
        parent::setup();
        $this->translationLocator = resolve(TranslationLocator::class);
    }

    public function test_it_two_found_locales()
    {
        Config::set('app.locale', 'en');
        $base = $this->app['path.lang'];
        File::shouldReceive('directories')
            ->once()
            ->with($base)
            ->andReturn(['en', 'da']);

        $this->assertEquals(2, count(resolve(TranslationLocator::class)->getLocaleDirectories('en')));
    }

    public function test_it_only_returns_two_directories_of_three()
    {
        Config::set('app.locale', 'en');
        $base = $this->app['path.lang'];
        File::shouldReceive('directories')
            ->once()
            ->with($base)
            ->andReturn(['en', 'da', 'sv']);

        resolve(TranslationLocator::class)->getLocaleDirectories('en', ['da']);
    }

    public function test_it_throws_exception()
    {
        Config::set('app.locale', 'en');
        $base = $this->app['path.lang'];
        File::shouldReceive('directories')
            ->once()
            ->with($base)
            ->andReturn(['en', 'da']);

        $this->expectException(DirectoryNotFoundException::class);
        resolve(TranslationLocator::class)->getLocaleDirectories('en', ['ru']);
    }

    public function test_that_it_can_load_dot_translations()
    {
        $expected = [
            'en' => $this->knownTranslations,
        ];

        $this->translationLocator->getLocaleDirectories('en');
        $this->assertEquals($expected, $this->translationLocator->getDotTranslations());
    }

}
