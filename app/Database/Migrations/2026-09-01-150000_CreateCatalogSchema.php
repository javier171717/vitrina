<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCatalogSchema extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'          => ['type' => 'VARCHAR', 'constraint' => 120],
            'email'         => ['type' => 'VARCHAR', 'constraint' => 190],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('users');

        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'        => ['type' => 'INT', 'unsigned' => true],
            'name'           => ['type' => 'VARCHAR', 'constraint' => 160],
            'slug'           => ['type' => 'VARCHAR', 'constraint' => 160],
            'tagline'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'phone'          => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'email'          => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'address'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'city'           => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'logo'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'banner'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'banner_mobile'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'primary_color'  => ['type' => 'VARCHAR', 'constraint' => 7, 'default' => '#e30613'],
            'instagram'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'facebook'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'tiktok'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'show_prices'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('businesses');

        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'business_id' => ['type' => 'INT', 'unsigned' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 120],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 120],
            'phone'       => ['type' => 'VARCHAR', 'constraint' => 40],
            'coverage'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sort_order'  => ['type' => 'INT', 'default' => 0],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['business_id', 'slug']);
        $this->forge->addForeignKey('business_id', 'businesses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('advisors');

        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'business_id' => ['type' => 'INT', 'unsigned' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 120],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 120],
            'sort_order'  => ['type' => 'INT', 'default' => 0],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['business_id', 'slug']);
        $this->forge->addForeignKey('business_id', 'businesses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('categories');

        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'business_id' => ['type' => 'INT', 'unsigned' => true],
            'category_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 190],
            'sku'         => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'compatible'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'price'       => ['type' => 'DECIMAL', 'constraint' => '12,2', 'null' => true],
            'image'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['business_id', 'is_active']);
        $this->forge->addKey('category_id');
        $this->forge->addForeignKey('business_id', 'businesses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('products');
    }

    public function down(): void
    {
        $this->forge->dropTable('products', true);
        $this->forge->dropTable('categories', true);
        $this->forge->dropTable('advisors', true);
        $this->forge->dropTable('businesses', true);
        $this->forge->dropTable('users', true);
    }
}
