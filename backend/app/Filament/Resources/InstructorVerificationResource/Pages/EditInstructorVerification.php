<?php

namespace App\Filament\Resources\InstructorVerificationResource\Pages;

use App\Filament\Resources\InstructorVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInstructorVerification extends EditRecord
{
    protected static string $resource = InstructorVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
