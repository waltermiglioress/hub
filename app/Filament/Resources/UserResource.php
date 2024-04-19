<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\City;
use App\Models\State;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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
                FileUpload::make('avatar')
                    ->label('Logo')
                    ->preserveFilenames()
                    ->disk('avatar')
                    ->image()
                    ->avatar()
                    ->openable()
                    ->imageEditor()
                    ->circleCropper()
                    ->columnSpanFull(),
                Section::make('Dati Generali')
                    ->aside()
                    ->description('Compilare i dati generali dell\'utente ricordando l\'obbligatoriertà dei campi nome,cognome,email,password. Il campo password deve essere un campo alfa numerico di lunghezza minima di 8 caratteri avente un simbolo.')
                    ->columns(2)
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
                            ->unique()
                            ->validationMessages([
                                'unique' => 'Il campo :attribute esiste già!',
                            ])
                            ->email()
                            ->label('Email'),
                        TextInput::make('tel')
                            ->numeric()
                            ->label('Mobile'),

                        TextInput::make('cf')
                            ->nullable()
                            ->label('Codice fiscale'),
                        TextInput::make('password')
                            ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                            ->dehydrated(fn(?string $state): bool => filled($state))
                            ->label('Password')
                            ->rules([Password::min(8)->symbols()->mixedCase()->numbers()])
//                    ->rules(['alpha_num:ascii','min:8'])
                            ->validationMessages([
                                'min' => 'La lunghezza del campo :attribute deve essere di almeno :value.',
                                'alpha_num:ascii' => 'La :attribute deve avere caratteri alfa numerici',
                            ])
                            ->required(fn(string $context): bool => $context === 'create')
                            ->revealable()
                            ->password(),
                    ]),

                Section::make('Indirizzi')
                    ->description('Inserire i dati relativi l\'anagrafica dell\'utente')
                    ->columns(2)
                    ->aside()
                    ->schema([
                        Select::make('country_id')
                            ->label('Paese')
                            ->relationship('country', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('state_id', null);
                                $set('city_id', null);
                            }),
                        Select::make('state_id')
                            ->label('Regione/Provincia')
                            ->options(fn(Get $get): Collection => State::query()
                                ->where('country_id', $get('country_id'))
                                ->pluck('name', 'id'))
//                    ->relationship('state','name')
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn(Set $set) => $set('city_id', null))
                            ->preload(),

                        Select::make('city_id')
                            ->label('Città')
                            ->options(fn(Get $get): Collection => City::query()
                                ->where('state_id', $get('state_id'))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live(),
                        TextInput::make('cap')
                            ->label('CAP')
                            ->numeric(),
                        TextInput::make('address')
                            ->label('Indirizzo')
                            ->columnSpanFull(),
                    ]),


                Section::make('Dati relativi la piattaforma')
                    ->description('Inserire il ruolo e assegnare le commesse all\'utente. L\'assegnazione permetterà all\'utente di visionare i dati relativi le proprie commesse.')
                    ->aside()
                    ->columns(1)
                    ->schema([
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required(),
                        CheckboxList::make('projects')
                            ->relationship('projects', 'code')
                            ->label('Commesse')
                            ->selectAllAction(
                                fn(Action $action) => $action->label('Seleziona tutto'))
                            ->columnSpanFull()->columns(6)
                            ->bulkToggleable()
                            ->searchable()
                            ->required(),
                    ]),


//                Select::make('projects')
//                    ->relationship('projects','code')
//                    ->label('Commesse')
//                    ->searchable()
//                    ->preload()
//                    ->multiple()
//                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->circular(),
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                TextColumn::make('surname')
                    ->label('Cognome')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->badge()
                    ->color('success')
                    ->label('Ruolo'),
                TextColumn::make('tel')
                    ->label('Mobile')
                    ->searchable(),
                TextColumn::make('projects.code')
                    ->searchable()
                    ->label('Commesse')
                    ->badge()
                    ->expandableLimitedList(true)
                    ->limitList(25),

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
