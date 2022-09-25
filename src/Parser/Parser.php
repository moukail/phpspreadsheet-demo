<?php declare(strict_types=1);

namespace App\Parser;

use Doctrine\Common\Collections\Collection;

interface Parser
{
    public function __construct();

    public function parse(string $file): void;

    public function getStudentResults(): Collection;

    public function getQuestionResults(): Collection;

    public function prepareStudentResults(): void;

    public function prepareQuestionResults(): void;
}
