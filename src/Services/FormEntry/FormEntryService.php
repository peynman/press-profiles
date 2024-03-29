<?php

namespace Larapress\Profiles\Services\FormEntry;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Larapress\CRUD\Exceptions\AppException;
use Larapress\CRUD\Exceptions\ValidationException;
use Larapress\CRUD\Extend\Helpers;
use Larapress\FileShare\Models\FileUpload;
use Larapress\FileShare\Services\FileUpload\IFileUploadService;
use Larapress\Profiles\Models\Form;
use Larapress\Profiles\Models\FormEntry;

class FormEntryService implements IFormEntryService
{
    public function __construct(
        public IFileUploadService $fileService,
        public IFormContentProvider $contentProvider
    ) {
    }

    /**
     *
     * @param mixed $user
     * @param int $domainId
     * @param int|string|Form $form
     * @param string $ip
     * @param string $agent
     * @param array $data
     * @param string|null $tags
     * @param callable $onProvide
     * @return FormEntry
     */
    public function updateFormEntry(
        $user,
        $domainId,
        $form,
        $ip,
        $agent,
        $data = [],
        $tags = null,
        $onProvide = null
    ) {
        if (is_numeric($form)) {
            /** @var Form */
            $form = Form::find($form);
        } else if (is_string($form)) {
            $form = Form::where('name', $form)->first();
        }

        if (is_null($form)) {
            throw new AppException(AppException::ERR_OBJECT_NOT_FOUND);
        }

        $inputs = $this->getValidatedFormInputs($form, $data);
        $inputs = $this->fileService->replaceBase64WithFilePathValuesRecursive(
            $user,
            'form-'.$form.'-entry-user-'.$user->id,
            $inputs,
            null,
            FileUpload::ACCESS_PRIVATE,
            config('larapress.fileshare.default_private_disk'),
            'uploads',
        );

        $entry = null;
        $created = true;

        $entry = $this->resolveFormEntryRequest(
            $user,
            $form,
            $tags,
            true,
            $domainId,
            function () use (
                $user,
                $form,
                $domainId,
                $onProvide,
                $inputs,
                $tags,
                $ip,
                $agent
            ) {
                $inputs = is_null($onProvide) ? $inputs : $onProvide($inputs, $form, null);
                return FormEntry::create([
                    'user_id' => $user->id,
                    'form_id' => $form->id,
                    'domain_id' => $domainId,
                    'tags' => $tags,
                    'data' => [
                        'ip' => $ip,
                        'agent' => $agent,
                        'values' => $inputs,
                    ],
                    'flags' => 0,
                ]);
            },
            function ($entry) use (
                $form,
                $onProvide,
                $inputs,
                $tags,
                $ip,
                $agent,
                &$created,
            ) {
                $inputs = is_null($onProvide) ? $inputs : $onProvide($inputs, $form, $entry);
                $created = false;

                $entry->update([
                    'tags' => $tags,
                    'data' => [
                        'ip' => $ip,
                        'agent' => $agent,
                        'values' => $inputs,
                    ],
                ]);
                return $entry;
            }
        );

        FormEntryUpdateEvent::dispatch(
            $user,
            $domainId,
            $entry,
            $form,
            $created,
            $ip,
            time()
        );

        Helpers::forgetCachedValues(['user.form.' . $form->id . '.entry:' . $user->id]);

        return $entry;
    }

    /**
     * Undocumented function
     *
     * @param Form $form
     *
     * @return array
     */
    public function getFormValidationRules(Form $form): array
    {
        return $this->contentProvider->getFormRules($form);
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param Form $form
     *
     * @return array
     */
    public function getValidatedFormInputs(Form $form, $inputs): array
    {
        $rules = $this->getFormValidationRules($form);
        $validInputs = $this->contentProvider->getFormValidInputs($form, $inputs);
        $validate = Validator::make($validInputs, $rules);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }

        return $validInputs;
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
        if (is_object($form)) {
            $form = $form->id;
        }
        if (is_object($domain)) {
            $domain = $domain->id;
        }
        if (is_object($user)) {
            $user = $user->id;
        }

        if (is_null($user)) {
            // handler open (no user) forms
            return $onCreateEntry();
        } else {
            $entry = FormEntry::query()
                ->where('user_id', $user)
                ->where('form_id', $form)
                ->where('domain_id', $domain);

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
