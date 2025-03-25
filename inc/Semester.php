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