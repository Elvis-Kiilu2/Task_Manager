<?php
$directory = new RecursiveDirectoryIterator(__DIR__);
$iterator = new RecursiveIteratorIterator($directory);
$regex = '/require_once\s*\(\s*__DIR__\s*\.\s*[\'"]\/\.\.\/includes\/(.*?)["\']\s*\);/';

foreach ($iterator as $file) {
    if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        $content = file_get_contents($file);
        $updated = preg_replace_callback($regex, function ($matches) {
            return "require_once __DIR__ . '/includes/" . $matches[1] . "';";
        }, $content);

        if ($updated !== $content) {
            file_put_contents($file, $updated);
            echo "✔️ Updated: " . $file->getPathname() . "\n";
        }
    }
}
