<?php

namespace App\Filament\Resources;

use App\Enums\TenderAttrBuEnum;
use App\Enums\TenderAttrFluidEnum;
use App\Enums\TenderStatusEnum;
use App\Filament\Resources\TenderResource\Pages;
use App\Models\City;
use App\Models\State;
use App\Models\Tender;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class TenderResource extends Resource
{
    protected static ?string $model = Tender::class;
    protected static ?string $modelLabel = 'Gare';

    protected static ?string $pluralModelLabel = 'Gare';
    protected static ?string $navigationGroup = 'Elenchi';

    protected static ?string $navigationLabel = 'Elenco gare';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dati Generali')
                    ->description('Inserisci i dati generali relativi la gara')
                    ->schema([
                        Select::make('group')
                            ->label('Gruppo')
                            ->options([
                                'Sicilsaldo'=>'SICILSALDO',
                                'Nuova Ghizzoni'=>'NUOVA GHIZZONI'
                            ]),
                        Select::make('client_id')->label('Cliente')
                            ->relationship('clifor','name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm(fn(Form $form)=>CliForResource::form($form))
                            ->createOptionAction(
                                fn (Action $action) => $action->modalWidth('5xl'),
                            )
                            ->required(),
                        TextInput::make('rdo')->label('RDO')->required(),
                        TextInput::make('cig')->label('CIG')->required(),
                        TextInput::make('num')->label('Numero Gara')
                            ->placeholder('inserisci il numero interno assegnato alla gara')
                            ->required(),
                        Select::make('mode')
                            ->options([
                                'Impresa singola',
                                'ATI/RTI',
                                'Consorzio',
                            ])
                            ->label('Modalità partecipazione')
                            ->required(),
//                        Forms\Components\Select::make('buyer_id')
//                            ->label('Buyer')
//                            ->options(function($model) {
//                            $buyer=$model->buyer();
//                                return $buyer->name->toString();
//                            }),
                        Radio::make('status')
                            ->options(TenderStatusEnum::class)
                            ->required()
                            ->label('Stato Gara')
                            ->inline()
                            ->inlineLabel(false),
                        MarkdownEditor::make('desc')
                            ->columnSpanFull()
                            ->label('Oggetto')->required(),
                    ])->columnSpan(2)->columns(3)->collapsible(),


                Group::make()
                    ->schema([
                        Section::make('Altri dati')
                            ->description('scadenze etc..')
                            ->schema([
                                DatePicker::make('date_in')
                                    ->label('Data ricezione')
//                                    ->format('d/M/Y')
                                    ->displayFormat('d/m/Y')
                                    ->columnSpanFull()
                                    ->required(),
                                DateTimePicker::make('date_end')
                                    ->label('Data scadenza')
//                                    ->format('d/M/Y')
                                    ->displayFormat('d/m/Y H:i:s')
                                    ->columnSpanFull()
                                    ->after('date_in')
                                    ->required(),
                                Radio::make('type')
                                    ->label('Tipologia gara')
                                    ->options([
                                        0 => 'Nazionale',
                                        1 => 'Internazionale'
                                    ])
//                                    ->colors([
//                                        0 => 'green',
//                                        1 => 'warning',
//                                    ])
                                    ->inline()
                                    ->inlineLabel(false),
                               ToggleButtons::make('inspection')
                                   ->label('Sopralluogo eseguito?')
                                   ->boolean()
                                   ->grouped(),
//                                Fieldset::make('Indirizzi')
//                                    ->schema([
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
                                TextInput::make('cap')
                                    ->label('CAP')
                                    ->numeric(),
                                TextInput::make('address')
                                    ->label('Indirizzo')
                                    ->columnSpanFull(),
//                                    ]),
                            ])->columns(2),
                    ]),


                Section::make('Dati Tecnici')
                    ->relationship('specs')
                    ->description('Inserisci i dati tecnici della gara')
                    ->schema([
                        Select::make('bu')
                            ->label('Business Unit')
                            ->options(TenderAttrBuEnum::class)
                            ->required()
                            ->placeholder('Scegli la business unit'),
                        Select::make('fluid_type')
                            ->label('Tipo Fluido')
                            ->options(TenderAttrFluidEnum::class)
                            ->required()
                            ->placeholder('Scegli la business unit'),
                        TextInput::make('lenght')
                            ->label('Lunghezza')
                            ->numeric()
                            ->suffix('km')
                            ->required(),
                        TextInput::make('inches')
                            ->label('Diametro condotta')
                            ->numeric()
                            ->suffix('pollici')
                            ->required(),
                        TextInput::make('n_hdd')
                            ->label('Numero TOC')
                            ->numeric()
                            ->nullable(),
                        TextInput::make('diameter_hdd')
                            ->label('Diametro TOC')
                            ->numeric()
                            ->nullable(),
                        TextInput::make('n_microt')
                            ->label('Numero Micro Tunnel')
                            ->numeric()
                            ->nullable(),
                        TextInput::make('diameter_microt')
                            ->label('Diametro Micro Tunnel')
                            ->numeric()
                            ->nullable(),
                        TextInput::make('n_bvs')
                            ->label('Numero Stazioni')
                            ->numeric()
                            ->nullable(),
                    ])->columns(3)->columnSpanFull()->collapsible(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListTenders::route('/'),
            'create' => Pages\CreateTender::route('/create'),
            'edit' => Pages\EditTender::route('/{record}/edit'),
        ];
    }
}
