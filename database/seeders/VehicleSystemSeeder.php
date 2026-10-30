<?php

namespace Database\Seeders;

use App\Models\FuelRecord;
use App\Models\MaintenanceCatalog;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceRecordItem;
use App\Models\NotificationPreference;
use App\Models\OdometerLog;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use App\Models\VehicleObligation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VehicleSystemSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cadastrar Marcas e Modelos Populares
        $brandsData = [
            'Chevrolet' => ['type' => 'car', 'models' => ['Onix', 'Onix Plus', 'Tracker', 'S10', 'Spin', 'Cruze', 'Celta', 'Corsa', 'Prisma', 'Montana']],
            'Volkswagen' => ['type' => 'car', 'models' => ['Gol', 'Polo', 'T-Cross', 'Nivus', 'Virtus', 'Saveiro', 'Fox', 'Voyage', 'Jetta', 'Amarok']],
            'Fiat' => ['type' => 'car', 'models' => ['Strada', 'Toro', 'Argo', 'Mobi', 'Pulse', 'Fastback', 'Uno', 'Palio', 'Siena', 'Fiorino']],
            'Toyota' => ['type' => 'car', 'models' => ['Corolla', 'Corolla Cross', 'Hilux', 'Yaris Hatch', 'Yaris Sedan', 'SW4', 'Etios Hatch', 'Etios Sedan']],
            'Hyundai' => ['type' => 'car', 'models' => ['HB20', 'HB20S', 'Creta', 'Tucson', 'i30', 'Santa Fe']],
            'Honda' => ['type' => 'both', 'models' => ['Civic', 'HR-V', 'City Sedan', 'City Hatchback', 'Fit', 'CR-V', 'CG 160 Titan', 'Biz 125', 'Bros 160', 'XRE 300', 'PCX 160', 'CB 300F Twister', 'CB 500X']],
            'Renault' => ['type' => 'car', 'models' => ['Kwid', 'Sandero', 'Duster', 'Logan', 'Captur', 'Oroch', 'Clio']],
            'Jeep' => ['type' => 'car', 'models' => ['Renegade', 'Compass', 'Commander']],
            'Nissan' => ['type' => 'car', 'models' => ['Kicks', 'Versa', 'Sentra', 'Frontier', 'March']],
            'Ford' => ['type' => 'car', 'models' => ['Ka', 'Ka Sedan', 'EcoSport', 'Ranger', 'Fiesta', 'Focus']],
            'Yamaha' => ['type' => 'motorcycle', 'models' => ['Fazer 250', 'Factor 150', 'Crosser 150', 'Lander 250', 'NMAX 160', 'MT-03', 'R3', 'FZ15']],
            'BMW' => ['type' => 'both', 'models' => ['320i', 'X1', 'X3', 'R 1250 GS', 'G 310 GS', 'F 850 GS']],
        ];

        foreach ($brandsData as $brandName => $data) {
            $brand = VehicleBrand::firstOrCreate(
                ['name' => $brandName],
                [
                    'slug' => Str::slug($brandName),
                    'type' => $data['type'],
                ]
            );

            foreach ($data['models'] as $modelName) {
                $isMoto = in_array($brandName, ['Yamaha']) || Str::contains($modelName, ['CG', 'Biz', 'Bros', 'XRE', 'PCX', 'Twister', 'CB', 'GS', 'Fazer', 'Factor', 'Lander', 'NMAX', 'MT', 'R3', 'FZ']);
                VehicleModel::firstOrCreate(
                    [
                        'brand_id' => $brand->id,
                        'name' => $modelName,
                    ],
                    [
                        'type' => $isMoto ? 'motorcycle' : 'car',
                        'category' => $isMoto ? 'Street/Scooter' : 'Automóvel',
                    ]
                );
            }
        }

        // 2. Cadastrar Catálogo de Manutenção Preventiva em Camadas
        $catalogsData = [
            // Motor e Lubrificação
            [
                'vehicle_type' => 'all',
                'system_category' => 'engine_oil',
                'item_name' => 'Troca de Óleo do Motor e Filtro de Óleo',
                'description' => 'Substituição completa do óleo lubrificante de especificação recomendada e elemento filtrante de óleo.',
                'default_interval_km' => 10000,
                'default_interval_months' => 12,
                'severe_interval_km' => 5000,
                'severe_interval_months' => 6,
                'is_critical' => true,
            ],
            [
                'vehicle_type' => 'all',
                'system_category' => 'engine_oil',
                'item_name' => 'Filtro de Ar do Motor',
                'description' => 'Substituição do elemento filtrante de admissão de ar para manter a queima correta e rendimento.',
                'default_interval_km' => 15000,
                'default_interval_months' => 12,
                'severe_interval_km' => 10000,
                'severe_interval_months' => 6,
                'is_critical' => false,
            ],
            [
                'vehicle_type' => 'car',
                'system_category' => 'engine_oil',
                'item_name' => 'Filtro de Combustível',
                'description' => 'Retenção de impurezas do combustível antes dos bicos injetores.',
                'default_interval_km' => 10000,
                'default_interval_months' => 12,
                'severe_interval_km' => 10000,
                'severe_interval_months' => 12,
                'is_critical' => false,
            ],
            [
                'vehicle_type' => 'all',
                'system_category' => 'engine_oil',
                'item_name' => 'Velas de Ignição',
                'description' => 'Inspeção e substituição das velas de ignição.',
                'default_interval_km' => 20000,
                'default_interval_months' => 24,
                'severe_interval_km' => 20000,
                'severe_interval_months' => 24,
                'is_critical' => false,
            ],
            [
                'vehicle_type' => 'car',
                'system_category' => 'engine_oil',
                'item_name' => 'Correia Dentada / Sincronizadora e Tensor',
                'description' => 'Substituição da correia sincronizadora do comando de válvulas e tensor.',
                'default_interval_km' => 50000,
                'default_interval_months' => 48,
                'severe_interval_km' => 40000,
                'severe_interval_months' => 36,
                'is_critical' => true,
            ],
            [
                'vehicle_type' => 'car',
                'system_category' => 'engine_oil',
                'item_name' => 'Correia de Acessórios / Alternador',
                'description' => 'Substituição da correia poli-V de acionamento do alternador e bomba d\'água.',
                'default_interval_km' => 40000,
                'default_interval_months' => 36,
                'severe_interval_km' => 30000,
                'severe_interval_months' => 24,
                'is_critical' => false,
            ],

            // Freios
            [
                'vehicle_type' => 'all',
                'system_category' => 'brakes',
                'item_name' => 'Pastilhas de Freio Dianteiras',
                'description' => 'Inspeção de espessura e substituição do conjunto de pastilhas de freio.',
                'default_interval_km' => 20000,
                'default_interval_months' => 18,
                'severe_interval_km' => 15000,
                'severe_interval_months' => 12,
                'is_critical' => true,
            ],
            [
                'vehicle_type' => 'all',
                'system_category' => 'brakes',
                'item_name' => 'Fluido de Freio (DOT 4 / DOT 5.1)',
                'description' => 'Substituição completa e sangria do fluido de freio para evitar contaminação por umidade e perda de frenagem.',
                'default_interval_km' => 30000,
                'default_interval_months' => 24,
                'severe_interval_km' => 20000,
                'severe_interval_months' => 12,
                'is_critical' => true,
            ],
            [
                'vehicle_type' => 'car',
                'system_category' => 'brakes',
                'item_name' => 'Discos de Freio Dianteiros',
                'description' => 'Inspeção de espessura mínima e empenamento dos discos.',
                'default_interval_km' => 40000,
                'default_interval_months' => 36,
                'severe_interval_km' => 30000,
                'severe_interval_months' => 24,
                'is_critical' => false,
            ],

            // Suspensão e Direção
            [
                'vehicle_type' => 'car',
                'system_category' => 'suspension_steering',
                'item_name' => 'Alinhamento de Direção e Balanceamento de Rodas',
                'description' => 'Geometria da suspensão, cambagem, cáster e balanceamento dos conjuntos roda/pneu.',
                'default_interval_km' => 10000,
                'default_interval_months' => 6,
                'severe_interval_km' => 5000,
                'severe_interval_months' => 6,
                'is_critical' => false,
            ],
            [
                'vehicle_type' => 'all',
                'system_category' => 'suspension_steering',
                'item_name' => 'Inspeção de Amortecedores, Pivôs e Buchas',
                'description' => 'Revisão geral do sistema de suspensão dianteira e traseira contra vazamentos e folgas.',
                'default_interval_km' => 30000,
                'default_interval_months' => 24,
                'severe_interval_km' => 20000,
                'severe_interval_months' => 12,
                'is_critical' => false,
            ],

            // Transmissão e Diferencial
            [
                'vehicle_type' => 'car',
                'system_category' => 'transmission',
                'item_name' => 'Óleo da Caixa de Transmissão (Câmbio)',
                'description' => 'Troca ou checagem do fluido da transmissão manual ou automática.',
                'default_interval_km' => 50000,
                'default_interval_months' => 48,
                'severe_interval_km' => 40000,
                'severe_interval_months' => 36,
                'is_critical' => false,
            ],
            [
                'vehicle_type' => 'car',
                'system_category' => 'transmission',
                'item_name' => 'Óleo do Diferencial / Trem de Força',
                'description' => 'Substituição do lubrificante de engrenagens hipóides do diferencial traseiro.',
                'default_interval_km' => 40000,
                'default_interval_months' => 36,
                'severe_interval_km' => 30000,
                'severe_interval_months' => 24,
                'is_critical' => false,
            ],

            // Arrefecimento e Climatização
            [
                'vehicle_type' => 'all',
                'system_category' => 'cooling_climate',
                'item_name' => 'Líquido de Arrefecimento (Fluido de Radiador)',
                'description' => 'Drenagem, limpeza e reabastecimento com aditivo inorgânico/orgânico e água desmineralizada.',
                'default_interval_km' => 40000,
                'default_interval_months' => 24,
                'severe_interval_km' => 30000,
                'severe_interval_months' => 18,
                'is_critical' => true,
            ],
            [
                'vehicle_type' => 'car',
                'system_category' => 'cooling_climate',
                'item_name' => 'Filtro de Cabine (Ar-Condicionado) & Oxi-Sanitização',
                'description' => 'Substituição do filtro antipólen e higienização dos dutos de ventilação.',
                'default_interval_km' => 10000,
                'default_interval_months' => 12,
                'severe_interval_km' => 10000,
                'severe_interval_months' => 6,
                'is_critical' => false,
            ],

            // Elétrica
            [
                'vehicle_type' => 'all',
                'system_category' => 'electrical',
                'item_name' => 'Diagnóstico e Teste de Bateria / Alternador',
                'description' => 'Teste de partida e capacidade de retenção de carga da bateria 12V.',
                'default_interval_km' => 20000,
                'default_interval_months' => 12,
                'severe_interval_km' => 15000,
                'severe_interval_months' => 12,
                'is_critical' => false,
            ],

            // Pneus
            [
                'vehicle_type' => 'car',
                'system_category' => 'tires_wheels',
                'item_name' => 'Rodízio de Pneus e Calibragem',
                'description' => 'Inversão dos pneus para equalizar desgaste entre eixos dianteiro e traseiro.',
                'default_interval_km' => 10000,
                'default_interval_months' => 6,
                'severe_interval_km' => 7000,
                'severe_interval_months' => 6,
                'is_critical' => false,
            ],

            // Específico para Motos
            [
                'vehicle_type' => 'motorcycle',
                'system_category' => 'motorcycle_drive',
                'item_name' => 'Kit Relação (Coroa, Corrente e Pinhão)',
                'description' => 'Substituição da transmissão secundária por corrente com retentores.',
                'default_interval_km' => 15000,
                'default_interval_months' => 18,
                'severe_interval_km' => 10000,
                'severe_interval_months' => 12,
                'is_critical' => true,
            ],
            [
                'vehicle_type' => 'motorcycle',
                'system_category' => 'motorcycle_drive',
                'item_name' => 'Regulagem de Folga de Válvulas e Vela de Moto',
                'description' => 'Verificação com cálibre de lâminas da folga de admissão e escape.',
                'default_interval_km' => 6000,
                'default_interval_months' => 12,
                'severe_interval_km' => 4000,
                'severe_interval_months' => 6,
                'is_critical' => true,
            ],
        ];

        foreach ($catalogsData as $cat) {
            MaintenanceCatalog::firstOrCreate(
                ['item_name' => $cat['item_name']],
                $cat
            );
        }

        // 3. Usuário Demo
        $user = User::firstOrCreate(
            ['email' => 'demo@veiculos.com'],
            [
                'name' => 'Eduardo Lopes',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        NotificationPreference::firstOrCreate(
            ['user_id' => $user->id],
            [
                'email_alerts_enabled' => true,
                'alert_km_before' => 2000,
                'alert_days_before' => [40, 20, 5],
                'notify_overdue' => true,
                'notify_monthly_odometer_reminder' => true,
            ]
        );

        // 4. Cadastrar Veículo 1: Carro
        $car = Vehicle::firstOrCreate(
            ['user_id' => $user->id, 'license_plate' => 'BRA2E19'],
            [
                'type' => 'car',
                'nickname' => 'Onix Plus Turbo',
                'brand' => 'Chevrolet',
                'model' => 'Onix Plus',
                'year_manufacture' => 2022,
                'year_model' => 2023,
                'fuel_type' => 'flex',
                'current_mileage' => 34500,
                'average_monthly_mileage' => 1200,
                'color' => 'Prata Metálico',
                'chassis' => '9BGKL69J0NG123456',
                'renavam' => '01234567890',
                'transmission_type' => 'automatic',
                'usage_condition' => 'normal',
                'status' => 'active',
                'notes' => 'Veículo de uso diário, revisões na autorizada até 30.000 km.',
            ]
        );

        // Log de odômetro
        OdometerLog::firstOrCreate(
            ['vehicle_id' => $car->id, 'mileage' => 34500],
            [
                'recorded_at' => Carbon::now()->subDays(5),
                'source' => 'manual',
                'notes' => 'Atualização pelo motorista',
            ]
        );

        // Gerar Planos de Manutenção Preventiva para o Carro
        $carCatalogs = MaintenanceCatalog::whereIn('vehicle_type', ['car', 'all'])->get();
        foreach ($carCatalogs as $item) {
            $lastKm = 30000;
            $lastDate = Carbon::now()->subMonths(4);

            $sched = $car->schedules()->firstOrCreate(
                ['catalog_id' => $item->id],
                [
                    'system_category' => $item->system_category,
                    'item_name' => $item->item_name,
                    'interval_km' => $item->default_interval_km,
                    'interval_months' => $item->default_interval_months,
                    'last_performed_at' => $lastDate,
                    'last_performed_mileage' => $lastKm,
                    'next_due_mileage' => $item->default_interval_km ? ($lastKm + $item->default_interval_km) : null,
                    'next_due_date' => $item->default_interval_months ? (clone $lastDate)->addMonths($item->default_interval_months) : null,
                    'is_active' => true,
                ]
            );
        }

        // Registrar Manutenção de Exemplo (Revisão de 30.000 km)
        $maint = MaintenanceRecord::firstOrCreate(
            ['vehicle_id' => $car->id, 'mileage' => 30000],
            [
                'user_id' => $user->id,
                'type' => 'oil_change',
                'system_category' => 'engine_oil',
                'title' => 'Revisão dos 30.000 km + Troca de Óleo Dexos 1 Gen 3',
                'description' => 'Troca completa de óleo sintético 5W30, filtro de óleo, filtro de combustível e filtro de ar.',
                'performed_at' => Carbon::now()->subMonths(4),
                'parts_cost' => 320.00,
                'labor_cost' => 150.00,
                'total_cost' => 470.00,
                'workshop_name' => 'Oficina Mecânica Precision Auto',
                'workshop_phone' => '(11) 98765-4321',
                'technician_name' => 'Carlos Mecânico',
                'oil_viscosity' => '5W30 100% Sintético Dexos 1',
                'oil_brand' => 'ACDelco',
                'oil_liters' => 3.5,
                'filters_replaced' => ['oil', 'air', 'fuel'],
                'warranty_days' => 90,
                'warranty_km' => 5000,
            ]
        );

        MaintenanceRecordItem::firstOrCreate(
            ['maintenance_record_id' => $maint->id, 'item_name' => 'Óleo ACDelco 5W30 Dexos1 Gen3'],
            ['quantity' => 3.5, 'unit_price' => 55.00, 'total_price' => 192.50]
        );
        MaintenanceRecordItem::firstOrCreate(
            ['maintenance_record_id' => $maint->id, 'item_name' => 'Filtro de Óleo Fram PH5949'],
            ['quantity' => 1, 'unit_price' => 45.00, 'total_price' => 45.00]
        );
        MaintenanceRecordItem::firstOrCreate(
            ['maintenance_record_id' => $maint->id, 'item_name' => 'Filtro de Combustível Tecfil'],
            ['quantity' => 1, 'unit_price' => 42.50, 'total_price' => 42.50]
        );

        // Abastecimento de Exemplo
        FuelRecord::firstOrCreate(
            ['vehicle_id' => $car->id, 'mileage' => 34500],
            [
                'user_id' => $user->id,
                'date' => Carbon::now()->subDays(5),
                'fuel_type' => 'gasoline',
                'liters' => 40.5,
                'price_per_liter' => 5.89,
                'total_cost' => 238.54,
                'is_full_tank' => true,
                'km_per_liter' => 12.8,
                'gas_station' => 'Posto Ipiranga Rodoanel',
                'notes' => 'Tanque cheio após viagem',
            ]
        );

        // Obrigações (IPVA e Licenciamento)
        VehicleObligation::firstOrCreate(
            ['vehicle_id' => $car->id, 'title' => 'IPVA 2026 - Cota Única com Desconto'],
            [
                'type' => 'ipva',
                'reference_year' => 2026,
                'due_date' => Carbon::create(2026, 1, 15),
                'amount' => 2450.00,
                'status' => 'paid',
                'paid_at' => Carbon::create(2026, 1, 12),
                'amount_paid' => 2376.50,
                'notes' => 'Pago com 3% de desconto via Pix.',
            ]
        );

        VehicleObligation::firstOrCreate(
            ['vehicle_id' => $car->id, 'title' => 'Licenciamento Anual 2026 - Final Placa 9'],
            [
                'type' => 'licensing',
                'reference_year' => 2026,
                'due_date' => Carbon::create(2026, 11, 30),
                'amount' => 160.00,
                'status' => 'pending',
                'notes' => 'Vencimento no final do ano.',
            ]
        );

        // 5. Cadastrar Veículo 2: Moto
        $moto = Vehicle::firstOrCreate(
            ['user_id' => $user->id, 'license_plate' => 'RIO9A82'],
            [
                'type' => 'motorcycle',
                'nickname' => 'Titan 160 Diária',
                'brand' => 'Honda',
                'model' => 'CG 160 Titan',
                'year_manufacture' => 2021,
                'year_model' => 2021,
                'fuel_type' => 'flex',
                'current_mileage' => 18200,
                'average_monthly_mileage' => 800,
                'color' => 'Vermelho Pérola',
                'secondary_drive_type' => 'chain',
                'usage_condition' => 'normal',
                'status' => 'active',
                'notes' => 'Moto para trajetos rápidos e trabalho.',
            ]
        );

        // Planos preventivos para a Moto
        $motoCatalogs = MaintenanceCatalog::whereIn('vehicle_type', ['motorcycle', 'all'])->get();
        foreach ($motoCatalogs as $item) {
            $lastKm = 15000;
            $lastDate = Carbon::now()->subMonths(3);

            $moto->schedules()->firstOrCreate(
                ['catalog_id' => $item->id],
                [
                    'system_category' => $item->system_category,
                    'item_name' => $item->item_name,
                    'interval_km' => $item->default_interval_km,
                    'interval_months' => $item->default_interval_months,
                    'last_performed_at' => $lastDate,
                    'last_performed_mileage' => $lastKm,
                    'next_due_mileage' => $item->default_interval_km ? ($lastKm + $item->default_interval_km) : null,
                    'next_due_date' => $item->default_interval_months ? (clone $lastDate)->addMonths($item->default_interval_months) : null,
                    'is_active' => true,
                ]
            );
        }
    }
}
