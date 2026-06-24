<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class AssignUncoveredFoamGroupSeeder extends Seeder
{
    public function run(): void
    {
        // Companies that should be marked as uncovered foam
        $uncoveredCompanies = [
            'MASTER FOAM UNCOVERED',
            'DURA FOAM UNCOVERED',
            'Diamond Foam UNCOVERED',
            'Style Foam Uncoverd',
            'Citi Foam uncovered',
            'Jumbolon Uncoverd',
        ];

        foreach ($uncoveredCompanies as $name) {
            Company::where('name', $name)
                ->update(['group_key' => 'uncovered_foam']);
        }

        // Also mark "Jumbolon Coverd" as covered foam
        Company::where('name', 'Jumbolon Coverd')
            ->update(['group_key' => 'foam']);

        // Mark other foam companies as regular foam
        $foamCompanies = [
            'ASHIR HARDWARE',
            'Master Foam',
            'Dura Foam',
            'Diamond Foam',
            'Style Foam',
            'Citi Foam',
        ];

        foreach ($foamCompanies as $name) {
            Company::where('name', $name)
                ->update(['group_key' => 'foam']);
        }

        $this->command->info('Assigned uncovered_foam group to uncovered companies.');
    }
}
