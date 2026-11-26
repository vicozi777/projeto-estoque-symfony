<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261126190356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Sincroniza a tabela produto com o mapeamento da entidade (categoria_id).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE produto ADD categoria_id INT DEFAULT NULL, DROP imagem_url');

        // Os produtos já cadastrados precisam de uma categoria antes de a coluna virar NOT NULL.
        $this->addSql("UPDATE produto p JOIN categoria c ON c.nome = 'Celulares' SET p.categoria_id = c.id WHERE p.nome LIKE '%iPhone%'");
        $this->addSql("UPDATE produto p JOIN categoria c ON c.nome = 'Móveis' SET p.categoria_id = c.id WHERE p.nome LIKE '%Sofá%'");
        $this->addSql('UPDATE produto SET categoria_id = (SELECT id FROM categoria ORDER BY id LIMIT 1) WHERE categoria_id IS NULL');

        $this->addSql('ALTER TABLE produto CHANGE categoria_id categoria_id INT NOT NULL');
        $this->addSql('ALTER TABLE produto ADD CONSTRAINT FK_5CAC49D73397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)');
        $this->addSql('CREATE INDEX IDX_5CAC49D73397707A ON produto (categoria_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE produto DROP FOREIGN KEY FK_5CAC49D73397707A');
        $this->addSql('DROP INDEX IDX_5CAC49D73397707A ON produto');
        $this->addSql('ALTER TABLE produto ADD imagem_url VARCHAR(500) DEFAULT NULL, DROP categoria_id');
    }
}
