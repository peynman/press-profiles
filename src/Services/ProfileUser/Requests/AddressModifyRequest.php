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
        return [
            'country_code' => 'required|numeric',
            'city_code' => 'required|numeric',
            'province_code' => 'required|numeric',
            'postal_code' => 'required|numeric',
            'address' => 'required|string',
            'location.lat' => 'required|numeric',
            'location.lng' => 'required|numeric',
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
