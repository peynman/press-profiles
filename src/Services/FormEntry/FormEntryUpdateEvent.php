<?php

namespace Larapress\Profiles\Services\FormEntry;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FormEntryUpdateEvent implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    /** @var \Larapress\Profiles\IProfileUser */
    public $user;
    /** @var \Larapress\Profiles\Models\Domain */
    public $domain;
    /** @var string */
    public $ip;
    /** @var int */
    public $timestamp;
    /** @var \Larapress\Profiles\Models\FormEntry */
    public $entry;
    /** @var \Larapress\Profiles\Models\Form */
    public $form;
    /** @var boolean */
    public $created;

    /**
     * Create a new event instance.
     *
     * @param $user
     * @param $domain
     * @param $entry
     * @param $form
     * @param $ip
     * @param $timestamp
     */
    public function __construct($user, $domain, $entry, $form, $created, $ip, $timestamp)
    {
        $this->user = $user;
        $this->domain = $domain;
        $this->ip = $ip;
        $this->timestamp = $timestamp;
        $this->form = $form;
        $this->entry = $entry;
        $this->created = $created;
    }
}
