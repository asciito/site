<?php

namespace App\Site\Support;

use Illuminate\Support\Str;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class FrontMatter
{
    /**
     * @var string The YAML string representation
     */
    protected string $yaml = '';

    /**
     * @var string The body of the content (without the YAML)
     */
    protected string $body = '';

    protected static string $pattern = '/\A---\s*[\r\n]+(.*?)\s*[\r\n]+---/s';

    /**
     * Class constructor
     *
     * @param  string  $content  The content to extract YAML
     */
    protected function __construct(protected string $content)
    {
        $this->extractYaml();
    }

    /**
     * Load content
     */
    public static function load(string $content): static
    {
        return new static(trim($content));
    }

    /**
     * Load file content
     *
     * @return $this
     */
    public function loadFile(string $path): static
    {
        return static::load(
            file_get_contents($path)
        );
    }

    /**
     * Extract YAML from content
     */
    public function extractYaml(): void
    {
        if ($yaml = \Illuminate\Support\Str::match(static::$pattern, $this->content)) {
            $this->yaml = $yaml;
            $this->body = static::removeFrontMatter($this->content);
        } else {
            $this->body = $this->content;
        }

    }

    /**
     * Get the PHP data representation
     *
     * @throws ParseException if the YAML is not valid
     */
    public function getData(): ?array
    {
        if (empty($this->yaml)) {
            return null;
        }

        return Yaml::parse($this->yaml);
    }

    /**
     * Remove the YAML from the give content
     */
    public static function removeFrontMatter(string $content): string
    {
        return Str::replaceMatches(static::$pattern, '', $content);
    }
}
