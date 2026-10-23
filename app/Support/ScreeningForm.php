<?php

namespace App\Support;

use Illuminate\Validation\Rule;

class ScreeningForm
{
    /**
     * A single definition drives the public form and its server-side validation.
     *
     * @return array<string, array<string, array{label: string, type: 'text'|'email'|'tel'|'textarea'|'file'|'date', required?: bool}|array{label: string, type: 'select', required: bool, options: array<string, string>}>>
     */
    public static function sections(): array
    {
        return [
            'Personal details' => [
                'name' => ['label' => 'Full Name', 'type' => 'text', 'required' => true],
                'email' => ['label' => 'Email Address', 'type' => 'email', 'required' => true],
                'phone' => ['label' => 'Phone Number', 'type' => 'tel', 'required' => true],
                'cv' => ['label' => 'CV / Resume (PDF, max 5 MB)', 'type' => 'file', 'required' => true],
                'date_of_birth' => ['label' => 'Date of Birth', 'type' => 'date', 'required' => true],
                'marital_status' => ['label' => 'Marital Status', 'type' => 'select', 'required' => true, 'options' => self::options(['Single', 'Married'])],
                'current_status' => ['label' => 'Current Status', 'type' => 'select', 'required' => true, 'options' => self::options(['Still Working / employed', 'Available / free / unemployed'])],
                'current_domicile' => ['label' => 'Current Domicile', 'type' => 'select', 'required' => true, 'options' => self::options(['Jakarta Area', 'Bogor Area', 'Depok Area', 'Tangerang Area', 'Bekasi Area'])],
                'english_fluency' => ['label' => 'English Fluency', 'type' => 'select', 'required' => true, 'options' => self::options(['Professional', 'Conversation', 'Intermediate', 'Beginner'])],
                'application_source' => ['label' => 'Where did you hear about this vacancy?', 'type' => 'select', 'required' => true, 'options' => self::options(['Referral', 'Approached by HR', 'Social media', 'Job portal', 'Company website'])],
                'referral_from' => ['label' => 'Who referred you?', 'type' => 'text'],
            ],
            'Career and compensation' => [
                'foreign_language_fluency' => ['label' => 'Foreign Language Fluency (Other than English)', 'type' => 'text'],
                'current_salary_answer' => ['label' => 'Current/Last Salary (IDR)', 'type' => 'text', 'required' => true],
                'additional_benefits' => ['label' => 'Additional Benefits (Private Insurance, Bonus, Allowance, etc)', 'type' => 'textarea', 'required' => true],
                'expected_salary_answer' => ['label' => 'Expected Salary (IDR)', 'type' => 'text', 'required' => true],
                'notice_period' => ['label' => 'Join Notification', 'type' => 'select', 'required' => true, 'options' => self::options(['As soon as Possible (ASAP)', '2 weeks notice', '1 month notice', '2 months notice'])],
                'motivation' => ['label' => 'Motivation for Apply', 'type' => 'textarea', 'required' => true],
                'reason_for_leaving' => ['label' => 'Reason of Leaving Last Company', 'type' => 'textarea', 'required' => true],
            ],
            'Reference checks' => [
                'latest_company_reference' => ['label' => 'Reference Check Contact Details From Latest Company (Name, Position, Phone/Email)', 'type' => 'textarea', 'required' => true],
                'second_latest_company_reference' => ['label' => 'Reference Check Contact Details From 2nd Latest Company (Name, Position, Phone/Email) (If any)', 'type' => 'textarea'],
            ],
            'Health' => [
                'serious_disease' => ['label' => 'Have you ever been diagnosed with any serious disease?', 'type' => 'select', 'required' => true, 'options' => ['yes' => 'Yes', 'no' => 'No']],
                'serious_disease_details' => ['label' => 'If yes, please describe the illness/disease.', 'type' => 'textarea'],
            ],
        ];
    }

    /** @return array<string, array<int, mixed>> */
    public static function rules(): array
    {
        $rules = [];

        foreach (self::sections() as $fields) {
            foreach ($fields as $name => $field) {
                $required = $field['required'] ?? false;
                $rules[$name] = [$required ? 'required' : 'nullable'];

                switch ($field['type']) {
                    case 'select':
                        $options = array_keys($field['options']);
                        $allowsOther = $name !== 'serious_disease';
                        $rules[$name][] = Rule::in($allowsOther ? [...$options, 'Other'] : $options);

                        if ($allowsOther) {
                            $rules[$name.'_other'] = ['required_if:'.$name.',Other', 'nullable', 'string', 'max:255'];
                        }
                        break;
                    case 'email':
                        $rules[$name][] = 'email';
                        $rules[$name][] = 'max:255';
                        break;
                    case 'tel':
                        $rules[$name][] = 'string';
                        $rules[$name][] = 'regex:/^\+?[0-9][0-9\s().-]{6,24}$/';
                        $rules[$name][] = 'max:25';
                        break;
                    case 'file':
                        $rules[$name][] = 'file';
                        $rules[$name][] = 'mimes:pdf';
                        $rules[$name][] = 'max:5120';
                        break;
                    default:
                        $rules[$name][] = 'string';
                        $rules[$name][] = $field['type'] === 'textarea' ? 'max:5000' : 'max:255';
                }
            }
        }

        $rules['serious_disease_details'][] = 'required_if:serious_disease,yes';
        $rules['date_of_birth'] = ['required', 'date_format:Y-m-d', 'before_or_equal:'.now('Asia/Jakarta')->toDateString()];
        $rules['referral_from'] = ['required_if:application_source,Referral', 'exclude_unless:application_source,Referral', 'string', 'max:255'];
        foreach (['current_salary_answer', 'expected_salary_answer'] as $salary) {
            $rules[$salary][] = 'regex:/^[0-9]{1,15}$/D';
        }

        return $rules;
    }

    /** @return array<string, string> */
    public static function attributes(): array
    {
        $attributes = [];

        foreach (self::sections() as $fields) {
            foreach ($fields as $name => $field) {
                $attributes[$name] = $field['label'];

                if ($field['type'] === 'select' && $name !== 'serious_disease') {
                    $attributes[$name.'_other'] = $field['label'].' (Other)';
                }
            }
        }

        return $attributes;
    }

    /** @return array<string, string> */
    public static function messages(): array
    {
        $messages = [
            'date_of_birth.date_format' => 'Select a valid Date of Birth.',
            'date_of_birth.before_or_equal' => 'Date of Birth cannot be in the future.',
            'phone.regex' => 'Phone Number must be a valid phone number.',
            'cv.mimes' => 'Upload a PDF file for your CV / Resume.',
            'cv.max' => 'CV / Resume must be no larger than 5 MB.',
            'serious_disease_details.required_if' => 'Please describe the illness/disease when you select Yes.',
            'referral_from.required_if' => 'Please enter the name of the person who referred you.',
            'current_salary_answer.regex' => 'Enter numbers only for the current salary, such as 8000000.',
            'expected_salary_answer.regex' => 'Enter numbers only for the expected salary, such as 10000000.',
        ];

        foreach (self::sections() as $fields) {
            foreach ($fields as $name => $field) {
                if ($field['type'] === 'select' && $name !== 'serious_disease') {
                    $messages[$name.'_other.required_if'] = 'Please specify '.$field['label'].' when choosing Other.';
                }
            }
        }

        return $messages;
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function normalized(array $data): array
    {
        foreach (self::sections() as $fields) {
            foreach ($fields as $name => $field) {
                if ($field['type'] !== 'select' || $name === 'serious_disease') {
                    continue;
                }

                if ($data[$name] === 'Other') {
                    $data[$name] = $data[$name.'_other'];
                }

                unset($data[$name.'_other']);
            }
        }

        $data['serious_disease'] = $data['serious_disease'] === 'yes';
        if (! $data['serious_disease']) {
            $data['serious_disease_details'] = null;
        }

        foreach (['current', 'expected'] as $salary) {
            $data[$salary.'_salary'] = (int) $data[$salary.'_salary_answer'];
            unset($data[$salary.'_salary_answer']);
        }

        return $data;
    }

    /** @param array<int, string> $values
     * @return array<string, string>
     */
    private static function options(array $values): array
    {
        return array_combine($values, $values);
    }
}
