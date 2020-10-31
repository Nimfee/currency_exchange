<?php
/**
 * Created by PhpStorm.
 * User: aleks
 * Date: 16.08.20
 * Time: 13:26
 */
namespace App\Command;

use App\Entity\Source;
use App\Repository\SourceRepository;
use App\Service\CurlService;
use App\Service\Import\Builder\CurrencyExchangeRate\BuilderFactory;
use App\Service\Import\Importer;
use App\Service\Import\Parser\CurrencyExchangeRate\ParserFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportDataCommand
 * @package App\Command
 */
class ImportDataCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:import-currencies';

    /**
     * @var string
     */
    protected static $url;

    protected function configure()
    {
        // ...
    }

    /**
     * @var SourceRepository
     */
    private $sourceRepository;

    /**
     * @var CurlService
     */
    private $curlService;

    /**
     * @var Importer
     */
    private $importer;

    public function __construct(
        SourceRepository $sourceRepository,
        Importer $importer,
        CurlService $curlService
    )
    {
        $this->sourceRepository = $sourceRepository;
        $this->importer = $importer;
        $this->curlService = $curlService;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sources = $this->sourceRepository->findAll();

        /** @var Source $source */
        foreach ($sources as $source) {
            $output->writeln("<info>Starting import from {$source->getUrl()}</info>");
            $resource = $this->curlService->executeRequest('GET', $source->getUrl());

            if (strpos($source->getUrl(), ParserFactory::PARSER_BPI) !== false) {
                $sourceType = ParserFactory::PARSER_BPI;
            } elseif (strpos($source->getUrl(), ParserFactory::PARSER_ECB) !== false) {
                $sourceType = ParserFactory::PARSER_ECB;
            }
            $result = $this->importer->processData(
                $resource,
                $source->getCurrency()->getISOCode(),
                $sourceType ?? '',
                BuilderFactory::BUILDER_CE
            );
            $output->writeln("<info>Imported $result items</info>");
        }

        return Command::SUCCESS;
    }
}