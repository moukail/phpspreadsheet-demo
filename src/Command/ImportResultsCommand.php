<?php
namespace App\Command;

use App\Parser\Xlsx;
use League\CLImate\CLImate;
use League\Flysystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportResultsCommand extends Command
{
    private CLImate $climate;
    private Filesystem $filesystem;

    public function __construct(CLImate $climate, Filesystem $filesystem)
    {
        $this->climate = $climate;
        $this->filesystem = $filesystem;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:import-results')
            ->setDescription('Add a short description for your command')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Import Results',
            '==========================',
            '',
        ]);

        $io = new SymfonyStyle($input, $output);

        // todo select excel file

        $fileStream = $this->filesystem->readStream('Assignment.xlsx');

        $file = '/tmp/fileStream';

        file_put_contents($file, $fileStream);

        // todo validate excel file

        $parser = new Xlsx($file);

        $parser->prepareStudentResults();
        $studentResults = $parser->getStudentResults();

        $parser->prepareQuestionResults();
        $questionResults = $parser->getQuestionResults();

        $this->climate->table($studentResults->getValues());
        $this->climate->table($questionResults->getValues());

        $io->success('Success.');
        return 0;
    }
}
