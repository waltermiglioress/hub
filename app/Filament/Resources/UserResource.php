<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\City;
use App\Models\State;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'Utente';
    protected static ?string $navigationGroup = 'Setting';
    protected static ?string $pluralModelLabel = 'Utenti';

    protected static ?string $navigationLabel = 'Utenti';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nome'),
                TextInput::make('surname')
                    ->required()
                    ->maxLength(255)
                    ->label('Cognome'),
                TextInput::make('email')
                    ->required()
                    ->email()
                    ->label('Email'),
                TextInput::make('tel')
                    ->required()
                    ->numeric()
                    ->label('Mobile'),
                TextInput::make('avatar')
                    ->nullable()
                    ->label('Avatar'),
                TextInput::make('cf')
                    ->nullable()
                    ->label('Codice fiscale'),
                Select::make('country_id')
                    ->label('Paese')
                    ->relationship('country','name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Set $set){
                        $set('state_id',null);
                        $set('city_id',null);
                    })
                    ->required(),
                Select::make('state_id')
                    ->label('Regione/Provincia')
                    ->options(fn(Get $get): Collection =>State::query()
                        ->where('country_id', $get('country_id'))
                        ->pluck('name','id'))
//                    ->relationship('state','name')
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(fn(Set $set) => $set('city_id',null))
                    ->preload()
                    ->required(),

                Select::make('city_id')
                    ->label('Città')
                    ->options(fn(Get $get): Collection =>City::query()
                        ->where('state_id', $get('state_id'))
                        ->pluck('name','id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required(),
                TextInput::make('address')
                    ->label('Indirizzo'),
                TextInput::make('cap')
                    ->label('CAP')
                    ->numeric(),

                TextInput::make('password')
                    ->dehydrateStateUsing(fn(string $state):string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->label('Password')
                    ->password(),

                Select::make('roles')
                    ->multiple()
                    ->relationship('roles','name'),

                Select::make('projects')
                    ->relationship('projects','code')
                    ->label('Commesse')
                    ->searchable()
                    ->preload()
                    ->multiple()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                TextColumn::make('surname')
                    ->label('Cognome')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Data Creazione')
                    ->date('d-m-Y')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
