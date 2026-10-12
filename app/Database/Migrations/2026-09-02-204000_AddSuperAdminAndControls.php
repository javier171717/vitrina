<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSuperAdminAndControls extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'owner',
                'after'      => 'password_hash',
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'role',
            ],
        ]);

        $this->forge->addColumn('businesses', [
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'wholesale_min_total',
            ],
        ]);

        $this->forge->addField([
            'key'   => ['type' => 'VARCHAR', 'constraint' => 80],
            'value' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('key', true);
        $this->forge->createTable('settings');

        $this->db->table('settings')->insert([
            'key'   => 'registration_open',
            'value' => '0',
        ]);

        $email = 'admin@vitrina.local';
        if ($this->db->table('users')->where('email', $email)->countAllResults() === 0) {
            $now = date('Y-m-d H:i:s');
            $this->db->table('users')->insert([
                'name'          => 'Super admin',
                'email'         => $email,
                'password_hash' => password_hash('admin123456', PASSWORD_DEFAULT),
                'role'          => 'admin',
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }
    }

    public function down(): void
    {
        $this->db->table('users')->where('email', 'admin@vitrina.local')->delete();
        $this->forge->dropTable('settings', true);
        $this->forge->dropColumn('businesses', 'is_active');
        $this->forge->dropColumn('users', ['role', 'is_active']);
    }
}
