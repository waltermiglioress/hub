<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubContractResource\Pages;
use App\Filament\Resources\SubContractResource\RelationManagers;
use App\Helpers\Utils;
use App\Models\CliFor;
use App\Models\ComplianceDocument;
use App\Models\ComplianceDocumentSubContract;
use App\Models\Production;
use App\Models\Project;
use App\Models\SubContract;
use App\Tables\Columns\ProgressColumn;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Nette\Utils\Html;

class SubContractResource extends Resource
{
    protected static ?string $model = SubContract::class;

    protected static ?string $modelLabel = 'Subappalto';

    protected static ?string $pluralModelLabel = 'Subappalti';

    protected static ?string $navigationGroup = 'Anagrafiche';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square-stack';
    protected static bool $hasTitleCaseModelLabel = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Subappalto')
                    ->tabs([

                        Tab::make('Informazioni Generali')
                            ->inlineLabel()
                            ->schema([
                                Select::make('project_id')->label('Commessa')
                                    ->searchable()
                                    ->options(Project::whereHas('users', function ($query) {
                                        $query->where('user_id', auth()->id());
                                    })->pluck('code', 'id'))
                                    ->preload()
                                    ->live()
                                    ->required(),
                                Select::make('client_id')
                                    ->label('Cliente')
                                    ->live()
                                    ->required()
                                    ->options(CliFor::where('is_client', 'like', true)->pluck('name', 'id')->toArray())
                                    ->afterStateUpdated(function (callable $set, Get $get) {
                                        $clientId = $get('client_id');
                                        if ($clientId) {
                                            // Recupera i documenti di conformità legati al cliente selezionato
                                            $complianceDocuments = ComplianceDocument::where('cli_fors_id', $clientId)->get();

                                            // Pre-carica i documenti nel repeater
                                            $set('compliance_documents', $complianceDocuments->map(fn($doc) => [
                                                'compliance_document_id' => $doc->id,  // Per identificare il documento nel pivot
                                                'title' => $doc->title,
                                                'desc' => $doc->desc,
//                                        'status' => 'pending',  // Default status
                                                'notes' => $doc->notes,
                                                'attachment' => null,
                                                'verified_at' => null,
                                            ])->toArray());
                                        }
                                    }),

                                Placeholder::make('CIG')->label('CIG')
                                    ->content(fn(Get $get): ?string => Project::find($get('project_id'))->CIG ?? 'N/A'),

                                Select::make('supplier_id')
                                    ->label('Subappaltatore')
                                    ->relationship(
                                        name: 'supplier',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn(Builder $query) => $query->where('is_supplier', true))
                                    ->required(),
                                TextInput::make('referent')
                                    ->label('Referente')
                                    ->required(),
                                TextArea::make('desc')
                                    ->label('Descrizione lunga')
                                    ->live()
                                    ->placeholder(fn(Get $get) => Project::find($get('project_id'))->long_desc ?? 'N/A')
                                    ->columnSpanFull(),

                            ]),
                        Tab::make('Documentazione richiesta')
                            ->badge(function (?SubContract $record) {
                                if (!$record) {
                                    return null; // Nessun badge durante la fase di creazione
                                }
                                [$approvedCount, $totalCount] = Utils::calculateApprovalCounts($record);
                                return "{$approvedCount}/{$totalCount}";
                            })
                            ->columnSpanFull()
                            ->schema([
                                Repeater::make('compliance_documents')
                                    ->label('Sezione relativa il caricamento dei documenti requisiti. Espandi ciascuna sezione e compila i moduli richiesti.')
                                    ->relationship('complianceDocumentSubContracts') // La relazione con la tabella pivot

                                    ->schema([
                                        Section::make()
                                            ->inlineLabel()
                                            ->compact()
                                            ->schema([
//                                        Select::make('title')
//                                            ->label('Nome del documento')
//                                            ->disabled()  // Il nome del documento viene precaricato e non può essere modificato dall'admin
//                                            ->required(),
                                                Select::make('compliance_document_id')
                                                    ->label('Documento di conformità')
                                                    ->relationship('complianceDocument', 'title', ignoreRecord: true)  // Usa la relazione con ComplianceDocument e il campo 'title'
                                                    ->searchable()
                                                    ->fixIndistinctState()
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                                    ->live()
                                                    ->helperText(function ($get) {
                                                        $complianceDocumentId = $get('compliance_document_id');

                                                        if ($complianceDocumentId) {
                                                            // Recupera il documento di conformità selezionato
                                                            $document = ComplianceDocument::find($complianceDocumentId);

                                                            // Restituisci la descrizione come HtmlString per interpretarla come HTML, assicurandoti che sia una stringa
                                                            return new HtmlString($document->desc ?? '');
                                                        }

                                                        // Restituisce una stringa vuota se non c'è un documento selezionato
                                                        return new HtmlString('');
                                                    })
                                                    ->required()
                                                    ->disabled(!auth()->user()->hasRole('con-mng')),
                                                TextArea::make('notes')
                                                    ->label('Note')
                                                    ->disabled(!auth()->user()->hasRole('con-mng')),


                                                TextInput::make('verified_at')
                                                    ->label('Verificato il')
                                                    ->disabled()  // Il campo di verifica viene compilato solo dopo la verifica
                                                    ->nullable(),
                                                Select::make('status')
                                                    ->label('Stato')
                                                    ->options([
                                                        'pending' => 'In attesa',
                                                        'approved' => 'Approvato',
                                                        'rejected' => 'Rigettato',
                                                    ])
                                                    ->default('pending')
                                                    ->selectablePlaceholder(false)
                                                ,
                                                FileUpload::make('attachments')
                                                    ->label('Allegati')// Gestisci il caricamento dei file
                                                    ->disk('attachment') // Definisci il disco
                                                    ->directory(function (ComplianceDocumentSubContract $record, Get $get) {
                                                        // Ottieni il project_id dal form (fuori dal repeater)
                                                        $projectId = $get('../../project_id');

                                                        // Verifica se il project_id esiste
                                                        if ($projectId) {
                                                            // Effettua la query per ottenere il progetto
                                                            $project = \App\Models\Project::find($projectId);

                                                            // Verifica che il record del progetto esista
                                                            if ($project) {
                                                                return "projects/{$project->code}/sub_contract/{$project->contract}_" . str_replace('/', '', $project->desc);
                                                            }
                                                        }

                                                        // Se il project_id non è disponibile o il progetto non esiste
                                                        return "projects/default/sub_contract/{$record->contract}_{$record->desc}";
                                                    })
                                                    ->preserveFilenames()
                                                    ->storeFileNamesIn('filename')
                                                    ->formatStateUsing(function (?ComplianceDocumentSubContract $record) {
                                                        return $record?->attachments()->get()->pluck('path')->toArray();
                                                    })->dehydrated(false)


                                            ])


                                    ])
                                    ->itemLabel(function (array $state, $record): HtmlString {
                                        // Determina il titolo del documento
                                        $title = null;

                                        // Se siamo in fase di creazione o aggiornamento e lo stato contiene 'compliance_document_id'
                                        if (isset($state['compliance_document_id'])) {
                                            // Recupera il titolo del documento di conformità associato all'ID
                                            $document = \App\Models\ComplianceDocument::find($state['compliance_document_id']);
                                            $title = $document->title ?? 'Documento';
                                        } elseif ($record && $record->complianceDocument) {
                                            // Altrimenti, usa il titolo dal record già salvato, se disponibile
                                            $title = $record->complianceDocument->title ?? 'Documento';
                                        } else {
                                            $title = 'Documento';
                                        }
                                        // Determina l'icona in base allo status
                                        $status = $state['status'] ?? ($record->status ?? 'pending');
                                        $icon = match ($status) {
                                            'approved' => '<svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>',
                                            'rejected' => '<svg class="h-6 w-6 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>',
                                            default => '<svg class="h-6 w-6 text-gray-500 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15m0-3-3-3m0 0-3 3m3-3V15" />
</svg>
'
                                        ,
                                        };
                                        // Genera l'HTML per il titolo e l'icona usando un contenitore flex
                                        $content = "<div class='flex items-center space-x-14'>{$icon}<span class='px-2.5'>{$title}</span></div>";
                                        // Restituisce un'istanza di HtmlString per interpretare correttamente l'SVG come HTML
                                        return new HtmlString($content);
                                    })
                                    ->required()
                                    ->collapsed()
                                    ->deleteAction(
                                        fn(Action $action) => $action->requiresConfirmation(),
                                    ),
                            ])
                    ])->columnSpanFull(),

            ]);

    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('client.avatar')
                    ->label(''),
                Tables\Columns\TextColumn::make('project.contract')
                    ->label('Contratto')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Subappaltatore')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.contractor')
                    ->label('Appaltatore')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.desc')
                    ->label('Descrizione breve')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('referent')
                    ->label('Referente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data creazione')
                    ->date('d/m/Y')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.desc')
                    ->label('Descrizione breve')
                    ->searchable()
                    ->sortable(),
                ProgressColumn::make('percentage')
                    ->label('Percentuale')
                    ->sortable(),
                Tables\Columns\TextColumn::make('Documentazione')
                    ->alignCenter()
                    ->badge()
                    ->state(function (SubContract $record) {
                        [$approvedCount, $totalCount] = Utils::calculateApprovalCounts($record);
                        return "{$approvedCount}/{$totalCount}";
                    })
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
            'index' => Pages\ListSubContracts::route('/'),
            'create' => Pages\CreateSubContract::route('/create'),
            'edit' => Pages\EditSubContract::route('/{record}/edit'),
        ];
    }


}
