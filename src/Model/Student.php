<?php declare(strict_types=1);

namespace App\Model;

class Student
{
    private string $result;
    private float $grade;
    private float $percentage;
    private float $totalScore;
    private float $maxTotalScore;

    public function __construct(private string $id)
    {
    }

    public function setTotalScore(float $totalScore): self
    {
        $this->totalScore = $totalScore;

        return $this;
    }

    public function setMaxTotalScore(float $maxTotalScore): self
    {
        $this->maxTotalScore = $maxTotalScore;

        return $this;
    }

    public function prepareResult(): void
    {
        if ($this->maxTotalScore === 0) {
            return;
        }

        $this->calculatePercentage();
        $this->calculateGrade();

        $this->result = $this->percentage >= 70 ? 'Passed' : 'Faild';
    }

    private function calculatePercentage(): void
    {
        $this->percentage = round($this->totalScore / $this->maxTotalScore * 100, 1);
    }

    private function calculateGrade(): void
    {
        $grade = 0;

        if ($this->percentage <= 20 / 100) {
            $grade = 1.0;
        }

        if ($this->percentage > 20 && $this->percentage < 70) {
            $grade = 1 + ($this->percentage - 20) * (5.5 - 1) / 50;
        }

        if ($this->percentage >= 70) {
            $grade = 5.5 + ($this->percentage - 70) * (10 - 5.5) / 30;
        }

        $this->grade = round($grade, 1);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'grade' => $this->grade,
            'percentage' => $this->percentage . '%',
            'result' => $this->result,
        ];
    }
}
