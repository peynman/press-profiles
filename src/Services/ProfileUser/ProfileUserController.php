<?php

namespace Larapress\Profiles\Services\ProfileUser;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Larapress\Profiles\IProfileUser;
use Illuminate\Http\Response;
use Larapress\Profiles\Services\ProfileUser\Requests\AddressModifyRequest;
use Larapress\Profiles\Services\ProfileUser\Requests\UpdatePasswordRequest;
use Larapress\Profiles\Services\ProfileUser\Requests\UpdateProfileRequest;

class ProfileUserController extends Controller
{
    public static function registerApiRoutes()
    {
        Route::post('me', '\\' . self::class . '@meQuery')
            ->name('me.any.query');

        Route::post('me/update-password', '\\' . self::class . '@updatePassword')
            ->name('me.any.update-password');

        Route::post('me/profile', '\\' . self::class . '@updateDetails')
            ->name('me.any.update-profile');

        Route::post('me/profile/add-address', '\\' . self::class . '@addAddress')
            ->name('me.any.add-address');

        Route::post('me/profile/update-address/{id}', '\\' . self::class . '@updateAddress')
            ->name('me.any.update-address');

        Route::delete('me/profile/remove-address/{id}', '\\' . self::class . '@removeAddress')
            ->name('me.any.remove-address');
    }

    /**
     * Undocumented function
     *
     * @param IProfileUserServices $service
     * @param ProfileUserQueryRequest $request
     *
     * @return Response
     */
    public function meQuery(IProfileUserServices $service)
    {
        /** @var IProfileUser */
        $user = Auth::user();
        return $service->userDetails($user);
    }

    /**
     * Undocumented function
     *
     * @param IProfileUserServices $service
     * @param UpdatePasswordRequest $request
     *
     * @return Response
     */
    public function updatePassword(IProfileUserServices $service, UpdatePasswordRequest $request)
    {
        $service->updatePassword(Auth::user(), $request->getOldPassword(), $request->getNewPassword());
        return [
            'message' => trans('larapress::profiles.messages.password_update_success'),
        ];
    }

    /**
     * Undocumented function
     *
     * @param IProfileUserServices $service
     * @param UpdateProfileRequest $request
     *
     * @return Response
     */
    public function updateDetails(IProfileUserServices $service, UpdateProfileRequest $request)
    {
        /** @var IProfileUser */
        $user = Auth::user();
        return [
            'message' => trans('larapress::profiles.messages.profile_updated'),
            'user' => $service->updateDetails($user, $request),
        ];
    }

    /**
     * Undocumented function
     *
     * @param IProfileUserServices $service
     * @param AddressModifyRequest $request
     *
     * @return Response
     */
    public function addAddress(IProfileUserServices $service, AddressModifyRequest $request) {
        /** @var IProfileUser */
        $user = Auth::user();
        return $service->addAddress($user, $request);
    }

    /**
     * Undocumented function
     *
     * @param IProfileUserServices $service
     * @param AddressModifyRequest $request
     * @param int $id
     *
     * @return Response
     */
    public function updateAddress(IProfileUserServices $service, AddressModifyRequest $request, $id) {
        /** @var IProfileUser */
        $user = Auth::user();
        return $service->updateAddress($user, $request, $id);
    }

    /**
     * Undocumented function
     *
     * @param IProfileUserServices $service
     * @param int $id
     *
     * @return Response
     */
    public function removeAddress(IProfileUserServices $service, $id) {
        /** @var IProfileUser */
        $user = Auth::user();
        return $service->removeAddress($user, $id);
    }
}
