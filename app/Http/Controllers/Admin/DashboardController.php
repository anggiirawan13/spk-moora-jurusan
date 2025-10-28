<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alternative;
use App\Models\Criteria;
use App\Models\Car;
use App\Models\CarBrand;
use App\Models\CarType;
use App\Models\FuelType;
use App\Models\TransmissionType;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $car = Car::count();
        $users = User::count();
        $carBrands = CarBrand::count();
        $carTypes = CarType::count();
        $fuels = FuelType::count();
        $transmissions = TransmissionType::count();
        $criteria = Criteria::count();
        $alternative = Alternative::count();

        $data = (object) [
            'transmissions' => $transmissions,
            'fuels' => $fuels,
            'carTypes' => $carTypes,
            'carBrands' => $carBrands,
            'student' => $car,
            'users' => $users,
            'criteria' => $criteria,
            'alternative' => $alternative,
        ];

        return view('admin.dashboard', compact('data'));
    }
}
