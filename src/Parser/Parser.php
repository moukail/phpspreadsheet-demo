<?php

namespace App\Parser;

use Doctrine\Common\Collections\Collection;

interface Parser
{
    public function __construct(string $file);
    public function getStudentResults(): Collection;
    public function getQuestionResults(): Collection;
    public function prepareStudentResults();
    public function prepareQuestionResults();
}