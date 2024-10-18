<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComplianceDocumentResource\Pages;
use App\Filament\Resources\ComplianceDocumentResource\RelationManagers;
use App\Models\ComplianceDocument;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class ComplianceDocumentResource extends Resource
{
    protected static ?string $model = ComplianceDocument::class;

    protected static ?string $modelLabel = 'Documenti di conformità';

    protected static ?string $pluralModelLabel = 'Documenti di conformità';

//    protected static ?string $navigationParentItem = 'Contratti';
    protected static ?string $navigationGroup = 'Impostazioni';

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static bool $hasTitleCaseModelLabel = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->label('Titolo')->required(),
                Select::make('cli_fors_id')->label('Cliente')->required()
                    ->relationship('client', 'name'),
                TextInput::make('desc')->label('Descrizione')->columnSpanFull(),
                FileUpload::make('template')

                    ->label('Modello')
                    ->directory('subcontract-documents-models')
                    ->disk('public')
                    ->preserveFilenames()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('client.avatar'),
                TextColumn::make('title')->label('Titolo')->searchable()->sortable()->wrap(),
                TextColumn::make('client.name')->label('Cliente')->searchable()->sortable(),
                TextColumn::make('desc')->label('Descrizione')->searchable()->sortable()->wrap(),
                TextColumn::make('template') // Nome della colonna del file
                ->label('Modello')
                    ->url(fn($record) => Storage::url($record->template)) // Genera il link per il file
                    ->openUrlInNewTab() // Apri il link in una nuova scheda
//                    ->formatStateUsing(fn($state) => basename($state)) // Testo del link
                    ->formatStateUsing(fn($state) => $state ? "<a href='" . Storage::url($state) . "' target='_blank' style='font-weight: bold; color: blue; font-size:10pt; text-decoration: underline;'>" . 'Modello' . "</a>" : '')
                ->html()
                ->icon('heroicon-o-arrow-down-tray')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageComplianceDocuments::route('/'),
        ];
    }
}
