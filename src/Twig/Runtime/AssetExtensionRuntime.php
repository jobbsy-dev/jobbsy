<?php

namespace App\Twig\Runtime;

use League\Glide\Signatures\SignatureFactory;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\RuntimeExtensionInterface;

readonly class AssetExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        #[Autowire('%env(GLIDE_KEY)%')]
        private string $secret
    ) {
    }

    public function assetUrl(
        string $path,
        array $parameters = [],
        $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        $parameters['s'] = SignatureFactory::create($this->secret)->generateSignature($path, $parameters);
        $parameters['path'] = ltrim($path, '/');

        return $this->urlGenerator->generate('asset_url', $parameters, $referenceType);
    }
}
