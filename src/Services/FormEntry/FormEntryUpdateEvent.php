<?php

namespace Larapress\Profiles\Services\FormEntry;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Larapress\ECommerce\IECommerceUser;
use Larapress\Profiles\Models\Form;
use Larapress\Profiles\Models\FormEntry;

class FormEntryUpdateEvent implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    /** @var \Larapress\Profiles\IProfileUser */
    public $userId;
    /** @var \Larapress\Profiles\Models\Domain */
    public $domainId;
    /** @var string */
    public $ip;
    /** @var int */
    public $timestamp;
    /** @var \Larapress\Profiles\Models\FormEntry */
    public $entryId;
    /** @var \Larapress\Profiles\Models\Form */
    public $formId;
    /** @var boolean */
    public $created;

    /**
     * Create a new event instance.
     *
     * @param IECommerceUser $user
     * @param Domain $domain
     * @param FormEntry $entry
     * @param Form $form
     * @param string $ip
     * @param $timestamp
     */
    public function __construct($user, $domain, $entry, $form, $created, $ip, $timestamp)
    {
        $this->userId = is_numeric($user) ? $user : $user->id;
        $this->supportId = $user->getSupportUserId();
        $this->domainId = is_numeric($domain) || is_null($domain) ? $domain : $domain->id;
        $this->formId = is_numeric($form) || is_null($form) ? $form : $form->id;
        $this->entryId = is_numeric($entry) || is_null($entry) ? $entry : $entry->id;
        $this->ip = $ip;
        $this->timestamp = $timestamp;
        $this->created = $created;
    }


    public function getUser(): IECommerceUser {
        return call_user_func([config('larapress.crud.user.class'), "find"], $this->userId);
    }

    public function getForm() : Form {
        return Form::find($this->formId);
    }

    public function getFormEntry() : FormEntry {
        return FormEntry::find($this->entryId);
    }
}
