<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201106104128 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO user (id, name,role) VALUES (1, 'admin', 1)");
        $this->addSql("INSERT INTO user (id, name,role) VALUES (2, 'user1', 2)");
        $this->addSql("INSERT INTO user (id, name,role) VALUES (3, 'bubuLubu', 2)");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DELETE FROM user WHERE id IN (1,2,3)");
    }
}
