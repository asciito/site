<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\EditProfile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Override;

class ProfilePage extends EditProfile
{
    #[Override]
    public static function isSimple(): bool
    {
        return false;
    }

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile Information')
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getCurrentPasswordFormComponent(),
                        $this->getResumeFormComponent(),
                        $this->getIntroductionComponent(),
                        $this->getDescriptionFormComponent(),
                    ])
                    ->aside()
                    ->description('Update the your profile information and provide the needed information.'),
                Group::make([
                    Section::make('Job Experience')
                        ->collapsed()
                        ->collapsible()
                        ->schema([
                            Repeater::make('experience')
                                ->relationship()
                                ->inlineLabel(false)
                                ->hiddenLabel()
                                ->collapsed()
                                ->collapsible()
                                ->itemLabel(fn (array $state) => $state['title'] ?? null)
                                ->orderColumn('order')
                                ->reorderableWithButtons()
                                ->schema([
                                    Hidden::make('id')
                                        ->default(0)
                                        ->dehydratedWhenHidden(false),
                                    TextInput::make('title'),
                                    RichEditor::make('description')
                                        ->disableToolbarButtons([
                                            'h2',
                                            'h3',
                                            'codeBlock',
                                        ])
                                        ->fileAttachments(false),
                                    Toggle::make('working_here')
                                        ->live()
                                        ->afterStateUpdated(fn (Set $set) => $set('end_date', null))
                                        ->afterStateUpdatedJs(RawJs::make(<<<'JS'
                                            (
                                                /**
                                                 * Handle the `working here` feature which only allow me to set
                                                 * at most one `job experience` as my current job position.
                                                 *
                                                 * @param {null|number} current
                                                 * @param {boolean} workingHere
                                                 **/
                                                (current, workingHere) => {
                                                    if (! workingHere) {
                                                        return;
                                                    }

                                                    /**
                                                    * @typedef {object} TJobExperience
                                                    * @property {string} title The position
                                                    * @property {(string|null)} description What you do in that job
                                                    * @property {boolean} working_here if you're still working here
                                                    **/

                                                    /**
                                                    * @var {Proxy<TJobExperience>} record
                                                    */
                                                    const records = $get('../');

                                                    for (let record in records) {
                                                        const recordStatePath = `../${record}`;

                                                        if ($get(`${recordStatePath}.working_here`) === false || $get(`${recordStatePath}.id`) === current) {
                                                            continue;
                                                        }

                                                        // Will reset the previously `working here` record to false
                                                        $set(`${recordStatePath}.working_here`, false);
                                                    }
                                                }
                                            )($get('id'), $get('working_here')); // IIFE
                                        JS)),
                                    Group::make([
                                        DatePicker::make('start_date')
                                            ->placeholder('Jan 1, 1977')
                                            ->native(false),
                                        DatePicker::make('end_date')
                                            ->placeholder(fn (Get $get) => $get('working_here') ? 'Not needed' : 'Jan 1, 1977')
                                            ->native(false)
                                            ->disabled(fn (Get $get) => $get('working_here')),
                                    ])
                                        ->columns(['default' => 2]),
                                ])->columnSpanFull(),
                        ])
                        ->columnStart([
                            'default' => 1,
                            'sm' => 1,
                            'md' => 2,
                            'lg' => 2,
                            'xl' => 2,
                            '2xl' => 2,
                        ])
                        ->columnSpan([
                            'default' => 'full',
                            'sm' => 'full',
                            'md' => 2,
                            'lg' => 2,
                            'xl' => 2,
                            '2xl' => 2,
                        ]),
                ])->columns([
                    'default' => 1,
                    'sm' => 3,
                    'md' => 3,
                    'lg' => 3,
                    'xl' => 3,
                    '2xl' => 3,
                ]),
            ]);
    }

    #[Override]
    public function getFormActionsAlignment(): string|Alignment
    {
        return Alignment::End;
    }

    protected function getDescriptionFormComponent(): Component
    {
        return RichEditor::make('description')
            ->label(__('Description'))
            ->toolbarButtons([
                'blockquote',
                'bold',
                'bulletList',
                'codeBlock',
                'h2',
                'h3',
                'italic',
                'link',
                'orderedList',
                'redo',
                'strike',
                'underline',
                'undo',
            ])
            ->grow()
            ->helperText('This text will be display in the `About me` section of the home page');
    }

    protected function getIntroductionComponent(): Component
    {
        return RichEditor::make('introduction')
            ->label(__('Introduction'))
            ->toolbarButtons([
                'blockquote',
                'bold',
                'bulletList',
                'codeBlock',
                'h1',
                'h2',
                'h3',
                'italic',
                'link',
                'orderedList',
                'redo',
                'strike',
                'underline',
                'undo',
            ])
            ->grow()
            ->helperText('This text will be display at the beginning of the home page');
    }

    protected function getResumeFormComponent(): Component
    {
        return FileUpload::make('resume')
            ->label(__('Resume'))
            ->acceptedFileTypes(['application/pdf'])
            ->formatStateUsing(fn () => Storage::exists('resume.pdf') ? ['resume.pdf'] : [])
            ->saveUploadedFileUsing(function (Component $component, TemporaryUploadedFile $file) {
                $filename = 'resume.pdf';

                if (Storage::exists($filename)) {
                    Storage::delete($filename);
                }

                Storage::putFileAs('/', $file, $filename);

                return $file;
            })
            ->deleteUploadedFileUsing(function (Component $component) {
                $filename = 'resume.pdf';

                if (Storage::exists($filename)) {
                    Storage::delete($filename);
                }
            })
            ->panelLayout(null);
    }
}
