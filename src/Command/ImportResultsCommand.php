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
            ->addOption('type', 't', InputOption::VALUE_REQUIRED)
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
        $type = $input->getOption('type');

        if ($fileName === null) {
            $fileName = $this->selectFile();
        }

        if ($type === null) {
            $type = $this->selectOutput();
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

        if ($type == 'console'){
            $this->climate->table($studentResults);
            $this->climate->table($questionResults);
        }

        if ($type == 'html'){
            $this->saveAsHtml($studentResults,'students-results');
            $this->saveAsHtml($questionResults, 'questions-results');
        }

        if ($type == 'pdf'){
            $this->saveAsPdf($studentResults, 'students-results');
            $this->saveAsPdf($questionResults, 'questions-results');
        }

        $io->success('Success.');

        return 0;
    }

    private function saveAsHtml($data, $filename)
    {
        $html = $this->twig->render($filename. '.html.twig', ['data' => $data]);

        file_put_contents(dirname(__DIR__) . '/../var/output/'.$filename.'.html', $html);
    }

    private function saveAsPdf($data, $filename)
    {
        $html = $this->twig->render($filename. '.html.twig', ['data' => $data]);

        $pdf = $this->pdf->output($html);

        file_put_contents(dirname(__DIR__) . '/../var/output/'.$filename.'.pdf', $pdf);
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
        $input = $this->climate->radio('Please select output type:', ['console', 'pdf', 'html']);

        return $input->prompt();
    }
}
