<?php

namespace Larapress\Profiles\Services\ProfileUser\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 */
class AddressModifyRequest extends FormRequest
{
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
        $required = config('larapress.profiles.security.addresses');
        $getRulesForField = function ($field, $extras = '') use($required) {
            if (in_array($field, $required)) {
                return 'required|'.$extras;
            }
            return 'nullable|'.$extras;
        };
        return [
            'country_code' => $getRulesForField('country_code', 'numeric'),
            'city_code' => $getRulesForField('city_code', 'numeric'),
            'province_code' => $getRulesForField('province_code', 'numeric'),
            'postal_code' => $getRulesForField('postal_code', 'numeric'),
            'address' => $getRulesForField('address', 'string'),
            'location.lat' => $getRulesForField('location', 'numeric'),
            'location.lng' => $getRulesForField('location', 'numeric'),
        ];
    }

    /**
     * Undocumented function
     *
     * @return int
     */
    public function getCountryCode()
    {
        return $this->get('country_code');
    }

    /**
     * Undocumented function
     *
     * @return int
     */
    public function getProvinceCode()
    {
        return $this->get('province_code');
    }

    /**
     * Undocumented function
     *
     * @return int
     */
    public function getCityCode()
    {
        return $this->get('city_code');
    }

    /**
     * Undocumented function
     *
     * @return int
     */
    public function getPostalCode()
    {
        return $this->get('postal_code');
    }

    /**
     * Undocumented function
     *
     * @return int
     */
    public function getAddress()
    {
        return $this->get('address');
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getLocation()
    {
        return $this->get('location');
    }
}
