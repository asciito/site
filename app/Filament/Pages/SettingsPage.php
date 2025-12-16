<?php

namespace App\Filament\Pages;

use Coyotito\LaravelSettings\Settings;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

/**
 * Base class for settings pages
 *
 * @property-read Schema $form
 */
abstract class SettingsPage extends Page
{
    use CanUseDatabaseTransactions;
    use HasUnsavedDataChangesAlert;

    /**
     * @var ?Settings Cache instance for settings
     */
    protected ?Settings $settings = null;

    /**
     * The FQCN of a settings
     *
     * @var class-string<Settings>
     */
    protected static string $settingsClass;

    /**
     * @var ?array The state for the settings
     */
    public ?array $data = [];

    /**
     * @var string The name of the view to use
     */
    protected string $view = 'filament.pages.settings';

    public function mount(): void
    {
        $this->fillForm();
    }

    public function getTitle(): string|Htmlable
    {
        return Str::headline($this->settings()->group).' Settings';
    }

    /**
     * Fill the settings form
     */
    protected function fillForm(): void
    {
        $settings = $this->mutateFormDataBeforeFill($this->settings()->all());

        $this->form->fill($settings);
    }

    public function form(Schema $schema): Schema
    {
        return $schema;
    }

    protected function makeSchema(): Schema
    {
        return parent::makeSchema()->statePath('data');
    }

    /**
     * Save the settings
     *
     * @return void
     */
    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            $this->settings()->update($data)->save();

            $this->callHook('afterSave');
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction()
                ? $this->rollBackDatabaseTransaction()
                : $this->commitDatabaseTransaction();

            return;
        }

        $this->commitDatabaseTransaction();

        $this->rememberData();

        $this->getSaveNotification()->send();

        $this->redirect(static::getNavigationUrl(), navigate: FilamentView::hasSpaMode());
    }

    /**
     * The settings for used in the page
     */
    public function settings(): Settings
    {
        if (filled($this->settings)) {
            return $this->settings;
        }

        /** @var Settings $settings */
        $settings = app()->make(static::getSettingsClass());

        return $this->settings = $settings;
    }

    /**
     * Get the FQCN
     *
     * @return string
     */
    public static function getSettingsClass(): string
    {
        return static::$settingsClass;
    }

    /**
     * Mutate settings before filling form
     *
     * @param array $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    /**
     * Mutate settings before saving settings
     *
     * @param array $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    /**
     * The `save` notification
     *
     * This notification is trigger once the settings are saved
     */
    public function getSaveNotification(): Notification
    {
        $title = $this->getSaveNotificationTitle();
        $message = $this->getSaveNotificationMessage();

        return Notification::make()
            ->success()
            ->title($title)
            ->body($message);
    }

    /**
     * The tittle when save settings
     */
    public function getSaveNotificationTitle(): string
    {
        return __('Settings saved successfully.');
    }

    /**
     * The message when save settings
     */
    public function getSaveNotificationMessage(): string
    {
        return __('Your settings have been saved successfully.');
    }

    /**
     * Get the footer actions
     *
     * @return Action[]
     */
    public function getFooterActions(): array
    {
        return [
            Action::make('save')->action('save'),
        ];
    }
}
