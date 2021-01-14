<?php

namespace App\Components\Mail;

class BaseMailHandler
{
    public function storeAttachments(Stream $stream, Email $email)
    {
        $attachments = [];

        foreach ($email->attachments() as $attachment) {
            $attachments[] = $stream->addDocuments($this->attach($stream, $attachment));
        }

        return $attachments;
    }

    /**
     * Stores the attachments on the server
     * @param Stream $stream
     * @param $attachment
     * @return array ['name', 'path']
     */
    public function attach(Stream $stream, $attachment)
    {
        $filename = $this->getFileName($attachment);

        $this->storeFileOnServer($stream, $filename, $attachment);

        return [
            'name' => $filename,
            'path' => $stream->token . '/' . $filename
        ];
    }

    /**
     * Generates a filename for our attachment.
     * @param $attachment
     * @return string (filename.ext)
     */
    public function getFileName($attachment)
    {
        return uniqid() . '.' . File::extension($attachment->getFilename());
    }

    /**
     * Stores the actual file: streamtoken/filename.ext
     * @param Stream $stream
     * @param $filename
     * @param $attachment
     * @return boolean
     */
    public function storeFileOnServer(Stream $stream, $filename, $attachment)
    {
        return Storage::put($stream->token . '/' . $filename, $attachment->getContent());
    }
}
