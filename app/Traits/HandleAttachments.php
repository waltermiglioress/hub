<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

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

//    protected function handleAttachments(Model $record, array $attachments): void
//    {
//        // Logica per gestire gli allegati
//        $existingAttachments = $record->attachments()->pluck('filename')->toArray();
//
//        $attachmentsToAdd = array_diff($attachments, $existingAttachments);
//        $attachmentsToRemove = array_diff($existingAttachments, $attachments);
//
//        foreach ($attachmentsToAdd as $path) {
//            $record->attachments()->create([
//                'filename' => basename($path),
//                'path' => $path,
//            ]);
//        }
//
//        if (!empty($attachmentsToRemove)) {
//            $record->attachments()
//                ->whereIn('filename', $attachmentsToRemove)
//                ->get()
//                ->each(function ($attachment) {
//                    $attachment->delete();
//                });
//        }
//    }

    public function handleAttachments(Model $record, array $attachments): void
    {
        // Retrieve existing attachments
        $existingAttachments = $record->attachments()->get();

        // Associate new attachments
        foreach ($attachments as $path) {

            $filename = basename($path);
            // Check if the attachment is already associated
            $existingAttachment = $existingAttachments->where('filename', $path)->first();

            if (!$existingAttachment) {
                // If not, associate the new attachment
                $record->attachments()->create([
                    'filename' => $filename,
                    'path'=> $path
                ]);
            }
        }

        // Detach attachments that are not present in the new set
        $attachmentsToRemove = $existingAttachments->reject(function ($attachment) use ($attachments) {
            return in_array($attachment->path, $attachments);
        });

        foreach ($attachmentsToRemove as $attachment) {
            $attachment->delete();
        }
    }
}
