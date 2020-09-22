<?php

namespace Larapress\Profiles\Services\FormEntry;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Larapress\CRUD\Exceptions\AppException;
use Larapress\CRUD\Exceptions\ValidationException;
use Larapress\Profiles\Models\Form;
use Larapress\Profiles\Models\FormEntry;
use Larapress\Profiles\Repository\Domain\IDomainRepository;
use Larapress\Profiles\IProfileUser;
use Larapress\CRUD\ICRUDUser;
use Larapress\ECommerce\Models\WalletTransaction;
use Larapress\ECommerce\Services\Banking\IBankingService;
use Illuminate\Support\Str;
use Larapress\Ecommerce\Services\FileUpload\IFileUploadService;

class FormEntryService implements IFormEntryService
{
    /**
     * @param Request|null $request
     * @param int $formId
     * @param IProfileUser $user
     * @param string|null $tags
     * @param callable $onProvide
     * @return FormEntry
     */
    public function updateFormEntry($request, $user, $formId, $tags = null, $onProvide = null)
    {
        /** @var Form */
        $form = Form::find($formId);

        if (is_null($form)) {
            throw new AppException(AppException::ERR_OBJECT_NOT_FOUND);
        }

        $inputNames = !is_null($request) ? $this->validateFormEntryRequestAndGetInputs($request, $form) : [];

        if (is_null($request)) {
            $domain = $user->getRegistrationDomain();
        } else {
            /** @var IDomainRepository */
            $domainRepo = app(IDomainRepository::class);
            $domain = $domainRepo->getRequestDomain($request);
        }
        $entry = null;
        $created = true;

        $entry = $this->resolveFormEntryRequest(
            $user,
            $form,
            $tags,
            true,
            $domain,
            function () use ($user, $form, $domain, $request, $onProvide, $inputNames, $tags) {
                if (is_null($onProvide)) {
                    if (is_null($request)) {
                        $values = [];
                    } else {
                        $values = $this->replaceBase64ImagesInInputs($request->all($inputNames));
                    }
                } else {
                    $values = $onProvide($request, $inputNames, $form, null);
                }

                return FormEntry::create([
                    'user_id' => $user->id,
                    'form_id' => $form->id,
                    'domain_id' => $domain->id,
                    'tags' => $tags,
                    'data' => [
                        'ip' => !is_null($request) ? $request->ip() : null,
                        'agent' => !is_null($request) ? $request->userAgent() : null,
                        'values' => $values,
                    ],
                    'flags' => 0,
                ]);
            },
            function ($entry) use ($form, $request, $onProvide, $inputNames, $tags, &$created) {
                if (is_null($onProvide)) {
                    if (is_null($request)) {
                        $values = [];
                    } else {
                        $values = $this->replaceBase64ImagesInInputs($request->all($inputNames));
                    }
                } else {
                    $values = $onProvide($request, $inputNames, $form, $entry);
                }
                $created = false;
                $entry->update([
                    'tags' => $tags,
                    'data' => [
                        'ip' => !is_null($request) ? $request->ip() : null,
                        'agent' => !is_null($request) ? $request->userAgent() : null,
                        'values' => $values,
                    ],
                ]);
                return $entry;
            }
        );
        FormEntryUpdateEvent::dispatch(
            $user,
            $domain,
            $entry,
            $form,
            $created,
            !is_null($request) ? $request->ip() : 'local',
            time()
        );
        Cache::tags(['user.form.' . $form->id . '.entry:' . $user->id])->flush();

        return $entry;
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param IProfileUser $user
     * @param int $formId
     * @param string $tags
     * @param callable $onProvide
     * @return void
     */
    public function updateUserFormEntryTag($request, $user, $formId, $tags, $onProvide = null)
    {
        /** @var Form */
        $form = Form::find($formId);

        if (is_null($form)) {
            throw new AppException(AppException::ERR_OBJECT_NOT_FOUND);
        }

        $inputNames = !is_null($request) ? $this->validateFormEntryRequestAndGetInputs($request, $form) : [];

        if (is_null($request)) {
            $domain = $user->getRegistrationDomain();
        } else {
            /** @var IDomainRepository */
            $domainRepo = app(IDomainRepository::class);
            $domain = $domainRepo->getRequestDomain($request);
        }

        $entry = null;
        $created = true;

        $entry = $this->resolveFormEntryRequest(
            $user,
            $form,
            $tags,
            false,
            $domain,
            function () use ($user, $form, $domain, $request, $onProvide, $inputNames, $tags) {
                if (is_null($onProvide)) {
                    if (is_null($request)) {
                        $values = [];
                    } else {
                        $values = $this->replaceBase64ImagesInInputs($request->all($inputNames));
                    }
                } else {
                    $values = $onProvide($request, $inputNames, $form, null);
                }
                return FormEntry::create([
                    'user_id' => $user->id,
                    'form_id' => $form->id,
                    'domain_id' => $domain->id,
                    'tags' => $tags,
                    'data' => [
                        'ip' => !is_null($request) ? $request->ip() : null,
                        'agent' => !is_null($request) ? $request->userAgent() : null,
                        'values' => $values
                    ],
                    'flags' => 0,
                ]);
            },
            function ($entry) use ($form, $request, $onProvide, $inputNames, $tags, &$created) {
                if (is_null($onProvide)) {
                    if (is_null($request)) {
                        $values = [];
                    } else {
                        $values = $this->replaceBase64ImagesInInputs($request->all($inputNames));
                    }
                } else {
                    $values = $onProvide($request, $inputNames, $form, null);
                }
                $created = false;
                $entry->update([
                    'tags' => $tags,
                    'data' => [
                        'ip' => !is_null($request) ? $request->ip() : null,
                        'agent' => !is_null($request) ? $request->userAgent() : null,
                        'values' => $values
                    ],
                ]);
                return $entry;
            }
        );
        FormEntryUpdateEvent::dispatch(
            $user,
            $domain,
            $entry,
            $form,
            $created,
            !is_null($request) ? $request->ip() : 'local',
            time()
        );
        Cache::tags(['user.form.' . $form->id . '.entry:' . $user->id])->flush();

        return $entry;
    }


    /**
     * Undocumented function
     *
     * @param array $values
     * @return array
     */
    public function replaceBase64ImagesInInputs($values) {
        $values = $this->replaceBase64WithFilepath($values, 'profile');
        $values = $this->replaceBase64WithFilepath($values, 'image');
        $values = $this->replaceBase64WithFilepath($values, 'melli_card', 'local', 'melli_cards');
        $unsets = [
            'p0',
            'submit',
            'cancel',
            'alert',
            'actions',
        ];
        foreach ($unsets as $unset) {
            if (isset($values[$unset])) {
                unset($values[$unset]);
            }
        }
        return $values;
    }
    /**
     * Undocumented function
     *
     * @param array $values
     * @param string $prop
     * @param string $disk
     * @param string $folder
     * @return array
     */
    protected function replaceBase64WithFilepath($values, $prop, $disk = 'public', $folder = 'avatars') {
        /** @var IFileUploadService */
        $this->fileService = app(IFileUploadService::class);
        if (isset($values[$prop]) && is_string($values[$prop])) {
            if (Str::startsWith($values[$prop], 'data:image/png;base64,')) {
                try {
                    $values[$prop] = '/storage/'.$this->fileService->saveBase64Image($values[$prop], $disk, $folder);
                } catch (Exception $e) {}
            }
        }

        return $values;
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

                if (!isset($fieldObj['exclude']) || !$fieldObj['exclude']) {
                    $inputNames[] = $fieldName;
                }

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
    protected function resolveFormEntryRequest($user, $form, $tags, $checkTags, $domain, $onCreateEntry, $onUpdateEntry)
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
