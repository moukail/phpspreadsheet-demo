<?php declare(strict_types=1);

namespace App\Command;

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

        // todo select excel file

        $fileName = $input->getOption('filename');

        if ($fileName === null) {
            $fileName = $this->selectFile();
        }

        try {
            $fileStream = $this->filesystem->readStream($fileName);
        } catch (FilesystemException $e) {
            $io->error($e->getMessage());

            return 0;
        }

        $file = '/tmp/fileStream';

        file_put_contents($file, $fileStream);

        // todo validate excel file

        $this->parser->parse($file);

        $this->parser->prepareStudentResults();
        $studentResults = $this->parser->getStudentResults()->toArray();

        $this->parser->prepareQuestionResults();
        $questionResults = $this->parser->getQuestionResults()->toArray();

        $this->climate->table($studentResults);
        $this->climate->table($questionResults);

        $html = $this->twig->render('students-results.html.twig', ['students' => $studentResults]);
        file_put_contents(dirname(__DIR__) . '/../var/output/students_results.html', $html);

        $output = $this->pdf->output($html);

        if ($output) {
            file_put_contents(dirname(__DIR__) . '/../var/output/students_results.pdf', $output);
        }

        $io->success('Success.');

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
}
