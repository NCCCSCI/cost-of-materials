<?php

require_once 'inc/Material.php';

// as long as it is 75% the same - consider it a match
define('MATCH_THRESHOLD', .50);

class Section{
    public string $term;
    public string $courseCode;
    public string $section;
    public string $crn;
    public string $follett;
    public bool $uncertain = false;
    public array $materials;
    
    public function __construct(string $term, string $courseCode, string $section, string $crn,  string $follett) {
        $this->term = $term;
        $this->courseCode = $courseCode;
        $this->section = $section;
        $this->crn = $crn;
        $this->follett = $follett;
        $this->materials = [];
    }
    
    private function makeSignature($publisher, $title, $author) {
        if ($title !== '') {
            // remove common words and the author's name
            /* echo "publisher ";
            var_dump($publisher);
            echo " title ";
            var_dump($title);
            echo " auth ";
            var_dump($author); */
	    $author = addcslashes($author,'/');
	    $publisher = addcslashes($publisher,'/');
            $re1 = preg_replace("/\b(THE|A|OF|IS|AN|AND|($author)|($publisher)|ETEXT|EBOOK|SUBSCRIPTION|\d+-\w+ TERM|INSTANT ACCESS)\b/i", '', $title);
            // remove anything in parentheses
            $re2 = preg_replace('/(\s*\([^)]+\))/', '', $re1);
            // this removes all whitespace, not in use
            //$re3 = preg_replace('/\W/','',$re2);
            $re4 = strtolower($re2);
        } else {
            $re4 = 'no-title';
        }
        return $re4;
    }

    private function matchSignature($signature) {
        $signatureArr = preg_split("/[\s,.:;]+/", $signature);
        foreach (array_keys($this->materials) as $k) {
            $lenK = strlen($k);
            $lenS = strlen($signature);
            if (strstr($k, $signature) !== false ||
                    strstr($signature, $k) !== false) {
                return $k;
            }
            $kArr = preg_split("/[\s,.:;]+/", $k);
            $intersect = array_intersect($signatureArr, $kArr);
            if (count($intersect) >= MATCH_THRESHOLD * count($kArr)) {
                $this->uncertain = true;
                return $k;
            }
        }
        
        return false;
    }

    public function addMaterial($publisher, $title, $author, $priceData) {
        $filtered = array_filter($priceData, function ($v) {
            return !empty($v);
        });
        if (!empty($filtered)) {
            $minPrice = min($filtered);
            $maxPrice = max($filtered);
            
        } else {
           $minPrice = 0;
           $maxPrice = 0; 
        }
        $signature = $this->makeSignature($publisher, $title, $author);
        if ($match = $this->matchSignature($signature)) {
            $material = $this->materials[$match];
            $material->minPrice = min($minPrice, $material->minPrice);
            $material->maxPrice = max($maxPrice, $material->maxPrice);
            if ($match !== $signature && strlen($signature) < strlen($match)) {
                $material->title = ucwords($signature);
                unset($this->materials[$match]);
                $this->materials[$signature] = $material;
            }
        } else {
            $material = new Material();
            $material->title = ucwords($signature);
            $material->author = $author;
            $this->materials[$signature] = $material;
            $material->minPrice = $minPrice;
            $material->maxPrice = $maxPrice;
        }
        
//        if($this->courseCode == "ACCT202N") {
//            var_dump($this->materials);
//            echo "<br>";
//            var_dump($priceData);
//            echo "<br>";
//            var_dump($this->term);
//            echo "<br>";
//        }
        
    }
    
    public function minTotal () {
        $t = 0.0;
        foreach ($this->materials as $m) {
            $t += $m->minPrice;
        }
        return $t;
    }
    
    public function maxTotal () {
        $t = 0.0;
        foreach ($this->materials as $m) {
            $t += $m->maxPrice;
        }
        return $t;
    }
}

