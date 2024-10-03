<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CliForResource\Pages;

use App\Models\City;
use App\Models\CliFor;


use App\Models\State;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class CliForResource extends Resource
{
    protected static ?string $model = CliFor::class;

    protected static ?string $modelLabel = 'Cliente Fornitore';

    protected static ?string $pluralModelLabel = 'Clienti Fornitori';

    protected static ?string $navigationGroup = 'Anagrafiche';

//    protected static ?string $navigationLabel = 'Clienti/Fornitori';

    protected static ?string $navigationIcon = 'heroicon-o-users';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dati generali')
                    ->schema([
                        TextInput::make('name')
                            ->label('Ragione sociale')
                            ->required()
                            ->columnSpan(2),
                        TextInput::make('piva')
                            ->label('P.IVA')
                            ->unique(ignoreRecord: true)
                            ->numeric()
                            ->maxLength(11)
                            ->required(),
                        TextInput::make('CF')
                            ->label('Codice Fiscale'),
                        ToggleButtons::make('is_client')
                            ->label('È cliente?')
                            ->boolean()
                            ->grouped()
                            ->required(),
                        ToggleButtons::make('is_supplier')
                            ->label('È fornitore?')
                            ->boolean()
                            ->grouped()
                            ->required(),

                        Fieldset::make('Indirizzi')
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
                                    })
                                    ->required(),
                                Select::make('state_id')
                                    ->label('Regione/Provincia')
                                    ->options(fn(Get $get): Collection => State::query()
                                        ->where('country_id', $get('country_id'))
                                        ->pluck('name', 'id'))
//                    ->relationship('state','name')
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(fn(Set $set) => $set('city_id', null))
                                    ->preload()
                                    ->required(),

                                Select::make('city_id')
                                    ->label('Città')
                                    ->options(fn(Get $get): Collection => City::query()
                                        ->where('state_id', $get('state_id'))
                                        ->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->required(),
                                TextInput::make('cap')
                                    ->label('CAP')
                                    ->numeric(),
                                TextInput::make('address')
                                    ->label('Indirizzo')
                                    ->columnSpanFull(),

                            ]),
                    ])->columnSpan(2)->columns(3),

                Section::make('Altri dati')->schema([
                    FileUpload::make('avatar')
                        ->label('Logo')
                        ->disk('public')
                        ->directory('storage')
                        ->image()
                        ->avatar()
                        ->openable()
                        ->imageEditor()
                        ->circleCropper(),
                    TextInput::make('tel')
                        ->label('Telefono')
                        ->tel(),
                    TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->required(fn (string $operation): bool => $operation === 'create'),
                    TextInput::make('email')
                        ->label('Email')
                        ->email(),
                    TextInput::make('website')
                        ->label('Sito web')
                        ->url(),
                ])->columnSpan(1),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([

                    ImageColumn::make('avatar')
                        ->label('Logo')
                        ->size(60)
                        ->toggleable()
                        ->grow(false),


                    Stack::make([
                        TextColumn::make('name')
                            ->label('Ragione Sociale')
                            ->weight(FontWeight::Bold)
                            ->searchable()
                            ->grow(false)
                            ->sortable(),
                        TextColumn::make('piva')
                            ->label('P.IVA')
                            ->searchable()
                            ->grow(false),
                        TextColumn::make('cf')
                            ->label('CF')
                            ->searchable()
                            ->grow(false)
                            ->toggleable(),
                    ]),

                    Stack::make([


                        TextColumn::make('client')
                            ->label('Tipologia')
                            ->grow(false)
                            ->visible(false)
                            ->toggleable(isToggledHiddenByDefault: true),
                        TextColumn::make('city.name')
                            ->label('Sede')
                            ->sortable()
                            ->grow(false),
                        TextColumn::make('address')
                            ->label('Indirizzo')
                            ->grow(false),
                    ]),

                    Stack::make([
                        TextColumn::make('cap')
                            ->label('CAP')
                            ->grow(false),
                        TextColumn::make('country.name')
                            ->label('Paese')
                            ->toggleable()
                            ->grow(false),
                        TextColumn::make('state.name')
                            ->label('Regione/Provincia')
                            ->toggleable()
                            ->grow(false),
                    ]),

                    Stack::make([

                        TextColumn::make('tel')
                            ->label('Telefono')
                            ->icon('heroicon-m-phone')
                            ->grow(false),
                        TextColumn::make('email')
                            ->icon('heroicon-m-envelope')
                            ->label('Email')
                            ->grow(false),
                        TextColumn::make('website')
                            ->label('Website')
                            ->icon('heroicon-m-globe-alt')
                            ->grow(false),
                    ]),
                ]),
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
            'index' => Pages\ListCliFors::route('/'),
            'create' => Pages\CreateCliFor::route('/create'),
            'edit' => Pages\EditCliFor::route('/{record}/edit'),
        ];
    }
}
