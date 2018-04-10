<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160423184537 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $queries = explode(';', file_get_contents(__DIR__ . 'migration.sql'));
        foreach($queries as $query){
            $this->addSql($query);
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'sqlite',
            'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DELETE FROM criteria');
        $this->addSql('DELETE FROM positive');
        $this->addSql('DELETE FROM negative');
        $this->addSql('DELETE FROM review');
        $this->addSql('DELETE FROM hotel');

    }
}
