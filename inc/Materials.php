<?php

class Materials {

    function __construct(string $code, string $title, float $newTail, float $usedTail, float $newRent, float $usedRent) {
        $this->code = $code;
        $this->title = $title;
        $arr = [$newTail, $usedTail, $newRent, $usedRent];
        $validPrice = array_filter($arr, function ($V) {
            return !empty($V);
        });
        if (!empty($validPrice)) {
            $this->low = min($validPrice);
            $this->high = max($validPrice);
        } else {
            $this->low = 0.0;
            $this->high = 0.0;
        }
    }

    function signature($title){
	if ($title !== '') {
		$re1 = preg_replace("/\b(THE|A|OF|IS|AN|AND|)\b/i",'',$title);
		$re2 = preg_replace('/(\s*\([^)]+\))/','',$re1);
		$re3 = preg_replace('/\W/','',$re2);
		$re4 = strtoupper($re3);
	} else {
		$re4 = 'no-title';
	}
	return $re4;
    }
}

$data = file("inc/python.txt");
$sectionMaterials = [];
foreach ($data as $t) {
    echo '*'.trim($t).' '.signature(trim($t)).PHP_EOL;
    $arr = explode(' ', signature(trim($t)));
    $sectionMaterials[] = $arr;
}

var_dump(array_intersect($sectionMaterials));