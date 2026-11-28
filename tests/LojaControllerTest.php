<?php

namespace App\Tests;

use App\Entity\Categoria;
use App\Entity\Produto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LojaControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);

        $this->em->getConnection()->executeStatement('DELETE FROM produto');
        $this->em->getConnection()->executeStatement('DELETE FROM categoria');
    }

    public function testVitrineMostraApenasProdutosAtivos(): void
    {
        $categoria = $this->criarCategoria('Celulares');
        $this->criarProduto('iPhone 16', 'IPH16', $categoria);
        $this->criarProduto('iPhone 15', 'IPH15', $categoria, false);

        $this->client->request('GET', '/loja');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Todos os produtos');
        self::assertSelectorTextContains('.loja-vitrine', 'iPhone 16');
        self::assertSelectorTextNotContains('.loja-vitrine', 'iPhone 15');
        self::assertSelectorTextContains('.loja-menu', 'Celulares');
    }

    public function testRaizMostraLoja(): void
    {
        $categoria = $this->criarCategoria('Celulares');
        $this->criarProduto('iPhone 16', 'IPH16', $categoria);

        $this->client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Todos os produtos');
        self::assertSelectorTextContains('.loja-vitrine', 'iPhone 16');
    }

    public function testFiltroPorCategoria(): void
    {
        $celulares = $this->criarCategoria('Celulares');
        $moveis = $this->criarCategoria('Móveis');
        $this->criarProduto('iPhone 16', 'IPH16', $celulares);
        $this->criarProduto('Sofá Cama', 'SOFA1', $moveis);

        $this->client->request('GET', '/loja?categoria='.$moveis->getId());

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Móveis');
        self::assertSelectorTextContains('.loja-vitrine', 'Sofá Cama');
        self::assertSelectorTextNotContains('.loja-vitrine', 'iPhone 16');
    }

    public function testCategoriaInexistenteRetorna404(): void
    {
        $this->client->request('GET', '/loja?categoria=999999');

        self::assertResponseStatusCodeSame(404);
    }

    private function criarCategoria(string $nome): Categoria
    {
        $categoria = (new Categoria())->setNome($nome);
        $this->em->persist($categoria);
        $this->em->flush();

        return $categoria;
    }

    private function criarProduto(string $nome, string $codigo, Categoria $categoria, bool $ativo = true): Produto
    {
        $produto = (new Produto())
            ->setNome($nome)
            ->setCodigo($codigo)
            ->setPreco('100.00')
            ->setCategoria($categoria)
            ->setAtivo($ativo);

        $this->em->persist($produto);
        $this->em->flush();

        return $produto;
    }
}
