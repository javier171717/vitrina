<?php

namespace App\Controllers\Panel;

use App\Models\AdvisorModel;
use App\Models\CategoryModel;
use App\Models\ProductModel;

class Dashboard extends BasePanelController
{
    public function index(): string
    {
        $businessId = (int) $this->business['id'];

        return $this->panelView('panel/dashboard', [
            'title'      => 'Resumen',
            'section'    => 'dashboard',
            'advisors'   => model(AdvisorModel::class)->where('business_id', $businessId)->countAllResults(),
            'categories' => model(CategoryModel::class)->where('business_id', $businessId)->countAllResults(),
            'products'   => model(ProductModel::class)->countForBusiness($businessId),
            'advisorList'=> model(AdvisorModel::class)->forBusiness($businessId),
        ]);
    }
}
