<?php
namespace App\Command;

use App\Generator\FakeDataGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateFakeDataCommand extends Command
{
    protected static $defaultName = 'app:generate-fake-data';
    private $generator;

    public function __construct(FakeDataGenerator $generator)
    {
        $this->generator = $generator;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Generate fake data for testing')
            ->setHelp('This command generates fake data for testing purposes.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->generator->generateAuthors(10);
        $this->generator->generateBooks(20);
        $output->writeln('Fake data generated successfully.');
        return Command::SUCCESS;
    }
}