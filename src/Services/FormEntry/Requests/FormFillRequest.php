<?php

namespace Larapress\Profiles\Services\FormEntry\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Larapress\CRUD\Exceptions\ValidationException;
use Larapress\Profiles\Models\Domain;
use Larapress\Profiles\Models\Form;
use Larapress\Profiles\Repository\Domain\IDomainRepository;
use Larapress\Profiles\Services\FormEntry\IFormEntryService;
use Mews\Captcha\Facades\Captcha;

/**
 *
 */
class FormFillRequest extends FormRequest
{
    protected $domain;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $form = $this->getForm();
        $dataRules = [];
        if (!is_null($form)) {
            /** @var IFormEntryService */
            $service = app(IFormEntryService::class);
            $rules = $service->getFormValidationRules($form);
            foreach ($rules as $key => $rule) {
                $dataRules['data.'.$key] = $rule;
            }
        }

        /** @var IDomainRepository */
        $ds = app(IDomainRepository::class);
        $this->domain = $ds->getRequestDomain($this);

        return array_merge([
            'form_name' => 'required_without:form_id|string|exits:forms,name',
            'form_id' => 'required_without:form_name|numeric|exists:forms,id',
            'tags' => 'nullable|string',
            'data' => 'nullable|json_object',
        ], $dataRules);
    }

    /**
     * Override default validation exception and include a new captcha
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     *
     * @throws ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new ValidationException($validator, [
            'captcha' => Captcha::create('default', true)
        ]);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getSenderIP(): string
    {
        return $this->getClientIp();
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getSenderAgent(): string
    {
        return $this->userAgent();
    }

    /**
     * Undocumented function
     *
     * @return Domain
     */
    public function getDomain(): Domain
    {
        return $this->domain;
    }

    /**
     * Undocumented function
     *
     * @return Form
     */
    public function getForm()
    {
        if (!is_null($this->get('form_id'))) {
            return Form::find($this->get('form_id'));
        } else {
            return Form::where('name', $this->get('form_name'))->first();
        }

        return null;
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function getTags()
    {
        return $this->get('tags');
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getEntryData()
    {
        return $this->get('data', []);
    }
}
