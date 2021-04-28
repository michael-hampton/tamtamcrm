<?php

namespace App\Mail\Admin;

use App\Models\Deal;
use App\Models\User;
use App\Traits\Money;
use App\ViewModels\AccountViewModel;
use App\ViewModels\CustomerViewModel;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class EntityCreated extends AdminMailer
{
    use Queueable, SerializesModels, Money;

    /**
     * EntityCreated constructor.
     * @param $entity
     * @param $entity_string
     * @param User $user
     */
    public function __construct($entity, $entity_string, User $user)
    {
        parent::__construct($entity_string . '_created', $entity);

        $this->entity_string = $entity_string;
        $this->entity = $entity;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return void
     */
    public function build()
    {
        $data = $this->getData();
        $this->setSubject($data);
        $this->setMessage($data);
        //$this->buildButton();
        $this->execute();
    }

    /**
     * @return array
     */
    private function getData(): array
    {
        return [
            'total'              => $this->entity->getFormattedTotal(),
            $this->entity_string => $this->entity->getNumber(),
            'customer'           => (new CustomerViewModel($this->entity->customer))->name(),
        ];
    }

    /**
     * @return array
     */
    private function buildButton(): void
    {
        $this->button = [
            'url'         => config('taskmanager.web_url') . '/#/deals?id=' . $this->entity->id,
            'button_text' => trans('texts.view_' . $this->entity_string),
        ];
    }
}
