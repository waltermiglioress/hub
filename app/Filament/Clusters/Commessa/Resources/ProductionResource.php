<?php

namespace App\Filament\Clusters\Commessa\Resources;

use App\Filament\Clusters\Commessa;
use App\Filament\Clusters\Commessa\Resources;
use App\Filament\Exports\ProductionExporter;
use App\Filament\Resources\ProductionResource\Pages;
use App\Http\Controllers\ProductionController;
use App\Models\Production;
use App\Models\Project;
use App\Tables\Columns\ProgressColumn;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class ProductionResource extends Resource
{
    protected static ?string $model = Production::class;
    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Commessa::class;
    protected static ?string $modelLabel = 'Produzione';

    protected static ?string $pluralModelLabel = 'Produzioni';

    protected static ?string $navigationLabel = 'Monitoraggio commessa';

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Parte generale')
                    ->description('Descrizione della parte generale')
                    ->schema([
                        Select::make('project_id')
                            ->label('Commessa')
                            ->options(Project::whereHas('users', function ($query) {
                                $query->where('user_id', Auth::id());
                            })->pluck('code', 'id'))
                            ->searchable()
                            ->preload()
//                            ->relationship(
//                                'projects',
//                                'code',
//                                fn (Builder $query)=>$query->whereBelongsTo('user','user','true'))
                            ->required(),

                        Select::make('client_id')->label('Cliente')
                            ->relationship('client', 'name')
                            ->required(),
                        TextInput::make('desc')
                            ->label('Breve descrizione attività')
                            ->maxLength(50)
                            ->columnSpan(2),
                        TextInput::make('type')
                            ->datalist([
                                'SAL',
                                'ODL',
                                'MAP',
                            ])
                            ->label('Identificativo')
                            ->required(),
//                        TextInput::make('doc_id')->label('ID Documento')->required(),
                        TextInput::make('percentage')
                            ->label('Percentuale')
                            ->suffix('%')
                            ->numeric()
                            ->maxValue(100)
                            ->afterStateUpdated(function (Set $set, $get, $state) {
                                $calc = (int)$get('value') * ($state / 100);
                                $set('imponibile', $calc);
                            })
                            ->debounce(600)
                            ->required(),

                        TextInput::make('value')

                            //->mask(RawJs::make('$money($input)'))
//                            ->stripCharacters('.')
                            ->live(debounce: 700)
                            ->step("any")
                            ->numeric()
                            ->minValue(-10000000)
                            ->prefix('€')
                            ->label('Valore lavorazione')
                            ->afterStateUpdated(callback: function (Set $set, $get, $state) {
                                $calc = ((int)$get('percentage')) * ($state / 100);
                                $set('imponibile', $calc);
                            })
                            ->required(),

                        TextInput::make('imponibile')
                            ->prefix('€')
                            ->readOnly()
                            ->live(),
                        MarkdownEditor::make('note')->label('Note')->columnSpanFull(),


                    ])->columns(4)->columnSpan(2),
                Group::make()
                    ->schema([
                        Section::make('Dettagli')
                            ->schema([
                                Select::make('status')->label('Stato')
                                    ->options([
                                        'fatturato' => 'FATTURATO',
                                        'contabilizzato e non ft' => 'CONTABILIZZATO E NON FATTURATO',
                                        'stimato' => 'STIMATO',
                                        'in corso' => 'IN CORSO',
                                    ])
                                    ->required(),
                                DatePicker::make('date_start')
                                    ->label('Data inizio')
                                    ->minDate(now()->subYears(10))
                                    ->displayFormat('d/m/Y')
                                    ->required(),
                                DatePicker::make('date_end')
                                    ->label('Data fine')
                                    ->displayFormat('d/m/Y')
                                    ->after('date_start')
                                    ->required(),
                                FileUpload::make('attachments')
                                    ->multiple()
                                    ->openable()
                                    ->label('Allegati')// Gestisci il caricamento dei file
                                    ->disk('attachment') // Definisci il disco
                                    ->directory(function (?Production $record) {
                                        // Directory dinamica basata sul progetto e sul post
                                        return "projects/{$record->project->code}/produzioni/{$record->project->contract}";
                                    })
                                    ->preserveFilenames()
                                    ->storeFileNamesIn('filename')
                                    ->formatStateUsing(function (?Production $record) {
                                        return $record?->attachments()->get()->pluck('path')->toArray();
                                    })->dehydrated(false)
                                ,
                            ]),

                    ])->columnSpan(1)

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.code')->label('Commessa')->searchable()->sortable(),
                TextColumn::make('client.name')->label('Cliente')->searchable()->sortable(),
                TextColumn::make('desc')->label('Descrizione')->words(10)->wrap(),
                ColumnGroup::make('Date lavorazioni', [
                    TextColumn::make('date_start')->date('d/m/Y')->label('Data inizio'),
                    TextColumn::make('date_end')->date('d/m/Y')->label('Data fine'),
                ])->alignment(Alignment::Center)
                    ->wrapHeader(),
                TextColumn::make('type')->label('Identificativo')->searchable()->sortable(),
                ColumnGroup::make('Valori', [
                    TextColumn::make('value')->label('Valore')
                        ->money('eur', true)
                        ->sortable()
                        ->summarize(
                            Sum::make()->label('Totale')->money('EUR')
                        ),
                    ProgressColumn::make('percentage')
                        ->label('Percentuale')
                        ->sortable(),

                    TextColumn::make('imponibile')
                        ->label('Imponibile')
                        ->money('eur', true)->sortable()
                        ->summarize(
                            Sum::make()->label('Totale')->money('EUR'))
                ])->alignment(Alignment::Center)
                    ->wrapHeader(),


                TextColumn::make('status')->label('Stato')
//                    ->options([
//                        'fatturato'=>'FATTURATO',
//                        'contabilizzato e non ft'=>'CONTABILIZZATO E NON FATTURATO',
//                        'stimato'=>'STIMATO',
//                        'in corso'=>'IN CORSO',
//                    ]),
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'fatturato' => 'success',
                        'contabilizzato e non ft' => 'warning',
                        'stimato' => 'estimated',
                        'in corso' => 'primary',
                    }),

                TextColumn::make('updated_at')
                    ->dateTime('d/m/y H:i', 'Europe/Rome')
                    ->label('Ultimo aggiornamento')
                    ->sortable(),


//                TextColumn::make('ft')->label('Fattura')
//                    ->searchable()->sortable(),
//                TextColumn::make('date_ft')->label('Data fattura'),
            ])
            ->defaultSort('date_end', 'desc')
            ->persistSortInSession()
            ->deferLoading()
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(25)
            ->filters([
                SelectFilter::make('status')
                    ->label('Stato')
                    ->multiple()
                    ->options([
                        'fatturato' => 'FATTURATO',
                        'contabilizzato e non ft' => 'CONTABILIZZATO E NON FATTURATO',
                        'stimato' => 'STIMATO',
                        'in corso' => 'IN CORSO',
                    ]),
                SelectFilter::make('project_id')
                    ->label('Commessa')
                    ->options(Project::whereHas('users', function ($query) {
                        $query->where('user_id', Auth::id());
                    })->pluck('code', 'id'))
                    ->multiple()
                    ->searchable()
                    ->preload(),

                SelectFilter::make('client_id')->label('Cliente')
                    ->preload()
                    ->relationship('client', 'name')
                    ->searchable(),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Data inizio'),
                        DatePicker::make('created_until')->label('Data fine'),
                    ])->columns(2)->columnSpan(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date_start', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date_end', '<=', $date),
                            );
                    })
            ], layout: FiltersLayout::AboveContent)
            ->persistFiltersInSession()
            ->deferFilters()
            ->filtersApplyAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Applica filtri'),
            )
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->color('green'),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()->hasRole(['reporting-admin', 'super_admin'])),
                    ExportBulkAction::make('exportergrid')
                        ->label('Esporta griglia')
                        ->columnMapping(false)
                        ->fileDisk('exports')
                        ->fileName(fn(Export $export): string => "produzioni-{$export->getKey()}.csv")
                        ->exporter(ProductionExporter::class),
                    BulkAction::make('export')
                        ->label('Esporta per competenza')
                        ->action(function (Collection $records) {
                            $ids = $records->pluck('id')->join(',');
                            Log::info("IDs received from bulk: " . $ids);
                            $c = new ProductionController();// Estrai gli ID e uniscili in una stringa separata da virgole
                            return $c->export(new Request(['ids' => $ids]));
                        })
                        ->visible(fn() => auth()->user()->hasRole(['reporting-admin', 'super_admin']))
                        ->icon('heroicon-o-arrow-down-tray')
                ]),

            ])
            ->selectCurrentPageOnly()
            ->emptyStateHeading('Nessuna produzione')
            ->emptyStateIcon('heroicon-o-currency-euro')
            ->emptyStateDescription('Aggiungi una nuova produzione da fatturare o non ancora bla bla bla')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Aggiungi produzione')
                    ->url('productions/create')
                    ->icon('heroicon-m-plus')
                    ->button(),
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
            'index' => Resources\ProductionResource\Pages\ListProductions::route('/'),
            'create' => Resources\ProductionResource\Pages\CreateProduction::route('/create'),
            'edit' => Resources\ProductionResource\Pages\EditProduction::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {

        return parent::getEloquentQuery()->whereIn('project_id', auth()->user()->projects()->pluck('id'));
    }

}
