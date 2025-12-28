<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;
    protected static ?string $title = 'Kria Notisia';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
