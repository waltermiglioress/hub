<?php

namespace App\Filament\Clusters\Commessa\Resources;

use App\Filament\Clusters\Commessa;
use App\Filament\Clusters\Commessa\Resources;
use App\Filament\Resources\PerformanceResource\Pages;
use App\Filament\Resources\PerformanceResource\RelationManagers;
use App\Models\Performance;
use App\Models\Project;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class PerformanceResource extends Resource
{
    protected static ?string $model = Performance::class;
    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Commessa::class;
    protected static ?string $modelLabel = 'Performance';

    protected static ?string $pluralModelLabel = 'Indici';

    protected static ?string $navigationLabel = 'Performance';

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    private static array $months = [
        'Gennaio' => 'Gennaio',
        'Febbraio' => 'Febbraio',
        'Marzo' => 'Marzo',
        'Aprile' => 'Aprile',
        'Maggio' => 'Maggio',
        'Giugno' => 'Giugno',
        'Luglio' => 'Luglio',
        'Agosto' => 'Agosto',
        'Settembre' => 'Settembre',
        'Ottobre' => 'Ottobre',
        'Novembre' => 'Novembre',
        'Dicembre' => 'Dicembre',
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dati Generali')
                    ->description('inserire i dati generali di riga')
                    ->schema([
                        Select::make('project_id')
                            ->label('Commessa')
                            ->options(Project::whereHas('users', function ($query) {
                                $query->where('user_id', Auth::id());
                            })->pluck('code', 'id'))
                            ->live()
                            ->searchable()
                            ->preload()
//                            ->relationship(
//                                'projects',
//                                'code',
//                                fn (Builder $query)=>$query->whereBelongsTo('user','user','true'))
                            ->required(),
                        Placeholder::make('Cliente')
                            ->content(function (Get $get): string {
                                $project = Project::find($get('project_id'));
                                return empty($project) ? 'seleziona prima la commessa' : $project->clifor->name;
                            }),
                        Select::make('period')
                            ->label('Periodo')
                            ->options(self::$months)
                            ->required()
                    ])
                    ->columns(3)->columnSpan(2),
                Section::make('Indici')
                    ->description('Prevent
                    abuse
                    by limiting the number of requests per period')
//                    ->aside()
                    ->columns(3)
                    ->schema([
                        TextInput::make('pic')
                            ->label('PIC')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('Numero di ore di formazione relativamente il primo ingresso in cantiere dei lavoratori, Art.36 ( solitamente 4h + 30 Min. a lavoratore) .')),
                        TextInput::make('add')
                            ->label('ADD')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('Numero di ore di formazione relativamente l addestramento di ATTREZZATURE NON NORMATE, ovvero non inserite nell’Accordo Stato-Regioni : Sega circolare, Motosega, Piegaferro,Smerigliatrici semiangolari,Tagliatubi, Tagliasfalti, Impianto di intasamento,Curva tubi,Compattatrice, Argano, Betoniera elettrica a bicchiere, Sideboom,  Ponteggio, Trabattello, ecc..')),
                        TextInput::make('pdo')
                            ->label('PDO')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('Numero di ore di formazione relativamente le Procedure Operative di cantiere.')),
                        TextInput::make('cnc')
                            ->label('CNC')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('Numero di ore di formazione relativamente le Chiusura delle Non Conformità ( Interne - Audit - D.L.)')),
                        TextInput::make('fob')
                            ->label('FOB')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('Numero di ore di formazione obbligatoria ( Rischio Alto, Preposto, Conduzione Attrezzature di lavoro, Spazi confinati, Apposizione Segnaletica Stradale,ecc.) tutte quelle formazioni obbligatorie per legge ed accordo stato regioni.')),
                        TextInput::make('tbt_ail')
                            ->label('TBT/AIL')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('Numero di ore relativamente i tool box / AIL effettuati in cantiere.')),
                        TextInput::make('swa')
                            ->label('SWA')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('(Stop Work Authority).')),
                        TextInput::make('swar')
                            ->label('SWAR')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('Numero di visite ed interviste in campo congiunte da parte del Management di cantiere alle maestranze impegnate nelle lavorazioni, sui temi della sicurezza e sulle cause che possono determinare eventi avversi o situazioni di criticità.')),
                        TextInput::make('audit')
                            ->label('Audit')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('Numero di Audit sia di 1a,2a,3a parte avuti in cantiere.')),
                        TextInput::make('infortuni')
                            ->label('Infortuni')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('Numero di Infortuni occorsi in cantiere.')),
                        TextInput::make('fac')
                            ->label('FAC')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('(First Aid Cases)')),
                        TextInput::make('near_miss')
                            ->label('Near Miss')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('Numero di Near Miss (mancati incidenti).')),
                        TextInput::make('ua_uc')
                            ->label('UA/UC')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('(Unsafe Act/Unsafe Condition).')),
                        TextInput::make('drills')
                            ->label('Drills')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('Numero di esercitazioni di emergenza svolte ( Antincindendio, Primo Soccorso, Alluvioni, Calamita naturali, Sversamenti, ecc).')),
                        TextInput::make('vir')
                            ->label('Segnalazioni VIR')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('Numero di segnalazioni riguardo i comportamenti virtuosi.')),
                        TextInput::make('ass_pnt')
                            ->label('Audit')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('Numero ASSEGNAZIONE Punti (Siti ENI).')),
                        TextInput::make('dec_pnt')
                            ->label('Dec PNT')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('Numero DECURTAZIONE Punti (Siti ENI).')),
                        TextInput::make('cse_sorv')
                            ->label('Conformità CSE/SORV')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('Numero conformità registrate nelle ispezioni da parte del CSE o dei sorveglianti.')),
                        TextInput::make('nc_cse_sorv')
                            ->label('Non conformità CSE/SORV')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('Numero di non conformità registrate nelle ispezioni da parte del CSE o dei sorveglianti.')),
                        TextInput::make('isp_hse_aspp')
                            ->label('Ispezioni HSE/ASPP')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('Numero di SOPRALLUOGHI in campo da parte degli HSE/ASPP del cantiere.')),
                        TextInput::make('isp_ext')
                            ->label('Ispezioni enti esterni')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('Numero di ispezioni da parte degli enti esterni ( ASL,Ispettorato del Lavoro,ODV,NIL,ecc.).')),
                        TextInput::make('m_h')
                            ->label('M/h')
                            ->numeric()
                            ->default(0)
                            ->helperText(new HtmlString('Numero ore lavorate.')),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.code')->label('Commessa')->searchable()->sortable(),
                TextColumn::make('project.clifor.name')->label('Cliente')->searchable()->sortable(),
                TextColumn::make('period')->label('Periodo')->searchable()->sortable(),
                TextColumn::make('pic')->label('PIC')
                    ->numeric(decimalPlaces: 1)
                    ->searchable()
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('add')->label('ADD')
                    ->numeric(decimalPlaces: 1)
                    ->searchable()
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('pdo')->label('PDO')
                    ->searchable()
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('cnc')->label('CNC')
                    ->searchable()
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('fob')->label('FOB')
                    ->searchable()
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('tbt_ail')->label('TBT/AIL')
                    ->searchable()
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('swa')->label('SWA')
                    ->searchable()
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('swar')->label('SWAR')
                    ->searchable()
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('audit')->label('AUDIT')
                    ->searchable()
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('infortuni')->label('Infortuni')
                    ->searchable()
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('fac')->label('FAC')
                    ->searchable()
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('near_miss')->label('NEAR MISS')
                    ->searchable()
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('ua_uc')->label('UA/UC')
                    ->searchable()
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('drills')->label('DRILLS')
                    ->searchable()
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('vir')->label('Segnalazioni VIR')
                    ->searchable()
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('ass_pnt')->label('ASS PNT')
                    ->searchable()
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('dec_pnt')->label('Dec PNT')
                    ->searchable()
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('cse_sorv')->label('Conformità CSE/SORV')
                    ->searchable()
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('nc_cse_sorv')->label('Non conformità CSE/SORV')
                    ->searchable()
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('isp_hse_aspp')->label('Ispezioni hse/aspp')
                    ->searchable()
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('isp_ext')->label('Ispezioni enti est')
                    ->searchable()
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
                TextColumn::make('m_h')->label('Ore lavorate')
                    ->searchable()
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label('Tot.')
                    ),
            ])
            ->persistSortInSession()
            ->deferLoading()
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(25)
            ->filters([
                SelectFilter::make('project_id')
                    ->label('Commessa')
                    ->options(Project::whereHas('users', function ($query) {
                        $query->where('user_id', Auth::id());
                    })->pluck('code', 'id'))
                    ->multiple()
                    ->searchable()
                    ->preload(),
                SelectFilter::make('period')
                    ->label('Periodo')
                    ->multiple()
                    ->options(self::$months),
            ],
                layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(2)
            ->persistFiltersInSession()
            ->deferFilters()
            ->filtersApplyAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Applica filtri'),
            )
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
            'index' => Resources\PerformanceResource\Pages\ListPerformances::route('/'),
            'create' => Resources\PerformanceResource\Pages\CreatePerformance::route('/create'),
            'edit' => Resources\PerformanceResource\Pages\EditPerformance::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {

        return parent::getEloquentQuery()->whereIn('project_id', auth()->user()->projects()->pluck('id'));
    }
}
