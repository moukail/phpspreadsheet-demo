<?php
namespace App\Command;

use App\Model\Question;
use App\Model\Student;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use League\CLImate\CLImate;
use League\Flysystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportResultsCommand extends Command
{
    private CLImate $climate;
    /** @var Filesystem */
    private $filesystem;
    private Collection $questions;
    private $maxScores = [];
    private Collection $studentResults;

    public function __construct(CLImate $climate, Filesystem $filesystem)
    {
        $this->climate = $climate;
        $this->filesystem = $filesystem;
        $this->questions = new ArrayCollection();

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

        $file = '/tmp/assignment.xlsx';

        file_put_contents($file, $fileStream);

        // todo validate excel file

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

        $sheet = $spreadsheet->getSheet(0);

        $highestRow = $sheet->getHighestDataRow();
        $highestColumn = $sheet->getHighestDataColumn();

        $ids = $sheet->rangeToArray('B1:'.$highestColumn.'1', null, true, false);
        $this->maxScores = $sheet->rangeToArray('B2:'.$highestColumn.'2', null, true, false);

        $progress = $this->climate->progress()->total($highestRow);

        $studentResults = new ArrayCollection();

        // calculate students results
        for ($row = 3; $row <= $highestRow; ++$row) {

            $progress->current($row, 'loading');

            $rowData = $sheet->rangeToArray('A'.$row.':'.$highestColumn.$row, null, true, false);

            $student = (new Student(
                array_shift($rowData[0])
            ))
                ->setMaxTotalScore(array_sum($this->maxScores[0]))
                ->setTotalScore(array_sum($rowData[0]));

            $studentResults->add($student);
            $student->calculateGrade();
        }

        $this->studentResults = $studentResults->map(function (Student $student){
            return $student->toArray();
        });

        for ($row = 3; $row <= $highestRow; ++$row) {
            $rowData = $sheet->rangeToArray('A'.$row.':'.$highestColumn.$row, null, true, false);
            array_shift($rowData[0]);
            $this->prepareQuestionsData($ids[0], $rowData[0]);
        }

        /** @var Question $next */
        while ($next = $this->questions->next()){
            $next->calculatePValue();
            $next->calculateRValue();
        }

        $results2 = $this->questions->map(function (Question $question){
            return $question->toArray();
        });

        $this->climate->table($this->studentResults->getValues());
        $this->climate->table($results2->getValues());

        $io->success('Success.');
        $io->note(sprintf('transactions_added: %s', 'test'));
        return 0;
    }

    /**
     * @param ArrayCollection $questions
     * @param $ids
     * @param $rowData
     * @return void
     */
    public function prepareQuestionsData(array $ids, $rowData): void
    {
        for ($col = 0; $col < sizeof($ids); ++$col) {
            if ($this->questions->containsKey($ids[$col])){
                $this->questions->get($ids[$col])->addScore($rowData[$col]);
            } else{
                $question = new Question($ids[$col]);
                $question
                    ->setMaxScore($this->maxScores[0][$col])
                    ->setStudentResults($this->studentResults)
                    ->addScore($rowData[$col])
                ;

                $this->questions->set($ids[$col], $question);
            }
        }
    }
}