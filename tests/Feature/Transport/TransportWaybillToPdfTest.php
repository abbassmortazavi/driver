<?php

namespace Tests\Feature\Transport;

use App\Models\Customer;
use App\Models\Driver;
use App\Models\PublicUser;
use App\Models\Shipment;
use App\Models\Transport;
use App\Models\Waybill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class TransportWaybillToPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_generate_pdf()
    {
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id]);
        $transport = Transport::factory()->create(['driver_id' => $driver->id]);
        $customer = Customer::factory()->create(['user_id' => $user->id]);
        $shipment = Shipment::factory()->create(['customer_id' => $customer->id]);
        Waybill::factory()->count(2)->create([
            'transport_id' => $transport->id,
            'driver_id' => $driver->id,
            'customer_id' => $customer->id,
            'shipment_id' => $shipment->id,
        ]);
        Passport::actingAs($user);
        $response = $this
            ->get("api/v1/transports/{$transport->getHashedId()}/waybill-pdf");

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_pdf_contains_correct_waybill_data()
    {
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id]);
        $transport = Transport::factory()->create(['driver_id' => $driver->id]);
        $customer = Customer::factory()->create(['user_id' => $user->id]);
        $shipment = Shipment::factory()->create(['customer_id' => $customer->id]);
        $waybill = Waybill::factory()->create([
            'transport_id' => $transport->id,
            'shipment_id' => $shipment->id,
            'driver_id' => $driver->id,
            'customer_id' => $customer->id,
            'waybill_number' => 'WB-TEST-123',
            'price_value' => 1500000,
            'price_currency' => 'IRR',
        ]);
        Passport::actingAs($user);
        $response = $this
            ->get("api/v1/transports/{$transport->getHashedId()}/waybill-pdf");
        $view = $this->view('waybills.pdf', [
            'transport' => $transport,
            'waybills' => collect([$waybill]),
        ]);

        $view->assertSee('WB-TEST-123');
        $view->assertSee('1,500,000');

    }
}
