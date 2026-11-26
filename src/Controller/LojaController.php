<?php

namespace App\Controller;

use App\Repository\CategoriaRepository;
use App\Repository\ProdutoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/loja')]
final class LojaController extends AbstractController
{
    #[Route(name: 'app_loja_index', methods: ['GET'])]
    public function index(
        Request $request,
        ProdutoRepository $produtos,
        CategoriaRepository $categorias,
    ): Response {
        $id = $request->query->filter('categoria', null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
            'flags' => FILTER_NULL_ON_FAILURE,
        ]);
        $categoriaAtual = null !== $id ? $categorias->find($id) : null;

        if (null !== $id && null === $categoriaAtual) {
            throw $this->createNotFoundException('Categoria não encontrada.');
        }

        return $this->render('loja/index.html.twig', [
            'categorias' => $categorias->comTotalAtivos(),
            'categoria_atual' => $categoriaAtual,
            'produtos' => $produtos->vitrine($categoriaAtual),
        ]);
    }
}