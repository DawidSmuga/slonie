<?php

/**
 * 
 * Solution to task `Słonie` in PHP.
 * @author Dawid Smuga <dawidsmuga@icloud.com> 
 * PHP ^7.4
 * 
 * */

class Elephants
{
    private int $elephantsCount;
    private int $lightest;
    private int $heaviest;
    private int $result = 0;
    private array $weights = [];
    private array $originOrder = [];
    private array $permutation = [];
    private array $verified = [];

    /**
     * Checks a file exists and load file into array, than pass data to other method.
     *
     * @param string $file
     * @return void
     */
    public function loadFile(string $file): void
    {
        if (!file_exists($file))
            throw new Exception("File {$file} doesn't exists.");

        $lines = file($file, FILE_IGNORE_NEW_LINES);

        $this->prepareData($lines);
    }

    /**
     * Parse array and assign data to properties.
     *
     * @param array $lines
     * @return void
     */
    private function prepareData(array $lines): void
    {
        // first line - count of elephants
        $this->elephantsCount = intval($lines[0]);
        // second line - weights
        $this->weights = array_map(fn ($value) => intval($value), explode(' ', $lines[1]));
        // find lightest one
        $this->lightest = min($this->weights);
        // find heaviest one
        $this->heaviest = max($this->weights);
        // third line -  origin order
        $this->originOrder = array_map(fn ($value) => intval($value) - 1, explode(' ', $lines[2]));
        // fourth line - permutation
        $target = array_map(fn ($value) => intval($value) - 1, explode(' ', $lines[3]));
        foreach ($target as $key => $value) {
            $this->permutation[$value] = $this->originOrder[$key];
        }
        // set default value of verification for all positions to false
        for ($i = 0; $i < $this->elephantsCount; $i++) {
            $this->verified[$i] = false;
        }
    }

    /**
     * Effort calculations.
     *
     * @return integer
     */
    public function resolve(): int
    {
        // start loop
        for ($i = 0; $i < $this->elephantsCount; $i++) {
            // verify is before cycle
            if (!$this->verified[$i]) {
                // initialize variables before cycle
                $minWeightInCycle = $this->heaviest + 1;
                $weightInCycle = 0;
                $current = $i;
                $cycleLength = 0;
                // cycle
                while (true) {
                    $minWeightInCycle = min($minWeightInCycle, $this->weights[$current]);
                    $weightInCycle += $this->weights[$current];
                    $current = $this->permutation[$current];
                    $this->verified[$current] = true;
                    $cycleLength++;
                    if ($current == $i) break;
                }
                // add less cost method
                $this->result += min(
                    $weightInCycle + ($cycleLength - 2) * $minWeightInCycle,
                    $weightInCycle + $minWeightInCycle + ($cycleLength + 1) * $this->lightest
                );
            }
        }
        return $this->result;
    }
}

try {
    // create new object
    $elephant = new Elephants();
    // if CLI you can choose file by -> php index.php 'file_name.in'
    $file = $argv[1] ?? 'slo1.in'; // <- if not, will be loaded default 
    $elephant->loadFile($file);
    // display result 
    echo $elephant->resolve();
} catch (Exception $e) {
    // exception if can't find file
    echo $e->getMessage();
}
