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
     * @param callable $onUpdate
     * @return FormEntry
     */
    public function updateFormEntry(Request $request, $formId, $tags = null, $onUpdate = null)
    {
        /** @var Form */
        $form = Form::find($formId);

        if (is_null($form)) {
            throw new AppException(AppException::ERR_OBJECT_NOT_FOUND);
        }

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

        /** @var IDomainRepository */
        $domainRepo = app(IDomainRepository::class);
        $domain = $domainRepo->getRequestDomain($request);

        $user = Auth::user();
        $entry = null;
        $created = true;

        if (is_null($user)) {
            // handler open (no user) forms
            $entry = FormEntry::create([
                'form_id' => $form->id,
                'domain_id' => $domain->id,
                'data' => [
                    'ip' => $request->ip(),
                    'agent' => $request->userAgent(),
                    'values' => $request->all($inputNames),
                ],
                'flags' => 0,
            ]);
        } else {
            $entry = FormEntry::query()
                ->where('user_id', $user->id)
                ->where('form_id', $form->id)
                ->where('domain_id', $domain->id);
            if (is_null($tags)) {
                $entry->whereNull('tags');
            } else {
                $entry->where('tags', $tags);
            }
            $entry = $entry->first();

            if (is_null($entry)) {
                $entry = FormEntry::create([
                    'user_id' => $user->id,
                    'form_id' => $form->id,
                    'domain_id' => $domain->id,
                    'tags' => $tags,
                    'data' => [
                        'ip' => $request->ip(),
                        'agent' => $request->userAgent(),
                        'values' => $request->all($inputNames),
                    ],
                    'flags' => 0,
                ]);
            } else {
                $values = is_null($onUpdate) ? $request->all($inputNames) : $onUpdate($request, $inputNames, $form, $entry);
                $created = false;
                $entry->update([
                    'data' => [
                        'ip' => $request->ip(),
                        'agent' => $request->userAgent(),
                        'values' => $values,
                    ],
                ]);
            }
        }

        FormEntryUpdateEvent::dispatch($user, $domain, $entry, $form, $created, $request->ip(), time());

        return $entry;
    }
}
