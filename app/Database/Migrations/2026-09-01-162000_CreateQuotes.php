<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuotes extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'uuid'        => ['type' => 'CHAR', 'constraint' => 36],
            'business_id' => ['type' => 'INT', 'unsigned' => true],
            'advisor_id'  => ['type' => 'INT', 'unsigned' => true],
            'items'       => ['type' => 'JSON'],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('uuid');
        $this->forge->addForeignKey('business_id', 'businesses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('advisor_id', 'advisors', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('quotes');
    }

    public function down(): void
    {
        $this->forge->dropTable('quotes', true);
    }
}
