<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

trait HandleAttachments
{
    protected function afterCreate(): void
    {
        $data = $this->form->getRawState();
        $record = $this->record;
        $attachments = $data['attachments'] ?? [];
        $this->handleAttachments($record, $attachments);
    }

    protected function afterSave(): void
    {
        $data = $this->form->getRawState();
        $record = $this->record;
        $attachments = $data['attachments'] ?? [];
        $this->handleAttachments($record, $attachments);
    }



//    public function handleAttachments(Model $record, array $attachments): void
//    {
//        // Retrieve existing attachments
//        $existingAttachments = $record->attachments()->get();
//
//        // Associate new attachments
//        foreach ($attachments as $path) {
//
//            $filename = basename($path);
//            // Check if the attachment is already associated
//            $existingAttachment = $existingAttachments->where('filename', $path)->first();
//
//            if (!$existingAttachment) {
//                // If not, associate the new attachment
//                $record->attachments()->create([
//                    'filename' => $filename,
//                    'path'=> $path
//                ]);
//            }
//        }
//
//        // Detach attachments that are not present in the new set
//        $attachmentsToRemove = $existingAttachments->reject(function ($attachment) use ($attachments) {
//            return in_array($attachment->path, $attachments);
//        });
//
//        foreach ($attachmentsToRemove as $attachment) {
//            $attachment->delete();
//        }
//    }

    public function handleAttachments(Model $record, array $attachments): void
    {
        // Retrieve existing attachments from the database
        $existingAttachments = $record->attachments()->get();

        // Associate new attachments
        foreach ($attachments as $path) {
            $filename = basename($path);

            // Check if the attachment is already associated
            $existingAttachment = $existingAttachments->where('filename', $filename)->first();

            if (!$existingAttachment) {
                // If not, associate the new attachment
                $record->attachments()->create([
                    'filename' => $filename,
                    'path' => $path,
                ]);
            }
        }

        // Detach and remove attachments that are not present in the new set
        $attachmentsToRemove = $existingAttachments->reject(function ($attachment) use ($attachments) {
            return in_array($attachment->path, $attachments);
        });

        foreach ($attachmentsToRemove as $attachment) {
            // Remove the attachment from the file system
            Storage::disk('attachment')->delete($attachment->path);

            // Delete the attachment record from the database
            $attachment->delete();
        }
    }

}
