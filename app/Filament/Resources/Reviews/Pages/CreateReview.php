<?php

namespace App\Filament\Resources\Reviews\Pages;

use App\Filament\Resources\Reviews\ReviewResource;
use App\Models\User;
use App\Notifications\NewReviewSubmitted;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Notification;

class CreateReview extends CreateRecord
{
    protected static string $resource = ReviewResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        $data['client_id'] = $user->client_id;
        $data['user_id'] = $user->id;

        // A client/agent submission always starts pending review — only an
        // admin editing a record directly can set status/featured directly
        // (that path never reaches here, since this is the Create page).
        if (! $user->is_admin) {
            $data['status'] = 'pending';
            $data['is_featured'] = false;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $admins = User::where('is_admin', true)->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewReviewSubmitted($this->getRecord()));
        }
    }
}
