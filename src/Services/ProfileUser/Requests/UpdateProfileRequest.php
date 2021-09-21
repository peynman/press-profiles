<?php

namespace Larapress\Profiles\Services\ProfileUser\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Form;
use Larapress\Profiles\Services\FormEntry\IFormEntryService;

/**
 */
class UpdateProfileRequest extends FormRequest
{
    /** @var Form */
    protected $form;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var IProfileUser */
        $user = Auth::user();

        if (is_null($user)) {
            return false;
        }

        $highRoleName = $user->getUserHighestRole()->name;
        $profileFormId = config('larapress.profiles.form_role_profiles.'.$highRoleName);
        if (is_null($profileFormId)) {
            $profileFormId = config('larapress.profiles.default_profile_form_id');
        }

        $this->form = Form::find($profileFormId);

        return !is_null($this->form);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var IFormEntryService */
        $formService = app(IFormEntryService::class);
        return $formService->getFormValidationRules($this->form);
    }

    /**
     * Undocumented function
     *
     * @return Form
     */
    public function getProfileForm() {
        return $this->form;
    }
}
