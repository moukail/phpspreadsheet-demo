<?php

namespace App\Model;

class Student
{
    private string $id;
    private float $grade;
    private float $percentage;
    private string $result;
    private float $totalScore;
    private int $maxTotalScore;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function setTotalScore(float $totalScore): self
    {
        $this->totalScore = $totalScore;
        return $this;
    }

    public function setMaxTotalScore(int $maxTotalScore): self
    {
        $this->maxTotalScore = $maxTotalScore;
        return $this;
    }

    public function calculateGrade()
    {
        $grade = 0;

        if ($this->maxTotalScore == 0){
            return 0;
        }

        $calc = ($this->totalScore / $this->maxTotalScore) *100;

        if($calc <= 20/100){
            $grade = 1.0;
        }

        if($calc > 20 && $calc < 70){
            $grade = 1 + ($calc - 20) * (5.5 - 1)/50;
        }

        if($calc >= 70){
            $grade = 5.5 + ($calc - 70) * (10 - 5.5)/30;
        }

        $this->percentage = round($calc, 1);
        $this->result = ($calc >= 70) ? 'Passed': 'Faild';
        $this->grade = round($grade, 1);
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'grade' => $this->grade,
            'percentage' => $this->percentage,
            'result' => $this->result,
        ];
    }
}