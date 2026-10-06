<?php

namespace App\Support;

use Illuminate\Validation\Rule;

class ScreeningForm
{
    /**
     * A single definition drives the public form and its server-side validation.
     *
     * @return array<string, array<string, array{label: string, type: 'text'|'email'|'tel'|'textarea'|'number'|'file', required?: bool}|array{label: string, type: 'select', required: bool, options: array<string, string>}>>
     */
    public static function sections(): array
    {
        return [
            'Personal details' => [
                'name' => ['label' => 'Full Name', 'type' => 'text', 'required' => true],
                'email' => ['label' => 'Email Address', 'type' => 'email', 'required' => true],
                'phone' => ['label' => 'Phone Number', 'type' => 'tel', 'required' => true],
                'cv' => ['label' => 'CV / Resume (PDF, max 5 MB)', 'type' => 'file', 'required' => true],
                'current_age' => ['label' => 'Current Age', 'type' => 'number', 'required' => true],
                'marital_status' => ['label' => 'Marital Status', 'type' => 'select', 'required' => true, 'options' => self::options(['Single', 'Married'])],
                'current_status' => ['label' => 'Current Status', 'type' => 'select', 'required' => true, 'options' => self::options(['Still Working / employed', 'Available / free / unemployed'])],
                'current_domicile' => ['label' => 'Current Domicile', 'type' => 'select', 'required' => true, 'options' => self::options(['Jakarta Area', 'Bogor Area', 'Depok Area', 'Tangerang Area', 'Bekasi Area'])],
                'english_fluency' => ['label' => 'English Fluency', 'type' => 'select', 'required' => true, 'options' => self::options(['Professional', 'Conversation', 'Intermediate', 'Beginner'])],
            ],
            'Career and compensation' => [
                'foreign_language_fluency' => ['label' => 'Foreign Language Fluency (Other than English)', 'type' => 'text'],
                'current_salary_answer' => ['label' => 'Current/Last Salary', 'type' => 'text', 'required' => true],
                'additional_benefits' => ['label' => 'Additional Benefits (Private Insurance, Bonus, Allowance, etc)', 'type' => 'textarea', 'required' => true],
                'expected_salary_answer' => ['label' => 'Expected Salary', 'type' => 'text', 'required' => true],
                'notice_period' => ['label' => 'Join Notification', 'type' => 'select', 'required' => true, 'options' => self::options(['As soon as Possible (ASAP)', '2 weeks notice', '1 month notice', '2 months notice'])],
                'motivation' => ['label' => 'Motivation for Apply', 'type' => 'textarea', 'required' => true],
                'reason_for_leaving' => ['label' => 'Reason of Leaving Last Company', 'type' => 'textarea', 'required' => true],
            ],
            'Reference checks' => [
                'latest_company_reference' => ['label' => 'Reference Check Contact Details From Latest Company (Name, Position, Phone/Email)', 'type' => 'textarea', 'required' => true],
                'second_latest_company_reference' => ['label' => 'Reference Check Contact Details From 2nd Latest Company (Name, Position, Phone/Email) (If any)', 'type' => 'textarea'],
                'third_latest_company_reference' => ['label' => 'Reference Check Contact Details From 3rd Latest Company (Name, Position, Phone/Email) (If any)', 'type' => 'textarea'],
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
                    case 'number':
                        $rules[$name][] = 'integer';
                        $rules[$name][] = 'between:1,120';
                        break;
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
            'current_age.between' => 'Current Age must be between 1 and 120.',
            'phone.regex' => 'Phone Number must be a valid phone number.',
            'cv.mimes' => 'Upload a PDF file for your CV / Resume.',
            'cv.max' => 'CV / Resume must be no larger than 5 MB.',
            'serious_disease_details.required_if' => 'Please describe the illness/disease when you select Yes.',
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
