<?php

namespace App\Filament\Pages;

use App\Models\Category;
use Filament\Actions\Action;
use Filament\Auth\Pages\EditProfile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\RawJs;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Override;

class ProfilePage extends EditProfile
{
    protected static array $technologies = [
        'JS', 'NodeJS', 'BunJS', 'NextJS', 'ReactJS', 'ReactNative',
        'PHP', 'Laravel', 'Symfony', 'CodeIgniter',
        'Python', 'Django', 'Flask',
        'CSS', 'TailwindCSS', 'Bootstrap', 'Styled Components',
        'SQL', 'MySQL', 'SQLite', 'PostgreSQL', 'NoSQL', 'MongoDB',
    ];

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
                        ->collapsible()
                        ->schema($this->getJobExperienceComponents())->columnStart([
                            'md' => 2,
                        ])
                        ->columnSpan([
                            'default' => 'full',
                            'sm' => 3,
                            'md' => 2,
                        ]),
                ])->columns([
                    'default' => 1,
                    'sm' => 3,
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

    protected function getJobExperienceComponents(): array
    {
        return [
            Repeater::make('experience')
                ->relationship()
                ->inlineLabel(false)
                ->hiddenLabel()
                ->collapsed()
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                ->reorderable()
                ->orderColumn('order')
                ->schema([
                    Hidden::make('id')
                        ->dehydratedWhenHidden(false),
                    TextInput::make('title')
                        ->required(),
                    Section::make('Date related information')
                        ->compact()
                        ->collapsible()
                        ->schema([
                            Group::make([
                                Toggle::make('working_here')
                                    ->live()
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
                                Toggle::make('date_range_as_relative')->label(__('Show as relative')),
                            ])->columns(['default' => 2]),
                            Group::make([
                                DatePicker::make('start_date')
                                    ->required()
                                    ->native(false)
                                    ->placeholder('Jan 1, 1977'),
                                DatePicker::make('end_date')
                                    ->native(false)
                                    ->disabled(fn (Get $get) => $get('working_here'))
                                    ->placeholder(fn (Get $get) => $get('working_here') ? '' : 'Jan 1, 1977'),
                            ])->columns([
                                'default' => 1,
                                'sm' => 2,
                            ]),
                        ]),
                    RichEditor::make('description')
                        ->required()
                        ->disableToolbarButtons([
                            'h2',
                            'h3',
                            'codeBlock',
                        ])
                        ->fileAttachments(false),
                    Select::make('categories')
                        ->relationship(titleAttribute: 'name')
                        ->label(__('Technologies'))
                        ->native(false)
                        ->multiple()
                        ->unique()
                        ->createOptionForm([
                            TextInput::make('name'),
                        ])
                        ->createOptionAction(function (Action $action) {
                            $action
                                ->modalWidth(Width::Small)
                                ->extraModalFooterActions([]);
                        })
                        ->createOptionUsing(function (Schema $schema, array $data) {
                            $name = $data['name'];
                            $slug = Str::slug($name);

                            try {
                                return Category::create(['name' => $name, 'slug' => $slug])->getKey();
                            } catch (UniqueConstraintViolationException) {
                                throw ValidationException::withMessages([
                                    'categories' => 'The technology "'.$name.'" already exists.',
                                ]);
                            }
                        }),
                ])
                ->columnSpanFull(),
        ];
    }
}
