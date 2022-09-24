<?php

namespace App\Parser;

use App\Model\Question;
use App\Model\Student;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class XlsxParser implements Parser
{
    private array $questionIds;
    private array $maxScores;
    private ArrayCollection $studentResults;
    private ArrayCollection $questionResults;

    private Worksheet $sheet;
    private int $highestRow;
    private string $highestColumn;

    public function __construct(string $file)
    {
        $this->studentResults = new ArrayCollection();
        $this->questionResults = new ArrayCollection();

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        $this->sheet = $spreadsheet->getSheet(0);
        $this->highestRow = $this->sheet->getHighestDataRow();
        $this->highestColumn = $this->sheet->getHighestDataColumn();

        $this->questionIds = $this->sheet->rangeToArray('B1:'.$this->highestColumn.'1', null, true, false);
        $this->maxScores = $this->sheet->rangeToArray('B2:'.$this->highestColumn.'2', null, true, false);
    }

    /**
     * @return Collection
     */
    public function getStudentResults(): Collection
    {
        return $this->studentResults;
    }

    /**
     * @return Collection
     */
    public function getQuestionResults(): Collection
    {
        return $this->questionResults;
    }

    private function getRowData($row)
    {
        return $this->sheet->rangeToArray('A'.$row.':'.$this->highestColumn.$row, null, true, false);
    }

    public function prepareStudentResults(): void
    {
        //$progress = $this->climate->progress()->total($this->highestRow);

        for ($row = 3; $row <= $this->highestRow; ++$row) {

            //$progress->current($row, 'loading');

            $rowData = $this->getRowData($row);

            $student = (new Student(
                array_shift($rowData[0])
            ))
                ->setMaxTotalScore(array_sum($this->maxScores[0]))
                ->setTotalScore(array_sum($rowData[0]));

            $student->prepareResult();

            $this->studentResults->add($student->toArray());
        }
    }

    public function prepareQuestionResults(): void
    {
        // prepare questions collection
        for ($row = 3; $row <= $this->highestRow; ++$row) {
            $rowData = $this->getRowData($row);
            array_shift($rowData[0]);
            $this->prepareQuestionsData($this->questionIds[0], $rowData[0]);
        }

        /** @var Question $next */
        while ($next = $this->questionResults->next()){
            $next->calculatePValue();
            $next->calculateRValue();
        }

        $this->questionResults = $this->questionResults->map(function (Question $question){
            return $question->toArray();
        });
    }

    /**
     * @param ArrayCollection $questions
     * @param $ids
     * @param $rowData
     * @return void
     */
    private function prepareQuestionsData(array $ids, $rowData): void
    {
        for ($col = 0; $col < sizeof($ids); ++$col) {

            $questionId = strval($ids[$col]);
            $score = $rowData[$col];
            $question = $this->questionResults->get($questionId);

            if ($question){
                $question->addScore($score);
                continue;
            }

            $question = new Question($questionId);
            $question
                ->setMaxScore($this->maxScores[0][$col])
                ->setStudentResults($this->studentResults)
                ->addScore($score)
            ;

            $this->questionResults->set($questionId, $question);
        }
    }
}