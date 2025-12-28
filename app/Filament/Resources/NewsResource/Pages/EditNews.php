<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Resources\Pages\EditRecord;

class EditNews extends EditRecord
{
    protected static string $resource = NewsResource::class;
    protected static ?string $title = 'Edita Notisia';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
