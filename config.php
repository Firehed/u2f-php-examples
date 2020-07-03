<?php
declare(strict_types=1);

    $builder = new Firehed\Container\Builder();

$definitions = glob('config/*.php');
assert($definitions !== false);
foreach ($definitions as $definitionFile) {
    $builder->addFile($definitionFile);
}

return $builder->build();

