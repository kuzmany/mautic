<?php

declare(strict_types=1);

namespace Mautic\MarketplaceBundle\DTO;

class Version
{
    private $license;
    private $homepage;
    private $issues;
    private $time;
    private $require;
    private $keywords;

    /**
     * An average time to run composer require some/package takes between 60 and 90 seconds.
     *
     * In seconds.
     */
    private const COMPOSER_RUNTIME_BASE = 90;

    /**
     * An average time to download one package dependency in the require array.
     *
     * In seconds.
     */
    private const COMPOSER_RUNTIME_DEPENDENCY = 4;

    public function __construct(string $name, string $version, array $license, \DateTimeInterface $time, string $homepage, string $issues, array $require, array $keywords)
    {
        $this->version  = $version;
        $this->license  = $license;
        $this->time     = $time;
        $this->homepage = $homepage;
        $this->issues   = $issues;
        $this->require  = $require;
        $this->keywords = $keywords;
    }

    public static function fromArray(array $array): Version
    {
        return new self(
            $array['name'],
            $array['version'],
            $array['license'],
            new \DateTimeImmutable($array['time']),
            $array['homepage'],
            $array['support']['issues'] ?? '',
            (array) $array['require'],
            (array) $array['keywords']
        );
    }

    /**
     * Returns composer runtime estimation in seconds.
     */
    public function estimateComposerRuntime(): int
    {
        $xdebugCoeficient = 1;

        if (extension_loaded('xdebug')) {
            $xdebugCoeficient = 2;
        }

        return (self::COMPOSER_RUNTIME_BASE + count($this->getRequire()) * self::COMPOSER_RUNTIME_DEPENDENCY) * $xdebugCoeficient;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getLicense(): array
    {
        return $this->license;
    }

    public function getHomepage(): string
    {
        return $this->homepage;
    }

    public function getIssues(): string
    {
        return $this->issues;
    }

    public function getTime(): \DateTimeInterface
    {
        return $this->time;
    }

    public function getRequire(): array
    {
        return $this->require;
    }

    public function getKeywords(): array
    {
        return $this->keywords;
    }
}
