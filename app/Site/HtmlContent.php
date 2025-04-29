<?php

namespace App\Site;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support;

class HtmlContent implements Htmlable
{
    public function __construct(protected string $content, protected bool $withTorchlight = true)
    {
        //
    }

    protected function replacePreWithTorchlight(): static
    {
        $this->content = Support\Str::of($this->content)
            ->replaceMatches(
                '/<pre><code(?: class="language-([^"]+)")?>(.*?)<\/code><\/pre>/s',
                fn (array $matches) => view('site::torchlight', [
                    'content' => trim($matches[2]),
                    'language' => $matches[1] ?? 'text', // Si no hay language, usar 'text'
                ])
            );

        return $this;
    }

    public function toHtml(): string
    {
        $this->withTorchlight && $this->replacePreWithTorchlight();

        return $this->content;
    }
}
