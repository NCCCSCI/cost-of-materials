<?php

require_once 'inc/Section.php';


class Material {
    
    public string $author;
    public string $title;
    public string $courseCode;
    public string $section;
    public float $minPrice = 0;
    public float $maxPrice = 0;

    /*public function __construct(string $courseCode, string $section, string $author, string $title) {
        $this->courseCode = $courseCode;
        $this->section = $section;
        $this->author = $author;
        $this->title = $title;
    } */
}
