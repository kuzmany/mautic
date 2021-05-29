<?php

namespace Mautic\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20210529183613 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $smsTable = $schema->getTable(MAUTIC_TABLE_PREFIX.'sms_messages');
        if (!$smsTable->hasColumn('properties')) {
            $this->addSql("ALTER TABLE {$this->prefix}sms_messages ADD properties LONGTEXT NOT NULL COMMENT '(DC2Type:json_array)'");
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
