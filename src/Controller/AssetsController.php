<?php

namespace App\Controller;

use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\Server;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;

final class AssetsController extends AbstractController
{
    public function __construct(
        private readonly Server $glide,
        #[Autowire('%env(GLIDE_KEY)%')]
        private readonly string $secret
    ) {
    }

    #[Route('/assets/{path}', name: 'asset_url', requirements: ['path' => '.+'], methods: ['GET'])]
    #[Cache(maxage: 86400, smaxage: 86400)]
    public function assets(string $path, Request $request): Response
    {
        $parameters = $request->query->all();

        if ([] !== $parameters) {
            try {
                SignatureFactory::create($this->secret)->validateRequest($path, $parameters);
            } catch (SignatureException $e) {
                throw $this->createNotFoundException('', $e);
            }
        }

        $this->glide->setResponseFactory(new SymfonyResponseFactory($request));

        try {
            $response = $this->glide->getImageResponse($path, $request->query->all());
        } catch (FileNotFoundException $e) {
            throw $this->createNotFoundException('', $e);
        }

        return $response;
    }
}
