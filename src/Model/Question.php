<?php declare(strict_types=1);

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Question
{
    private Collection $scores;
    private int $maxScore;
    private float $pValue = 0;
    private float $rValue = 0;
    private Collection $studentsResults;

    public function __construct(private string $id)
    {
        $this->scores = new ArrayCollection();
    }

    public function setMaxScore(int $maxScore): self
    {
        $this->maxScore = $maxScore;
        return $this;
    }

    public function setStudentResults(Collection $studentsResults): self
    {
        $this->studentsResults = $studentsResults;
        return $this;
    }

    public function addScore(float $score): void
    {
        $this->scores->add($score);
    }

    public function calculatePValue(): void
    {
        if ($this->maxScore === 0 || $this->scores->isEmpty()) {
            return;
        }

        $sumScores = array_sum($this->scores->toArray());
        $average = $sumScores / $this->scores->count();
        $this->pValue = $average / $this->maxScore;
    }

    public function calculateRValue(): void
    {
        $scores = $this->scores->toArray();
        $studentsResults = array_column($this->studentsResults->toArray(), 'grade');

        $t = $this->scores->count();

        $x = array_sum($scores);
        $y = array_sum($studentsResults);

        $x2 = $this->x2($scores);
        $y2 = $this->y2($studentsResults);
        $xy = $this->xy($scores, $studentsResults);

        $division = sqrt((($t * $x2) - pow($x, 2)) * (($t * $y2) - pow($y, 2)));

        if ($division == 0) {
            $this->rValue = 0;
            return;
        }

        $this->rValue = (($t * $xy) - ($x * $y)) / $division;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'pValue' => round($this->pValue, 1),
            'rValue' => round($this->rValue, 1),
        ];
    }

    /**
     * @param array $scores
     * @return float|int
     */
    private function x2(array $scores): int|float
    {
        return array_sum(
            array_map(function ($score) {
                return $score ** 2;
            }, $scores));
    }

    /**
     * @param array $studentsResults
     * @return float|int
     */
    private function y2(array $studentsResults): int|float
    {
        return array_sum(
            array_map(function ($result) {
                return $result ** 2;
            }, $studentsResults));
    }

    /**
     * @param array $scores
     * @param array $studentsResults
     * @return float|int
     */
    private function xy(array $scores, array $studentsResults): int|float
    {
        return array_sum(
            array_map(function ($x, $y) {
                return ($x * $y);
            }, $scores, $studentsResults));
    }
}
