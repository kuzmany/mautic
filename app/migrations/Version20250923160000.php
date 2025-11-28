<?php

declare(strict_types=1);

namespace Mautic\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Mautic\CoreBundle\Doctrine\PreUpAssertionMigration;
use Mautic\FormBundle\Entity\AbandonedSubmission;

final class Version20250923160000 extends PreUpAssertionMigration
{
    protected function preUpAssertions(): void
    {
        $this->skipAssertion(
            fn (Schema $schema) => $schema->hasTable($this->getPrefixedTableName(AbandonedSubmission::TABLE_NAME)),
            sprintf('Table %s already exists', AbandonedSubmission::TABLE_NAME)
        );
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable($this->getPrefixedTableName(AbandonedSubmission::TABLE_NAME));

        $table->addColumn('id', Types::BIGINT, ['autoincrement' => true, 'unsigned' => true]);
        $table->addColumn('form_id', Types::INTEGER, ['unsigned' => true]);
        $table->addColumn('ip_id', Types::INTEGER, ['unsigned' => true, 'notnull' => false]);
        $table->addColumn('lead_id', Types::INTEGER, ['unsigned' => true, 'notnull' => false]);
        $table->addColumn('date_modified', Types::DATETIME_MUTABLE);
        $table->addColumn('data', Types::TEXT, ['notnull' => true]);
        $table->addColumn('session_id', Types::STRING, ['length' => 191, 'notnull' => false]);

        $table->setPrimaryKey(['id']);
        $table->addIndex(['date_modified'], 'form_abandoned_date_modified');
        $table->addIndex(['session_id'], 'form_abandoned_session_id');

        $table->addForeignKeyConstraint(
            $this->getPrefixedTableName('forms'),
            ['form_id'],
            ['id'],
            ['onDelete' => 'CASCADE'],
            'FK_FORM_ABANDONED_FORM'
        );

        $table->addForeignKeyConstraint(
            $this->getPrefixedTableName('ip_addresses'),
            ['ip_id'],
            ['id'],
            ['onDelete' => 'SET NULL'],
            'FK_FORM_ABANDONED_IP'
        );

        $table->addForeignKeyConstraint(
            $this->getPrefixedTableName('leads'),
            ['lead_id'],
            ['id'],
            ['onDelete' => 'SET NULL'],
            'FK_FORM_ABANDONED_LEAD'
        );
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable($this->getPrefixedTableName(AbandonedSubmission::TABLE_NAME));
    }
}
