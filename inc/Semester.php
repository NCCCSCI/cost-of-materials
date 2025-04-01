<?php

class Semester{
    public string $term;
    public array $sections;
    
    public function __construct($term) {
        $this->term = $term;
        $this->sections = [];
    }
    
    public function addSection(object $section) {
        $this->sections[] = $section;
    }
}

function getCurrentSemester() {
    $semesters = ["Spring" => 1, "Summer" => 5, "Fall" => 8];
    $month = date('n');
    
    foreach ($semesters as $k => $v) {
        if ($month >= $v) {
            return $k;
        }
    }
}