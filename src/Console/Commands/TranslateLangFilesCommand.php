<?php

namespace PavelZanek\LaravelDeepl\Console\Commands;

use Illuminate\Console\Command;
use PavelZanek\LaravelDeepl\Services\TranslationService;
use PavelZanek\LaravelDeepl\Traits\Console\RunsPint;

class TranslateLangFilesCommand extends Command
{
    use RunsPint;

    /**
     * @var string
     */
    protected $signature = 'deepl:translate
                            {file : Path to the file to translate}
                            {--sourceLang=en : Source language (default: en)}
                            {--targetLang=cs : Target language (default: cs)}
                            {--with-pint : Run Pint after translation (only in local environment)}';

    /**
     * @var string
     */
    protected $description = 'Translate Laravel localization files using DeepL';

    public function __construct(
        private readonly TranslationService $translationService
    ) {
        parent::__construct();
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(): int
    {
        $sourceLang = $this->option('sourceLang');
        $targetLang = $this->option('targetLang');
        $filePath = $this->argument('file');

        if (! is_string($filePath) || ! is_string($sourceLang) || ! is_string($targetLang)) {
            $this->error('Invalid arguments provided.');

            return self::FAILURE;
        }

        try {
            $this->translationService->translateFile($filePath, $sourceLang, $targetLang);

            $this->info('Translations have been successfully written.');

            $this->maybeRunPint();

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
