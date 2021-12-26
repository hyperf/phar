<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Hyperf\Phar;

use Phar;
use Symfony\Component\Finder\Finder;
use Traversable;

class TargetPhar
{
    public function __construct(private Phar $phar, private PharBuilder $pharBuilder)
    {
        $phar->startBuffering();
    }

    public function __toString(): string
    {
        $exploded = explode('/', $this->phar->getPath());
        return end($exploded);
    }

    /**
     * Start writing the Phar package.
     */
    public function stopBuffering(): void
    {
        $this->phar->stopBuffering();
    }

    /**
     * Add a resource bundle to the Phar package.
     */
    public function addBundle(Bundle $bundle): void
    {
        /** @var Finder|string $resource */
        foreach ($bundle as $resource) {
            if (is_string($resource)) {
                $this->addFile($resource);
            } else {
                $this->buildFromIterator($resource);
            }
        }
    }

    /**
     * Add the file to the Phar package.
     */
    public function addFile(string $filename): void
    {
        $this->phar->addFile($filename, $this->pharBuilder->getPathLocalToBase($filename));
    }

    /**
     * Add folder resources to the Phar package.
     */
    public function buildFromIterator(Traversable $iterator): void
    {
        /* @phpstan-ignore-next-line */
        $this->phar->buildFromIterator($iterator, $this->pharBuilder->getPackage()->getDirectory());
    }

    /**
     * Create the default execution file.
     */
    public function createDefaultStub(string $indexFile, string $webIndexFile = null): string
    {
        $params = [$indexFile];
        if ($webIndexFile != null) {
            $params[] = $webIndexFile;
        }

        return '#!/usr/bin/env php' . PHP_EOL . $this->phar->createDefaultStub(...$params);
    }

    /**
     * Set the default startup file.
     */
    public function setStub(string $stub): void
    {
        $this->phar->setStub($stub);
    }

    /**
     * Add a string to the Phar package.
     */
    public function addFromString(string $local, string $contents): void
    {
        $this->phar->addFromString($local, $contents);
    }
}
