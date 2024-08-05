<?php

namespace App\Site\Support;

use Illuminate\Support\Str;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class FrontMatter
{
    protected string $yaml = '';

    protected string $body = '';

    protected static string $pattern = '/\A---\s*[\r\n]+(.*?)\s*[\r\n]+---/s';

    public function __construct(protected string $content)
    {
        $this->extractYaml();
    }

    public static function load(string $content): static
    {
        return new static(trim($content));
    }

    public function loadFile(string $path): static
    {
        return static::load(
            file_get_contents($path)
        );
    }

    public function extractYaml(): void
    {
        if ($yaml = \Illuminate\Support\Str::match(static::$pattern, $this->content)){
            $this->yaml = $yaml;
            $this->body = static::removeFrontMatter($this->content);
        } else {
            $this->body = $this->content;
        }

    }

    /**
     * @throws ParseException if the YAML is not valid
     */
    public function getData(): array|null
    {
        if (empty($this->yaml)) {
            return null;
        }

        return Yaml::parse($this->yaml);
    }

    public static function removeFrontMatter(string $content): string
    {
        return Str::replaceMatches(static::$pattern, '', $content);
    }
}
