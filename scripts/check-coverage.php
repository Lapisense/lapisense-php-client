<?php

$file = $argv[1] ?? 'build/coverage.xml';

if (!file_exists($file)) {
    echo "Coverage file not found: {$file}\n";
    exit(1);
}

$xml = simplexml_load_file($file);
$statements = (int) $xml->project->metrics['statements'];
$covered = (int) $xml->project->metrics['coveredstatements'];
$rate = $statements > 0 ? $covered / $statements : 0;

echo sprintf("Coverage: %s/%s (%.2f%%)\n", $covered, $statements, $rate * 100);

if ($rate < 1.0) {
    exit(1);
}
