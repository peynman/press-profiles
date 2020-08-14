<?php

namespace Larapress\Profiles\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Larapress\CRUD\Exceptions\AppException;
use Larapress\CRUD\Exceptions\ValidationException;
use Larapress\Profiles\Models\Form;
use Larapress\Profiles\Models\FormEntry;
use Larapress\Profiles\Repository\Domain\IDomainRepository;

class FormEntryService implements IFormEntryService
{
    /**
     *
     * @param Request $request
     * @param int $formId
     * @param string|null $tags
     * @param callable $onProvide
     * @return FormEntry
     */
    public function updateFormEntry(Request $request, $formId, $tags = null, $onProvide = null)
    {
        /** @var Form */
        $form = Form::find($formId);

        if (is_null($form)) {
            throw new AppException(AppException::ERR_OBJECT_NOT_FOUND);
        }

        $inputNames = $this->validateFormEntryRequestAndGetInputs($request, $form);

        /** @var IDomainRepository */
        $domainRepo = app(IDomainRepository::class);
        $domain = $domainRepo->getRequestDomain($request);

        $user = Auth::user();
        $entry = null;
        $created = true;

        $entry = $this->resolveFormEntryRequest(
            $request,
            $user,
            $form,
            $tags,
            true,
            $domain,
            function () use ($user, $form, $domain, $request, $onProvide, $inputNames, $tags) {
                return FormEntry::create([
                    'user_id' => is_null($user) ? null : $user->id,
                    'form_id' => $form->id,
                    'domain_id' => $domain->id,
                    'tags' => $tags,
                    'data' => [
                        'ip' => $request->ip(),
                        'agent' => $request->userAgent(),
                        'values' => is_null($onProvide) ? $request->all($inputNames) : $onProvide($request, $inputNames, $form, null)
                    ],
                    'flags' => 0,
                ]);
            },
            function ($entry) use($form, $request, $onProvide, $inputNames, $tags, &$created) {
                $values = is_null($onProvide) ? $request->all($inputNames) : $onProvide($request, $inputNames, $form, $entry);
                $created = false;
                $entry->update([
                    'tags' => $tags,
                    'data' => [
                        'ip' => $request->ip(),
                        'agent' => $request->userAgent(),
                        'values' => $values,
                    ],
                ]);
            }
        );
        FormEntryUpdateEvent::dispatch($user, $domain, $entry, $form, $created, $request->ip(), time());

        return $entry;
    }



    public function updateUserFormEntryTag(Request $request, $user, $formId, $tags, $onProvide = null)
    {
        /** @var Form */
        $form = Form::find($formId);

        if (is_null($form)) {
            throw new AppException(AppException::ERR_OBJECT_NOT_FOUND);
        }

        $inputNames = $this->validateFormEntryRequestAndGetInputs($request, $form);

        /** @var IDomainRepository */
        $domainRepo = app(IDomainRepository::class);
        $domain = $domainRepo->getRequestDomain($request);

        $entry = null;
        $created = true;

        $entry = $this->resolveFormEntryRequest(
            $request,
            $user,
            $form,
            $tags,
            false,
            $domain,
            function () use ($user, $form, $domain, $request, $onProvide, $inputNames, $tags) {
                return FormEntry::create([
                    'user_id' => is_null($user) ? null : $user->id,
                    'form_id' => $form->id,
                    'domain_id' => $domain->id,
                    'tags' => $tags,
                    'data' => [
                        'ip' => $request->ip(),
                        'agent' => $request->userAgent(),
                        'values' => is_null($onProvide) ? $request->all($inputNames) : $onProvide($request, $inputNames, $form, null)
                    ],
                    'flags' => 0,
                ]);
            },
            function ($entry) use($form, $request, $onProvide, $inputNames, $tags, &$created) {
                $values = is_null($onProvide) ? $request->all($inputNames) : $onProvide($request, $inputNames, $form, $entry);
                $created = false;
                $entry->update([
                    'tags' => $tags,
                    'data' => [
                        'ip' => $request->ip(),
                        'agent' => $request->userAgent(),
                        'values' => $values,
                    ],
                ]);
            }
        );
        FormEntryUpdateEvent::dispatch($user, $domain, $entry, $form, $created, $request->ip(), time());

        return $entry;
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param Form $form
     * @return void
     */
    protected function validateFormEntryRequestAndGetInputs(Request $request, Form $form)
    {

        $rules = [];
        $inputNames = [];
        $feilds = isset($form->data['form']['schema']['fields']) ? $form->data['form']['schema']['fields'] : [];
        $collectValidationsDeep = function ($root, $path, $callback) use (&$rules, &$inputNames) {
            foreach ($root as $fieldName => $fieldObj) {
                if (isset($fieldObj['validations'])) {
                    $rules[$path . $fieldName] = [];
                    foreach ($fieldObj['validations'] as $check => $val) {
                        switch ($check) {
                            case 'required':
                                if ($val) {
                                    $rules[$path . $fieldName][] = 'required';
                                }
                                break;
                            case 'minLength':
                                if (is_numeric($val)) {
                                    $rules[$path . $fieldName][] = 'min:' . $val;
                                }
                                break;
                            case 'maxLength':
                                if (is_numeric($val)) {
                                    $rules[$path . $fieldName][] = 'max:' . $val;
                                }
                                break;
                            case 'numeric':
                                if ($val) {
                                    $rules[$path . $fieldName][] = 'numeric';
                                }
                                break;
                            case 'ascii':
                                if ($val) {
                                    $rules[$path . $fieldName][] = 'min:3|regex:/[^x00-x7F]*/';
                                }
                                break;
                        }
                    }
                }
                if (isset($fieldObj['fields'])) {
                    $callback($fieldObj['fields'], $path . $fieldName . '.', $callback);
                }
                if (isset($fieldObj['groups'])) {
                    $callback($fieldObj['groups'], $path . $fieldName . '.', $callback);
                }
                $inputNames[] = $fieldName;
            }
        };
        $collectValidationsDeep($feilds, '', $collectValidationsDeep);

        $validate = Validator::make($request->all($inputNames), $rules);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }

        return $inputNames;
    }

    /**
     * Undocumented function
     *
     * @param [type] $request
     * @param [type] $user
     * @param [type] $form
     * @param [type] $tags
     * @param [type] $checkTags
     * @param [type] $domain
     * @param [type] $onCreateEntry
     * @param [type] $onUpdateEntry
     * @return void
     */
    protected function resolveFormEntryRequest($request, $user, $form, $tags, $checkTags, $domain, $onCreateEntry, $onUpdateEntry)
    {
        if (is_null($user)) {
            // handler open (no user) forms
            return $onCreateEntry();
        } else {
            $entry = FormEntry::query()
                ->where('user_id', $user->id)
                ->where('form_id', $form->id)
                ->where('domain_id', $domain->id);
            if ($checkTags) {
                if (is_null($tags)) {
                    $entry->whereNull('tags');
                } else {
                    $entry->where('tags', $tags);
                }
            }
            $entry = $entry->first();

            if (is_null($entry)) {
                return $onCreateEntry();
            } else {
                return $onUpdateEntry($entry);
            }
        }
    }
}
