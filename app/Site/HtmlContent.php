<?php

namespace App\Site;

use App\Site\Support\FrontMatter;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support;
use Symfony\Component\Yaml\Exception\ParseException;

class HtmlContent implements Htmlable
{
    public function __construct(protected string $content)
    {
        //
    }

    protected function replacePreWithTorchlight(): static
    {
        $this->content = Support\Str::of($this->content)->replaceMatches('/<pre>(.*?)<\/pre>/s', function ($matches) {
            $pre = $matches[1];

            try {
                $data = FrontMatter::load($pre)->getData();
            } catch (ParseException) {
                Support\Facades\Log::error('Trying to highlight code failed', ['code' => $pre]);

                $data = [];
            }

            $pre = FrontMatter::removeFrontMatter($pre);

            return view('site::torchlight', [
                'content' => trim($pre),
                'language' => $data['language'] ?? 'php',
            ]);
        });

        return $this;
    }

    public function toHtml(): string
    {
        $this->replacePreWithTorchLight();

        return $this->content;
    }
}
