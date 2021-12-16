<?php

namespace Larapress\Profiles\Services\ProfileUser;

use Illuminate\Support\Facades\Hash;
use Larapress\CRUD\Exceptions\RequestException;
use Larapress\Profiles\IProfileUser;
use Illuminate\Database\Eloquent\Model;
use Larapress\CRUD\Exceptions\AppException;
use Larapress\CRUD\Extend\Helpers;
use Larapress\CRUD\Services\CRUD\ICRUDService;
use Larapress\FileShare\Models\FileUpload;
use Larapress\FileShare\Services\FileUpload\IFileUploadService;
use Larapress\Profiles\Services\FormEntry\IFormEntryService;
use Larapress\Profiles\Services\ProfileUser\Requests\AddressModifyRequest;
use Larapress\Profiles\Services\ProfileUser\Requests\UpdateProfileRequest;
use Larapress\Profiles\Models\PhysicalAddress;

class ProfileUserServices implements IProfileUserServices
{
    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     * @param AddressModifyRequest $request
     *
     * @return PhysicalAddress
     */
    public function addAddress(IProfileUser $user, AddressModifyRequest $request)
    {
        return PhysicalAddress::create([
            'user_id' => $user->id,
            'domain_id' => $user->getMembershipDomainId(),
            'country_code' => $request->getCountryCode(),
            'province_code' => $request->getProvinceCode(),
            'city_code' => $request->getCityCode(),
            'postal_code' => $request->getPostalCode(),
            'address' => $request->getAddress(),
            'data' => [
                'location' => $request->getLocation(),
            ],
        ]);
    }

    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     * @param AddressModifyRequest $request
     * @param int|PhysicalAddress $address
     *
     * @return PhysicalAddress
     */
    public function updateAddress(IProfileUser $user, AddressModifyRequest $request, $address)
    {
        if (is_numeric($address)) {
            /** @var PhysicalAddress */
            $address = PhysicalAddress::find($address);
        }

        if (is_null($address) || $user->id !== $address->user_id) {
            throw new AppException(AppException::ERR_ACCESS_DENIED);
        }

        $address->update([
            'country_code' => $request->getCountryCode(),
            'province_code' => $request->getProvinceCode(),
            'city_code' => $request->getCityCode(),
            'postal_code' => $request->getPostalCode(),
            'address' => $request->getAddress(),
            'data' => [
                'location' => $request->getLocation(),
            ],
        ]);

        return $address;
    }


    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     * @param int   $address
     *
     * @return void
     */
    public function removeAddress(IProfileUser $user, $address)
    {
        if (is_numeric($address)) {
            /** @var PhysicalAddress */
            $address = PhysicalAddress::find($address);
        }

        if (is_null($address) || $user->id !== $address->user_id) {
            throw new AppException(AppException::ERR_ACCESS_DENIED);
        }

        $address->delete();

        return $address;
    }

    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     *
     * @return array
     */
    public function userDetails(IProfileUser $user)
    {
        /** @var ICRUDService */
        $crudService = app(ICRUDService::class);
        $provider = $crudService->makeCompositeProvider(config('larapress.crud.user.provider'));
        $with = $provider->getDefaultShowRelations();
        if (Helpers::isAssocArray($with)) {
            $with = array_keys($with);
        }

        /** @var Model $user */
        $user->load($with);

        $user['permissions'] = $user->getPermissions();

        return $user;
    }

    /**
     * Undocumented function
     *
     * @param IProfileUser|Model $user
     * @param string $old
     * @param string $new
     * @return void
     */
    public function updatePassword($user, string $old, string $new)
    {
        if (Hash::check($old, $user->password)) {
            $user->update([
                'password' => Hash::make($new),
            ]);
        } else {
            throw new RequestException(trans('larapress::auth.exceptions.invalid_password'));
        }
    }

    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     * @param Request $request
     *
     * @return array
     */
    public function updateDetails(IProfileUser $user, UpdateProfileRequest $request) {
        /** @var IFormEntryService */
        $formService = app(IFormEntryService::class);
        /** @var IFileUploadService */
        $fileService = app(IFileUploadService::class);

        $values = $request->all();
        $values = $fileService->replaceBase64WithFilePathValuesRecursive(
            $user,
            'user-'.$user->id.'-profile-pic',
            $values,
            'profilePic',
            FileUpload::ACCESS_PUBLIC,
            config('larapress.fileshare.default_public_disk'),
            'images/avatars',
        );

        $formService->updateFormEntry(
            $user,
            $user->getMembershipDomainId(),
            $request->getProfileForm(),
            $request->getClientIp(),
            $request->userAgent(),
            $values,
        );

        return $this->userDetails($user);
    }

    public function getAddressString(PhysicalAddress $address) {}
}
