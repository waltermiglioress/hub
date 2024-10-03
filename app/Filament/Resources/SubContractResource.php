<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubContractResource\Pages;
use App\Filament\Resources\SubContractResource\RelationManagers;
use App\Models\CliFor;
use App\Models\ComplianceDocument;
use App\Models\Project;
use App\Models\SubContract;
use App\Tables\Columns\ProgressColumn;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class SubContractResource extends Resource
{
    protected static ?string $model = SubContract::class;

    protected static ?string $modelLabel = 'Subappalto';

    protected static ?string $pluralModelLabel = 'Subappalti';

    protected static ?string $navigationGroup = 'Contratti';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square-stack';
    protected static bool $hasTitleCaseModelLabel = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informazioni generali')
                    ->columns(3)
                    ->schema([
                        Select::make('project_id')->label('Commessa')
                            ->searchable()
                            ->relationship('project', 'code')
                            ->preload()
                            ->live(),
                        Select::make('client_id')
                            ->label('Cliente')
                            ->live()
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
                Section::make('Documenti richiesti')
                    ->schema([
//                        TableRepeater::make('compliance_documents')
//
//                            ->columnSpanFull()
//                            ->headers([
//                                Header::make('Titolo'),
//                                Header::make('Desc'),
//                                Header::make('Stato'),
//                                Header::make('Note'),
//                                Header::make('Allegati'),
//                                Header::make('Data verifica'),
//                            ])
                        Repeater::make('compliance_documents')
                            ->label('Sezione relativa il caricamento dei documenti requisiti. Espandi ciascuna sezione e compila i moduli richiesti.')
                            ->relationship('complianceDocumentSubContracts') // La relazione con la tabella pivot
                            ->schema([
                                Section::make()
                                    ->compact()
                                    ->schema([
//                                        Select::make('title')
//                                            ->label('Nome del documento')
//                                            ->disabled()  // Il nome del documento viene precaricato e non può essere modificato dall'admin
//                                            ->required(),
                                        Select::make('compliance_document_id')
                                            ->label('Documento di conformità')
                                            ->relationship('complianceDocument', 'title', ignoreRecord: true)  // Usa la relazione con ComplianceDocument e il campo 'title'
                                            ->searchable()  // Permetti la ricerca
                                            ->required(),

                                        TextArea::make('desc')
                                            ->label('Descrizione')
                                            ->columnSpan(2)
                                            ->disabled(),  // La descrizione del documento viene precaricata e non può essere modificata dall'admin
                                        TextArea::make('notes')
                                            ->label('Note')
                                            ->columnSpanFull()
                                            ->disabled(),
                                    ])->columns(2)->columnSpan(3),
                                Section::make()
                                    ->compact()
                                    ->schema([
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
                                            ->required(),
                                        FileUpload::make('attachment')
                                            ->label('Allegato')
                                            ->directory('subcontract-documents')
                                            ->disk('public')
                                            ->preserveFilenames(),
                                    ])->columnSpan(1),

                            ])
                            ->itemLabel(fn(array $state, $record): ?string => $state['title'] ?? ($record->complianceDocument->title ?? null))  // Usa $state per creazione e $record per modifica
                            ->required()
                            ->collapsed()
                            ->columns(4),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('client.avatar'),
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
