<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Entity\Conta;
use App\Repository\UsuarioRepository;
use App\Repository\ContaRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use App\Dto\UsuarioDto;
use App\Dto\UsuarioContaDto;

#[Route('/api')]
final class UsuariosController extends AbstractController
{
    #[Route('/usuarios', name: 'usuarios_criar', methods: ['POST'])]
    public function criar(
        #[MapRequestPayload(acceptFormat: 'json')]
        UsuarioDto $usuarioDto,
        // UsuarioContaDto $usuarioContaDto,

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
        
        $conta = new Conta();
        $conta->setNumero(preg_replace('/\D/', '', uniqid()));
        $conta->setSaldo('0');
        $conta->setUsuario($usuario);
        
        $entityManager->persist($conta);
        $entityManager->flush();

        $usuarioContaDto = new UsuarioContaDto();
        $usuarioContaDto->setId($usuario->getId());
        $usuarioContaDto->setNome($usuario->getNome());
        $usuarioContaDto->setEmail($usuario->getEmail());
        $usuarioContaDto->setTelefone($usuario->getTelefone());
        $usuarioContaDto->setCpf($usuario->getCpf());
        $usuarioContaDto->setNumeroConta($conta->getNumero());  
        $usuarioContaDto->setSaldo($conta->getSaldo());
        
        return $this->json($usuarioContaDto, 201);
    }


    #[Route('/usuarios/{id}', name: 'usuarios_buscar', methods: ['GET'])]
    public function buscarPorId(
        int $id,
        ContaRepository $contaRepository
    ) {
        $conta = $contaRepository->findByUsuarioId($id);

        if(!$conta) {
            return $this->json([
                'message' => 'Conta não encontrada!'
            ], 404);
        }

        $usuarioContaDto = new UsuarioContaDto();
        $usuarioContaDto->setId($conta->getUsuario()->getId());
        $usuarioContaDto->setNome($conta->getUsuario()->getNome());
        $usuarioContaDto->setEmail($conta->getUsuario()->getEmail());
        $usuarioContaDto->setTelefone($conta->getUsuario()->getTelefone());
        $usuarioContaDto->setCpf($conta->getUsuario()->getCpf());
        $usuarioContaDto->setNumeroConta($conta->getNumero());
        $usuarioContaDto->setSaldo($conta->getSaldo());

        return $this->json($usuarioContaDto, 200);
    }
}
