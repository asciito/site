<?php

namespace App\Blog;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use Stringable;

class HtmlContent implements Htmlable, Stringable
{
    public function __construct(protected string $content, protected bool $withTorchlight = true)
    {
        //
    }

    protected function replacePreWithTorchlight(): static
    {
        $this->content = Str::of($this->content)
            ->replaceMatches(
                '~<pre>\s*<code(?:\s+class="language-([^"]+)")?>(.*?)</code>\s*</pre>~s',
                fn (array $matches) => view('site::torchlight', [
                    'content' => trim((string) $matches[2]),
                    'language' => $matches[1] ?? 'text',
                ])
            );

        return $this;
    }

    public function toHtml(): string
    {
        if ($this->withTorchlight) {
            $this->replacePreWithTorchlight();
        }

        return $this->content;
    }

    public function __toString(): string
    {
        return $this->toHtml();
    }
}
