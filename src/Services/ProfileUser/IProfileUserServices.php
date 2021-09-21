<?php

namespace Larapress\Profiles\Services\ProfileUser;

use Illuminate\Http\Request;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Services\ProfileUser\Requests\AddressModifyRequest;
use Larapress\Profiles\Services\ProfileUser\Requests\UpdateProfileRequest;

interface IProfileUserServices {

    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     * @param AddressModifyRequest $request
     *
     * @return PhysicalAddress
     */
    public function addAddress(IProfileUser $user, AddressModifyRequest $request);


    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     * @param AddressModifyRequest $request
     * @param int|PhysicalAddress $address
     *
     * @return PhysicalAddress
     */
    public function updateAddress(IProfileUser $user, AddressModifyRequest $request, $address);


    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     * @param int   $address
     *
     * @return void
     */
    public function removeAddress(IProfileUser $user, $address);

    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     *
     * @return array
     */
    public function userDetails(IProfileUser $user);

    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     * @param UpdateProfileRequest $request
     *
     * @return array
     */
    public function updateDetails(IProfileUser $user, UpdateProfileRequest $request);

    /**
     * Undocumented function
     *
     * @param IProfileUser|Model $user
     * @param string $old
     * @param string $new
     *
     * @return array
     */
    public function updatePassword($user, string $old, string $new);
}
