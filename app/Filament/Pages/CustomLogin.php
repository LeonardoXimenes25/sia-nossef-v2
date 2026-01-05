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
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;


class CustomLogin extends Login
{
    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('login_id')
                ->label('Numeru Identifikasaun')
                ->placeholder('Prenxe ita nia numeru identifikasaun')
                ->required()
                ->autocomplete('off')
                ->extraInputAttributes([
                    'inputmode' => 'numeric',           // keyboard angka (mobile)
                    'pattern' => '[0-9]*',               // hint angka
                    'onwheel' => 'this.blur()',          // cegah scroll
                    'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                ])
                ->rules([
                    'required',
                    'digits_between:1,20',
                ]),


            TextInput::make('password')
                ->label('Password')
                ->placeholder('Prenxe ita nia password')
                ->password()
                ->required()
                ->autocomplete('off'),
        ]);
    
    }

    public function getHeading(): string | Htmlable
{
    return new HtmlString(
        '<div class="text-center mb-4">
            <img 
                src="' . asset('assets/img/nossef-logo.png') . '" 
                alt="Logo"
                style="height:80px; margin:0 auto;"
            >
            <div style="margin-top:10px; font-size:1.25rem; font-weight:700;">
                Login
            </div>
        </div>'
    );
}

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'login_id' => $data['login_id'],
            'password' => $data['password'],
        ];
    }

    public function getUsername(): string
    {
        return 'login_id';
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login_id' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

}