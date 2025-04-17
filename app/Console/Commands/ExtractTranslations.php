<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExtractTranslations extends Command
{
    protected $signature = 'translations:extract';
    protected $description = 'Extract all translations from the project';

    public function handle()
    {
        $translations = [];

        $directories = [
            app_path(),
            resource_path('views'),
        ];

        foreach ($directories as $directory) {
            $this->extractTranslationsFromDirectory($directory, $translations);
        }

        $jsonTranslations = json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        File::put(resource_path('lang/en.json'), $jsonTranslations);

        $this->info('Translations extracted successfully.');
    }

    private function extractTranslationsFromDirectory($directory, &$translations)
    {
        $files = File::allFiles($directory);

        foreach ($files as $file) {
            $content = file_get_contents($file->getPathname());
            $this->extractTranslationsFromContent($content, $translations);
        }
    }

    private function extractTranslationsFromContent($content, &$translations)
    {
        preg_match_all('/__\([\'"](.+?)[\'"]\)/', $content, $matches);

        foreach ($matches[1] as $match) {
            $translations[$match] = $match;
        }
    }
}
