<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'generate:sitemap';

    protected $description = 'Generate the sitemap xml file';

    protected \Illuminate\Contracts\Filesystem\Filesystem $files;

    protected string $disk = 'public';

    public function handle(): int
    {

        $this->components->task(
            'Generating sitemap',
            function () {
                $this->generateSitemap(
                    $this->getSitemappables()
                );
            }
        );

        return self::SUCCESS;
    }

    protected function getSitemappables(): Collection
    {
        $pages = ['home', 'contact', 'terms' , 'privacy'];

        return collect($pages)
            ->map(fn (string $page) => $this->getPageUrl($page))
            ->merge(Post::all());
    }

    protected function getPageUrl(string $page): Url
    {
        $lastModificationDate = File::lastModified(
            resource_path("views/site/pages/{$page}.blade.php")
        );

        return Url::create(route($page))
            ->setLastModificationDate(
                \Illuminate\Support\Carbon::createFromTimestamp($lastModificationDate, config('app.timezone')),
            );
    }

    protected function generateSitemap(Collection $collection): void
    {
        Sitemap::create()
            ->add($collection)
            ->writeToDisk($this->getDisk(), 'sitemap.xml');
    }

    protected function getDisk(): string
    {
        return $this->disk;
    }
}
