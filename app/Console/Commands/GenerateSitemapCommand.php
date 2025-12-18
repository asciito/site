<?php

namespace App\Console\Commands;

use App\Blog\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'generate:sitemap';

    protected $description = 'Generate the sitemap xml file';

    protected Filesystem $files;

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
        $pages = ['home', 'contact', 'terms', 'privacy'];

        return collect($pages)
            ->map(fn (string $page) => $this->getPageUrl($page))
            ->merge(
                Post::all()->map(fn (Post $post): Url => $this->getPageUrl($post))
            );
    }

    /**
     * @template TModel of Model
     *
     * @param  string|TModel  $page
     */
    protected function getPageUrl(string|Model $page): Url
    {
        if (is_string($page)) {
            $time = File::lastModified(resource_path("views/site/pages/$page.blade.php"));
            $route = route($page);
            $lastModificationDate = Carbon::createFromTimestamp($time);
        } elseif ($page instanceof Model && $updated_at = $page->updated_at) {
            $route = route('post', $page);
            $lastModificationDate = $updated_at;
        } else {
            throw new InvalidArgumentException('The page provided does not have a updated_at property.');
        }

        return Url::create($route)->setLastModificationDate($lastModificationDate);
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
