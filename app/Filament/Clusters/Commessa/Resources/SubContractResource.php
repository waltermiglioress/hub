<?php

namespace App\Filament\Clusters\Commessa\Resources;

use App\Filament\Clusters\Commessa;
use App\Filament\Clusters\Commessa\Resources\SubContractResource\Pages;
use App\Filament\Clusters\Commessa\Resources\SubContractResource\RelationManagers;
use App\Helpers\Utils;
use App\Models\SubContract;
use App\Tables\Columns\ProgressColumn;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class SubContractResource extends Resource
{
    protected static ?string $model = SubContract::class;

    protected static ?string $modelLabel = 'Subappalto';

    protected static ?string $pluralModelLabel = 'Subappalti';

    protected static ?string $navigationGroup = 'Contratti';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square-stack';
    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $cluster = Commessa::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn(SubContract $record): bool => auth()->user()->can('update_sub::contract', $record)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn(SubContract $record): bool => auth()->user()->can('delete_sub::contract', $record)),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->inlineLabel()
                    ->schema([
                        Split::make([
                            Grid::make(2)
                                ->schema([
                                    Group::make([
                                        TextEntry::make('project.code')->label('Commessa')
                                            ->weight(FontWeight::Bold),
                                        TextEntry::make('project.contract')->label('N. Contratto')
                                            ->weight(FontWeight::Bold),
                                        TextEntry::make('project.CIG')->label('CIG')
                                            ->weight(FontWeight::Bold)


                                    ]),
                                    Group::make([
                                        TextEntry::make('client.name')->label('Cliente')
                                            ->weight(FontWeight::Bold),
                                        TextEntry::make('project.contractor')->label('Appaltatore')
                                            ->weight(FontWeight::Bold),
                                        TextEntry::make('referent')->label('Referente')
                                            ->weight(FontWeight::Bold)
                                            ->icon('heroicon-m-user')
                                            ->iconColor('primary'),
                                    ]),
                                ]),
                            ImageEntry::make('client.avatar')
                                ->hiddenLabel()
                                ->grow(false),
                        ])->from('lg'),
                    ]),
                Section::make(fn (SubContract $record):String => Utils::getApprovalStatus($record))

                    ->schema([
                        RepeatableEntry::make('complianceDocumentSubContracts')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('complianceDocument.title')->label('Titolo documento')
                                    ->weight(FontWeight::Bold),
                                TextEntry::make('status')->label('Stato')
                                    ->default('pending')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                    }),
                                TextEntry::make('attachment')->label('Allegato')
                                    ->url(fn($record) => Storage::url($record->attachment))
                                    ->formatStateUsing(fn($record) => basename($record->attachment))
                                    ->icon('heroicon-s-paper-clip')
                                    ->iconColor('primary')
                                    ->columnSpan(3),
                                TextEntry::make('complianceDocument.desc')->label('Descrizione documento')->columnSpanFull(),
                                TextEntry::make('complianceDocument.notes')->label('Note')
                                    ->columnSpanFull(),

                            ])
                            ->columns(5)
                    ])
                    ->collapsible(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'view' => Pages\ViewSubContract::route('/{record}'),
            'index' => Pages\ManageSubContracts::route('/'),

        ];
    }



}
