<?php

namespace App\Rules;

use App\Services\RecaptchaService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RecaptchaRule implements ValidationRule
{
    private string $action;

    public function __construct(string $action = 'submit')
    {
        $this->action = $action;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $recaptchaService = app(RecaptchaService::class);

        // Skip if disabled
        if (! $recaptchaService->isEnabled()) {
            return;
        }

        $result = $recaptchaService->verify($value, $this->action);

        if (! $result['success']) {
            $fail('validation.recaptcha', $result['message'] ?? null);
        }
    }
}
