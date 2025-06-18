<?php

namespace App\Controller;

use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use UsuarioDto;

#[Route('/api')]
final class UsuariosController extends AbstractController
{
    #[Route('/usuarios', name: 'usuarios_criar', methods: ['POST'])]
    public function criar(
        #[MapRequestPayload(acceptFormat: 'json')]
        UsuarioDto $usuarioDto
    ): JsonResponse
    {
        dd($usuarioDto);


        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UsuariosController.php',
        ]);
    }
}
