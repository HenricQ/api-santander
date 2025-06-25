<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
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
        UsuarioDto $usuarioDto,

        EntityManagerInterface $entityManager,
        UsuarioRepository $usuarioRepository

    ): JsonResponse
    {
        // dd($usuarioDto);
        $erros = [];
        if (empty($usuarioDto->getNome())) {
            $erros[] = ['message' => 'O nome é obrigatório!'];
        }
        if (empty($usuarioDto->getEmail())) {
            $erros[] = ['message' => 'O email é obrigatório!'];
        }
        if (empty($usuarioDto->getSenha())) {
            $erros[] = ['message' => 'A senha é obrigatória!'];
        }
        if (empty($usuarioDto->getTelefone())) {
            $erros[] = ['message' => 'O telefone é obrigatório!'];
        }
        if (empty($usuarioDto->getCpf())) {
            $erros[] = ['message' => 'O CPF é obrigatório!'];
        }
        if (count($erros) > 0){
            return $this->json($erros, 422); 
        }

        $usuarioExistente = $usuarioRepository->findByCpf($usuarioDto->getCpf());
        // dd($usuarioExistente);
        if ($usuarioExistente) {
            return $this->json([
                'message' => 'Já existe um usuário com esse CPF!'
            ], 409);
        }

        // converte o DTO para a entidade Usuario
        $usuario = new Usuario();
        $usuario->setNome($usuarioDto->getNome());
        $usuario->setEmail($usuarioDto->getEmail());
        //$usuario->setSenha(password_hash($usuarioDto->getSenha(), PASSWORD_BCRYPT));
        $usuario->setSenha($usuarioDto->getSenha());
        $usuario->setTelefone($usuarioDto->getTelefone());
        $usuario->setCpf($usuarioDto->getCpf());

        // cria o registro no banco de dados
        $entityManager->persist($usuario);
        $entityManager->flush();

        return $this->json($usuario);
    }
}
