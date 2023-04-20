<?php declare(strict_types=1);

namespace App\Command;

use App\Output\ConsoleOutput;
use App\Output\HtmlOutput;
use App\Output\OutputInterface as MyOutputInterface;
use App\Output\PdfOutput;
use App\Parser\Parser;
use App\Pdf\PdfRenderer;
use League\CLImate\CLImate;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;

class ImportResultsCommand extends Command
{
    private array $outputTypes = ['console', 'pdf', 'html'];
    private string $file = '/tmp/fileStream';
    private MyOutputInterface $myOutput;

    public function __construct
    (
        private Parser $parser,
        private CLImate $climate,
        private Filesystem $filesystem,
        private Environment $twig,
        private PdfRenderer $pdf
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:import-results')
            ->addOption('filename', 'f', InputOption::VALUE_REQUIRED)
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED)
            ->setDescription('Add a short description for your command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Import Results',
            '==========================',
            '',
        ]);

        $io = new SymfonyStyle($input, $output);

        $this->setOutputType($input, $io);

        try {
            $this->setFile($input);
        } catch (FilesystemException $e) {
            $io->error($e->getMessage());
            return 1;
        }

        $this->parser->parse($this->file);

        $this->parser->prepareStudentResults();
        $studentResults = $this->parser->getStudentResults()->toArray();

        $this->parser->prepareQuestionResults();
        $questionResults = $this->parser->getQuestionResults()->toArray();

        $this->myOutput->print($studentResults, 'students-results');
        $this->myOutput->print($questionResults, 'questions-results');

        $io->success('Success. the file is saved in var/output dir');
        return 0;
    }

    private function selectFile()
    {
        $dirListing = $this->filesystem->listContents('');

        $files = array_map(function ($fileAtr) {
            return $fileAtr->path();
        }, $dirListing->toArray());

        $input = $this->climate->radio('Please select your file:', $files);

        return $input->prompt();
    }

    private function selectOutput()
    {
        $input = $this->climate->radio('Please select output type:', $this->outputTypes);
        return $input->prompt();
    }

    private function setOutputType(InputInterface $input, SymfonyStyle $io): void
    {
        $type = $input->getOption('output');
        if ($type === null) {
            $type = $this->selectOutput();
        }

        if (! in_array($type, $this->outputTypes)){
            $io->error("Output type $type is not supported");
        }

        match($type){
            'html' => $this->myOutput = new HtmlOutput($this->twig),
            'pdf' => $this->myOutput = new PdfOutput($this->twig, $this->pdf),
            default => $this->myOutput = new ConsoleOutput($this->climate),
        };
    }

    /**
     * @param InputInterface $input
     * @return void
     * @throws FilesystemException
     */
    private function setFile(InputInterface $input): void
    {
        $fileName = $input->getOption('filename');
        if ($fileName === null) {
            $fileName = $this->selectFile();
        }
        $fileStream = $this->filesystem->readStream($fileName);
        file_put_contents($this->file, $fileStream);
    }
}
