<?php

namespace App\Filament\Pages;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Pages\Auth\Login;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class CustomLogin extends Login
{
    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('ni')
                ->label('Numeru Identifikasaun')
                ->required()
                ->autocomplete(),
            TextInput::make('password')
                ->label('Password')
                ->password()
                ->required()
                ->autocomplete(),
          
        ]);
    
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'ni' => $data['ni'],
            'password' => $data['password'],
        ];
    }

    public function getUsername(): string
    {
        return 'ni';
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.ni' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

}