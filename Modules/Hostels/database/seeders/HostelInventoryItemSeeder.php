<?php

namespace Modules\Hostels\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelInventoryItem;

class HostelInventoryItemSeeder extends Seeder
{
    public function run(): void
    {
        $hostels = Hostel::all();

        if ($hostels->isEmpty()) {
            $this->call(HostelSeeder::class);
            $hostels = Hostel::all();
        }

        $inventoryItems = [
            // Bedding & Linens
            ['Bed Sheets (Single)', 'Bedding & Linens', 'Cotton bed sheets for single beds', 'BED-SHT-SGL', 45.00, 200, 50, 300, 'pieces', 'active'],
            ['Bed Sheets (Double)', 'Bedding & Linens', 'Cotton bed sheets for double beds', 'BED-SHT-DBL', 65.00, 150, 40, 250, 'pieces', 'active'],
            ['Pillow Cases', 'Bedding & Linens', 'Standard pillow cases', 'PILLOW-CASE', 15.00, 300, 100, 500, 'pieces', 'active'],
            ['Blankets', 'Bedding & Linens', 'Warm blankets for guests', 'BLANKET-STD', 120.00, 100, 30, 200, 'pieces', 'active'],
            ['Duvet Covers', 'Bedding & Linens', 'Protective duvet covers', 'DUVET-COV', 85.00, 120, 40, 200, 'pieces', 'active'],
            ['Mattress Protectors', 'Bedding & Linens', 'Waterproof mattress protectors', 'MAT-PROT', 95.00, 80, 25, 150, 'pieces', 'active'],

            // Bathroom Supplies
            ['Bath Towels', 'Bathroom Supplies', 'Standard bath towels', 'BATH-TOWEL', 35.00, 250, 80, 400, 'pieces', 'active'],
            ['Hand Towels', 'Bathroom Supplies', 'Small hand towels', 'HAND-TOWEL', 18.00, 300, 100, 500, 'pieces', 'active'],
            ['Face Towels', 'Bathroom Supplies', 'Face washing towels', 'FACE-TOWEL', 12.00, 400, 120, 600, 'pieces', 'active'],
            ['Bath Mats', 'Bathroom Supplies', 'Non-slip bath mats', 'BATH-MAT', 45.00, 60, 20, 100, 'pieces', 'active'],
            ['Toilet Paper (Rolls)', 'Bathroom Supplies', '2-ply toilet paper', 'TP-2PLY', 8.50, 1000, 300, 2000, 'rolls', 'active'],
            ['Soap Bars', 'Bathroom Supplies', 'Guest soap bars', 'SOAP-BAR', 5.00, 500, 150, 800, 'pieces', 'active'],
            ['Shampoo Bottles', 'Bathroom Supplies', '250ml shampoo bottles', 'SHAMPOO-250', 15.00, 300, 100, 500, 'bottles', 'active'],
            ['Conditioner Bottles', 'Bathroom Supplies', '250ml conditioner bottles', 'COND-250', 15.00, 250, 80, 400, 'bottles', 'active'],
            ['Body Wash', 'Bathroom Supplies', '500ml body wash bottles', 'BODY-WASH-500', 25.00, 200, 60, 350, 'bottles', 'active'],

            // Cleaning Supplies
            ['All-Purpose Cleaner', 'Cleaning Supplies', 'Multi-surface cleaner', 'CLEAN-AP', 45.00, 50, 15, 100, 'bottles', 'active'],
            ['Glass Cleaner', 'Cleaning Supplies', 'Streak-free glass cleaner', 'CLEAN-GLASS', 38.00, 40, 12, 80, 'bottles', 'active'],
            ['Bathroom Cleaner', 'Cleaning Supplies', 'Tile and bathroom cleaner', 'CLEAN-BATH', 52.00, 45, 15, 90, 'bottles', 'active'],
            ['Floor Cleaner', 'Cleaning Supplies', 'Floor cleaning solution', 'CLEAN-FLOOR', 65.00, 30, 10, 60, 'bottles', 'active'],
            ['Disinfectant Spray', 'Cleaning Supplies', 'Surface disinfectant', 'DISINFECT', 42.00, 60, 20, 120, 'bottles', 'active'],
            ['Bleach', 'Cleaning Supplies', 'Chlorine bleach', 'BLEACH-1L', 28.00, 25, 8, 50, 'bottles', 'active'],
            ['Trash Bags (Large)', 'Cleaning Supplies', '30-gallon trash bags', 'BAG-TRASH-30', 85.00, 20, 5, 40, 'rolls', 'active'],
            ['Trash Bags (Small)', 'Cleaning Supplies', '13-gallon trash bags', 'BAG-TRASH-13', 45.00, 40, 12, 80, 'rolls', 'active'],
            ['Dish Soap', 'Cleaning Supplies', 'Liquid dish soap', 'SOAP-DISH', 22.00, 35, 10, 70, 'bottles', 'active'],
            ['Sponges', 'Cleaning Supplies', 'Cleaning sponges', 'SPONGE', 3.50, 200, 50, 300, 'pieces', 'active'],
            ['Microfiber Cloths', 'Cleaning Supplies', 'Reusable cleaning cloths', 'CLOTH-MF', 8.00, 150, 40, 250, 'pieces', 'active'],
            ['Mop Heads', 'Cleaning Supplies', 'Replacement mop heads', 'MOP-HEAD', 25.00, 15, 5, 30, 'pieces', 'active'],
            ['Broom Heads', 'Cleaning Supplies', 'Replacement broom heads', 'BROOM-HEAD', 20.00, 12, 4, 25, 'pieces', 'active'],

            // Kitchen Supplies
            ['Coffee Mugs', 'Kitchen Supplies', 'Ceramic coffee mugs', 'MUG-CERAMIC', 15.00, 100, 30, 200, 'pieces', 'active'],
            ['Tea Cups', 'Kitchen Supplies', 'Ceramic tea cups', 'CUP-TEA', 12.00, 80, 25, 150, 'pieces', 'active'],
            ['Dinner Plates', 'Kitchen Supplies', 'Ceramic dinner plates', 'PLATE-DINNER', 18.00, 120, 35, 200, 'pieces', 'active'],
            ['Side Plates', 'Kitchen Supplies', 'Ceramic side plates', 'PLATE-SIDE', 10.00, 150, 40, 250, 'pieces', 'active'],
            ['Bowls', 'Kitchen Supplies', 'Ceramic soup bowls', 'BOWL-SOUP', 14.00, 100, 30, 180, 'pieces', 'active'],
            ['Cutlery Sets', 'Kitchen Supplies', 'Stainless steel cutlery sets', 'CUTLERY-SET', 45.00, 50, 15, 100, 'sets', 'active'],
            ['Coffee Filters', 'Kitchen Supplies', '#4 coffee filters', 'COFFEE-FILTER', 12.00, 200, 60, 300, 'pieces', 'active'],
            ['Sugar Packets', 'Kitchen Supplies', 'Individual sugar packets', 'SUGAR-PKT', 0.50, 2000, 500, 3000, 'packets', 'active'],
            ['Tea Bags', 'Kitchen Supplies', 'Black tea bags', 'TEA-BAG', 0.30, 1500, 400, 2500, 'bags', 'active'],
            ['Coffee Packets', 'Kitchen Supplies', 'Instant coffee packets', 'COFFEE-PKT', 0.80, 1200, 300, 2000, 'packets', 'active'],

            // Maintenance & Repair
            ['Light Bulbs (LED)', 'Maintenance & Repair', '9W LED bulbs', 'BULB-LED-9W', 12.00, 100, 30, 200, 'pieces', 'active'],
            ['Batteries (AA)', 'Maintenance & Repair', 'AA alkaline batteries', 'BATT-AA', 3.50, 200, 50, 300, 'pieces', 'active'],
            ['Batteries (AAA)', 'Maintenance & Repair', 'AAA alkaline batteries', 'BATT-AAA', 3.00, 180, 45, 250, 'pieces', 'active'],
            ['Fuses', 'Maintenance & Repair', 'Standard electrical fuses', 'FUSE-STD', 8.00, 50, 15, 100, 'pieces', 'active'],
            ['Door Handles', 'Maintenance & Repair', 'Standard door handles', 'DOOR-HANDLE', 85.00, 10, 3, 20, 'pieces', 'active'],
            ['Locks', 'Maintenance & Repair', 'Door locks and mechanisms', 'LOCK-DOOR', 120.00, 8, 2, 15, 'pieces', 'active'],
            ['Paint (White)', 'Maintenance & Repair', 'Interior white paint', 'PAINT-WHITE', 180.00, 5, 2, 10, 'gallons', 'active'],
            ['Paint Brushes', 'Maintenance & Repair', '2-inch paint brushes', 'BRUSH-2IN', 15.00, 20, 6, 40, 'pieces', 'active'],
            ['Screws & Nails', 'Maintenance & Repair', 'Assorted screws and nails', 'HARDWARE-ASS', 25.00, 15, 5, 30, 'kits', 'active'],

            // Office Supplies
            ['Pens', 'Office Supplies', 'Black ballpoint pens', 'PEN-BLACK', 2.50, 200, 50, 300, 'pieces', 'active'],
            ['Notepads', 'Office Supplies', 'Standard notepads', 'PAD-NOTE', 8.00, 50, 15, 100, 'pads', 'active'],
            ['Staples', 'Office Supplies', 'Standard staples', 'STAPLE-BOX', 5.00, 30, 10, 60, 'boxes', 'active'],
            ['Paper Clips', 'Office Supplies', 'Assorted paper clips', 'CLIP-PAPER', 3.00, 40, 12, 80, 'boxes', 'active'],
            ['Envelopes', 'Office Supplies', '#10 business envelopes', 'ENV-10', 12.00, 100, 30, 200, 'pieces', 'active'],

            // Guest Amenities
            ['Water Bottles', 'Guest Amenities', '500ml bottled water', 'WATER-500ML', 8.00, 300, 100, 500, 'bottles', 'active'],
            ['Snack Packs', 'Guest Amenities', 'Assorted snack packs', 'SNACK-PACK', 15.00, 150, 40, 250, 'packs', 'active'],
            ['Welcome Packets', 'Guest Amenities', 'Guest information packets', 'WELCOME-PKT', 5.00, 100, 25, 200, 'packets', 'active'],
            ['Slippers', 'Guest Amenities', 'Disposable guest slippers', 'SLIPPER-DISP', 8.00, 200, 50, 300, 'pairs', 'active'],
            ['Shower Caps', 'Guest Amenities', 'Disposable shower caps', 'SHOWER-CAP', 2.50, 300, 80, 400, 'pieces', 'active'],
        ];

        foreach ($hostels as $hostel) {
            foreach ($inventoryItems as $itemData) {
                [$name, $category, $description, $sku, $unitCost, $currentStock, $minStock, $maxStock, $uom, $status] = $itemData;

                // Add some variation to stock levels between hostels
                $stockVariation = rand(-20, 20);
                $adjustedStock = max(0, $currentStock + $stockVariation);
                $adjustedMinStock = max(5, $minStock + rand(-5, 5));
                $adjustedMaxStock = max($adjustedMinStock + 10, $maxStock + rand(-10, 10));

                HostelInventoryItem::create([
                    'hostel_id' => $hostel->id,
                    'name' => $name,
                    'category' => $category,
                    'description' => $description,
                    'sku' => $sku.'-'.$hostel->code,
                    'unit_cost' => $unitCost * (1 + rand(-0.1, 0.1)), // ±10% price variation
                    'current_stock' => $adjustedStock,
                    'min_stock_level' => $adjustedMinStock,
                    'max_stock_level' => $adjustedMaxStock,
                    'uom' => $uom,
                    'status' => $status,
                ]);
            }
        }
    }
}
