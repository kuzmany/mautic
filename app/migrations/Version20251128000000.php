<?php

declare(strict_types=1);

namespace Mautic\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251128000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create form_abandonments table for tracking form abandonment events';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('form_abandonments');

        $table->addColumn('id', 'bigint', [
            'unsigned'      => true,
            'autoincrement' => true,
        ]);

        $table->addColumn('form_id', 'integer', [
            'unsigned' => true,
        ]);

        $table->addColumn('lead_id', 'integer', [
            'unsigned' => true,
            'notnull'  => false,
        ]);

        $table->addColumn('ip_id', 'integer', [
            'unsigned' => true,
            'notnull'  => false,
        ]);

        $table->addColumn('tracking_id', 'string', [
            'length'  => 255,
            'notnull' => false,
        ]);

        $table->addColumn('date_abandoned', 'datetime', [
            'notnull' => true,
        ]);

        $table->addColumn('referer', 'text', [
            'notnull' => false,
        ]);

        $table->addColumn('page_id', 'integer', [
            'unsigned' => true,
            'notnull'  => false,
        ]);

        $table->addColumn('filled_fields', 'json', [
            'notnull' => false,
        ]);

        $table->addColumn('abandonment_percentage', 'integer', [
            'unsigned' => true,
            'default'  => 0,
        ]);

        $table->addColumn('time_spent_seconds', 'integer', [
            'unsigned' => true,
            'default'  => 0,
        ]);

        $table->setPrimaryKey(['id']);
        $table->addIndex(['tracking_id'], 'form_abandonment_tracking_search');
        $table->addIndex(['date_abandoned'], 'form_abandonment_date');
        $table->addIndex(['form_id', 'lead_id'], 'form_abandonment_form_lead');

        $table->addForeignKeyConstraint(
            $schema->getTable('forms'),
            ['form_id'],
            ['id'],
            ['onDelete' => 'CASCADE'],
            'FK_form_abandonments_form'
        );

        $table->addForeignKeyConstraint(
            $schema->getTable('leads'),
            ['lead_id'],
            ['id'],
            ['onDelete' => 'SET NULL'],
            'FK_form_abandonments_lead'
        );

        $table->addForeignKeyConstraint(
            $schema->getTable('ip_addresses'),
            ['ip_id'],
            ['id'],
            ['onDelete' => 'SET NULL'],
            'FK_form_abandonments_ip'
        );

        $table->addForeignKeyConstraint(
            $schema->getTable('pages'),
            ['page_id'],
            ['id'],
            ['onDelete' => 'SET NULL'],
            'FK_form_abandonments_page'
        );
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('form_abandonments');
    }
}
