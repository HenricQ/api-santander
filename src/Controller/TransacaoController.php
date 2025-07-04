<?php

namespace App\Controller;

use App\Entity\Conta;
use App\Entity\Transacao;
use App\Repository\ContaRepository;
use App\Repository\TransacaoRepository;
use App\Dto\TransacaoDto;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class TransacaoController extends AbstractController
{
    #[Route('/transacao', name: 'transacao_realizar', methods: ['POST'])]
public function realizar(
    #[MapRequestPayload(acceptFormat: 'json')]
    TransacaoDto $entrada,
    ContaRepository $contaRepository,
    EntityManagerInterface $entityManager,
): JsonResponse 
{

    // .1 validação das entradas    / id / destinoId / origemId
    $erros = [];
    $entrada->setValor((float) $entrada->getValor());

    if(empty($entrada->getValor())){
        $erros[] = ['message' => 'Insira um Valor de Transação'];
    }
    if($entrada->getValor() <= 0){
        $erros[] = ['message' => 'Insira um Valor maior que zero'];
    }
    if(empty($entrada->getContaOrigemId())){
        $erros[] = ['message' => 'Insira um ID válido de conta de origem'];
    }
    if(empty($entrada->getContaDestinoId())){
        $erros[] = ['message' => 'Insira um ID válido de conta de destino'];
    }
    if((int) $entrada->getContaDestinoId() === (int) $entrada->getContaOrigemId()){
        $erros[] = ['message' => 'Pô campeão, não pode enviar dinheiro pra própia conta'];
    }
    
    if (count($erros) > 0){
        return $this->json($erros, 400); 
    }

    $contaOrigem = $contaRepository->findByUsuarioId((int) $entrada->getContaOrigemId());
    $contaDestino = $contaRepository->findByUsuarioId((int) $entrada->getContaDestinoId());

    if(empty($contaDestino)){
        return $this->json([
            'message' => 'Conta de Destino Não Existe no banco'
        ], 404 );
    }
    if(empty($contaOrigem)){
        return $this->json([
            'message' => 'Conta de Origem Não Existe no banco'
        ], 404 );
    }

    if($contaOrigem->getSaldo() < $entrada->getValor()){
        return $this->json([
            'message' => 'Saldo Insuficiente na conta de Origem'
        ], 409 );
    }

    $contaRepository->debitar($contaOrigem, $entrada->getValor());
    $contaRepository->depositar($contaDestino, $entrada->getValor());

    $transacao = new Transacao();
    $transacao->setDataHora(new DateTime());
    $transacao->setValor($entrada->getValor());
    $transacao->setContaOrigem($contaOrigem);
    $transacao->setContaDestino($contaDestino);
    $entityManager->persist($transacao);

    $entityManager->flush();
    
    
    $mensagem = sprintf('Transação no valor de R$ %s realizada com sucesso!',
                        number_format($entrada->getValor(), 2, '.', ','));
    return $this->json([
        'message' => $mensagem
    ], 200);
}

}
