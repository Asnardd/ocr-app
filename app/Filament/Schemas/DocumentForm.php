<?php

namespace App\Filament\Schemas;

use App\Models\Document;
use App\Services\OcrService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('file_path')
                    ->label('Document')
                    ->disk('local')
                    ->directory('documents')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state): void {
                        if (blank($state)) {
                            return;
                        }
                        $result = app(OcrService::class)->analyze($state);
                        $set('name', $result['name']);
                        $set('description', $result['description']);
                    }),

                TextInput::make('name'),

                Textarea::make('description'),
            ]);
    }
}
