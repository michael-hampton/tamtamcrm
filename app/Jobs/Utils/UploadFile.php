<?php
//https://laracasts.com/discuss/channels/laravel/how-to-store-and-show-image-fields-with-file-type-file-size-and-with-dimensions?page=1

namespace App\Jobs\Utils;

use App\Events\Uploads\FileWasUploaded;
use App\Factory\NotificationFactory;
use App\Models\Account;
use App\Models\File;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\Facades\Image;

class UploadFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $entity;
    protected $file;
    /**
     * @var User
     */
    protected User $user;
    /**
     * @var Account
     */
    protected Account $account;
    protected $uploaded_by_customer;
    protected $customer_can_view;

    /**
     * UploadFile constructor.
     * @param $file
     * @param User $user
     * @param Account $account
     * @param $entity
     * @param bool $uploaded_by_customer
     * @param bool $customer_can_view
     */
    public function __construct(
        $file,
        User $user,
        Account $account,
        $entity,
        $uploaded_by_customer = false,
        $customer_can_view = false
    ) {
        $this->file = $file;
        $this->user = $user;
        $this->account = $account;
        $this->entity = $entity;
        $this->uploaded_by_customer = $uploaded_by_customer;
        $this->customer_can_view = $customer_can_view;
    }

    /**
     * Execute the job.
     *
     * @return File|null
     */
    public function handle(): ?File
    {
        $extension = $this->file->getClientOriginalExtension();
        $originalname = time() . '.' . $extension;
        $destinationPath = 'storage/' . $this->account->id . '/uploads/';
        $path = $this->file->move(public_path($destinationPath), $originalname);

        $imgsizes = $path->getSize();
        $data = getimagesize($path);

        $width = !empty($data) ? $data[0] : null;
        $height = !empty($data) ? $data[1] : null;
        $preview = !empty($data) ? $this->makePreview($path) : null;

        //Start Store in Database
        $file = new File;
        $file->preview = $preview;
        $file->user_id = $this->user->id;
        $file->account_id = $this->account->id;
        $file->file_path = $destinationPath . $originalname;
        $file->size = $imgsizes;
        $file->name = $originalname;
        $file->type = $extension;
        $file->width = $width;
        $file->height = $height;
        $file->uploaded_by_customer = $this->uploaded_by_customer;
        $file->customer_can_view = $this->customer_can_view;

        $this->entity->files()->save($file);

        // create notification
        $notification = NotificationFactory::create($this->account->id, $this->user->id);
        $notification->entity_id = $file->id;
        $notification->type = 'App\Notifications\AttachmentCreated';
        $notification->data = json_encode(
            [
                'id'       => $file->id,
                'message'  => 'A new file has been uploaded',
                'filename' => $file->name
            ]
        );
        $notification->save();

        event(new FileWasUploaded($file));

        return $file;
    }

    private function makePreview($file)
    {
        //https://quickadminpanel.com/blog/file-upload-in-laravel-the-ultimate-guide/
        $extension = $this->file->getClientOriginalExtension();
        $originalname = time() . '-thumb.' . $extension;
        $destinationPath = 'storage/' . $this->account->id . '/uploads/';
        Image::make($file)->resize(300, 200)->save($destinationPath . $originalname);
        return $destinationPath . $originalname;
    }
}
