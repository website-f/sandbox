<?php

namespace Database\Seeders;

use App\Models\CollectionType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CollectionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Collection Types for each Sandbox account type:
     *
     * SANDBOX USAHAWAN (Entrepreneurs):
     * - geran_asas: Starter grant, target RM600
     * - tabung_usahawan: Business savings (unlimited)
     * - had_pembiayaan: Financing limit (unlimited)
     *
     * SANDBOX REMAJA (Youth 11-20 years):
     * - biasiswa_pemula: Starter scholarship, target RM600
     * - had_biasiswa: Scholarship limit (max RM150,000)
     * - dana_usahawan_muda: Young entrepreneur fund (max RM350,000)
     *
     * SANDBOX AWAM (General Public):
     * - modal_pemula: Starter capital, target RM600
     * - had_pembiayaan_hutang: Debt financing limit (unlimited)
     * - khairat_kematian: Death benefit fund (max RM50,000)
     */
    public function run(): void
    {
        $collectionTypes = [
            // ================================
            // SANDBOX USAHAWAN COLLECTIONS
            // ================================
            [
                'code' => 'geran_asas',
                'name' => 'Geran Asas',
                'account_type' => CollectionType::ACCOUNT_SANDBOX_USAHAWAN,
                'limit' => null, // No limit
                'target' => 60000, // RM600.00 target (in cents)
                'is_starter' => true,
                'description' => 'Geran permulaan untuk ahli Sandbox Usahawan. Perlu mencapai RM600 untuk mengaktifkan tabung lain.',
                'sort_order' => 1,
            ],
            [
                'code' => 'tabung_usahawan',
                'name' => 'Tabung Usahawan',
                'account_type' => CollectionType::ACCOUNT_SANDBOX_USAHAWAN,
                'limit' => null, // Unlimited
                'target' => null,
                'is_starter' => false,
                'description' => 'Tabung simpanan untuk modal perniagaan.',
                'sort_order' => 2,
            ],
            [
                'code' => 'had_pembiayaan',
                'name' => 'Had Pembiayaan',
                'account_type' => CollectionType::ACCOUNT_SANDBOX_USAHAWAN,
                'limit' => null, // Unlimited
                'target' => null,
                'is_starter' => false,
                'description' => 'Had pembiayaan perniagaan yang layak dipohon.',
                'sort_order' => 3,
            ],

            // ================================
            // SANDBOX REMAJA COLLECTIONS
            // ================================
            [
                'code' => 'biasiswa_pemula',
                'name' => 'Biasiswa Pemula',
                'account_type' => CollectionType::ACCOUNT_SANDBOX_REMAJA,
                'limit' => null,
                'target' => 60000, // RM600.00 target (in cents)
                'is_starter' => true,
                'description' => 'Biasiswa permulaan untuk ahli Sandbox Remaja. Perlu mencapai RM600 untuk mengaktifkan tabung lain.',
                'sort_order' => 1,
            ],
            [
                'code' => 'had_biasiswa',
                'name' => 'Had Biasiswa',
                'account_type' => CollectionType::ACCOUNT_SANDBOX_REMAJA,
                'limit' => 15000000, // RM150,000.00 (in cents)
                'target' => null,
                'is_starter' => false,
                'description' => 'Had biasiswa pendidikan maksimum RM150,000.',
                'sort_order' => 2,
            ],
            [
                'code' => 'dana_usahawan_muda',
                'name' => 'Dana Usahawan Muda',
                'account_type' => CollectionType::ACCOUNT_SANDBOX_REMAJA,
                'limit' => 35000000, // RM350,000.00 (in cents)
                'target' => null,
                'is_starter' => false,
                'description' => 'Dana untuk usahawan muda maksimum RM350,000.',
                'sort_order' => 3,
            ],

            // ================================
            // SANDBOX AWAM COLLECTIONS
            // ================================
            [
                'code' => 'modal_pemula',
                'name' => 'Modal Pemula',
                'account_type' => CollectionType::ACCOUNT_SANDBOX_AWAM,
                'limit' => null,
                'target' => 60000, // RM600.00 target (in cents)
                'is_starter' => true,
                'description' => 'Modal permulaan untuk ahli Sandbox Awam. Perlu mencapai RM600 untuk mengaktifkan tabung lain.',
                'sort_order' => 1,
            ],
            [
                'code' => 'had_pembiayaan_hutang',
                'name' => 'Had Pembiayaan Hutang',
                'account_type' => CollectionType::ACCOUNT_SANDBOX_AWAM,
                'limit' => null, // Unlimited
                'target' => null,
                'is_starter' => false,
                'description' => 'Had pembiayaan untuk penyelesaian hutang.',
                'sort_order' => 2,
            ],
            [
                'code' => 'khairat_kematian',
                'name' => 'Khairat Kematian',
                'account_type' => CollectionType::ACCOUNT_SANDBOX_AWAM,
                'limit' => 5000000, // RM50,000.00 (in cents)
                'target' => null,
                'is_starter' => false,
                'description' => 'Dana khairat kematian maksimum RM50,000.',
                'sort_order' => 3,
            ],
        ];

        foreach ($collectionTypes as $type) {
            CollectionType::updateOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}
