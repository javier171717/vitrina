<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWholesaleToProducts extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('products', [
            'wholesale_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
                'after'      => 'price',
            ],
            'wholesale_qty' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'wholesale_price',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('products', ['wholesale_price', 'wholesale_qty']);
    }
}
