<?php
namespace App\Command;

use App\Parser\XlsxParser;
use Dompdf\Dompdf;
use League\CLImate\CLImate;
use League\Flysystem\FileAttributes;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;

class ImportResultsCommand extends Command
{
    private CLImate $climate;
    private Filesystem $filesystem;
    private Environment $twig;
    private Dompdf $dompdf;

    public function __construct(CLImate $climate, Filesystem $filesystem, Environment $twig, Dompdf $dompdf)
    {
        $this->climate = $climate;
        $this->filesystem = $filesystem;
        $this->twig = $twig;
        $this->dompdf = $dompdf;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:import-results')
            ->addOption('filename', 'f', InputOption::VALUE_REQUIRED)
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

        $dirListing = $this->filesystem->listContents('');

        $fileName = $input->getOption('filename');

        if($fileName == null){
            $files = array_map(function ($fileAtr) {
                return $fileAtr->path();
            }, $dirListing->toArray());

            $input    = $this->climate->radio('Please select your file:', $files);
            $fileName = $input->prompt();
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

        $parser = new XlsxParser($file);

        $parser->prepareStudentResults();
        $studentResults = $parser->getStudentResults()->toArray();

        $parser->prepareQuestionResults();
        $questionResults = $parser->getQuestionResults();

        $this->climate->table($studentResults);
        $this->climate->table($questionResults->getValues());

        $html = $this->twig->render('students-results.html.twig', ['students' => $studentResults]);
        file_put_contents( dirname(__DIR__) . '/../var/output/students_results.html', $html);

        $this->dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $this->dompdf->setPaper('A4', 'portrait');
        // Render the HTML as PDF
        $this->dompdf->render();
        // Store PDF Binary Data
        $output = $this->dompdf->output();

        file_put_contents( dirname(__DIR__) . '/../var/output/students_results.pdf', $output);

        $io->success('Success.');
        return 0;
    }
}
