<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Category;
use App\Services;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CategoryServicesAjaxTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Category $categoryA;
    protected Category $categoryB;
    protected Services $serviceA1;
    protected Services $serviceA2;
    protected Services $serviceB1;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::first() ?? User::create([
            'name'       => 'Category Service Tester',
            'email'      => 'cat_test_' . uniqid() . '@example.com',
            'password'   => bcrypt('password'),
            'company_id' => 1,
            'is_active'  => 1,
        ]);

        $this->categoryA = Category::create([
            'name'       => 'Hair Care ' . uniqid(),
            'company_id' => $this->user->company_id,
            'is_active'  => 1,
        ]);

        $this->categoryB = Category::create([
            'name'       => 'Skin Care ' . uniqid(),
            'company_id' => $this->user->company_id,
            'is_active'  => 1,
        ]);

        $this->serviceA1 = Services::create([
            'name'        => 'Haircut ' . uniqid(),
            'category_id' => $this->categoryA->id,
            'company_id'  => $this->user->company_id,
            'is_active'   => 1,
            'is_deleted'  => 0,
            'price'       => 25.00,
        ]);

        $this->serviceA2 = Services::create([
            'name'        => 'Hair Color ' . uniqid(),
            'category_id' => $this->categoryA->id,
            'company_id'  => $this->user->company_id,
            'is_active'   => 1,
            'is_deleted'  => 0,
            'price'       => 50.00,
        ]);

        $this->serviceB1 = Services::create([
            'name'        => 'Facial ' . uniqid(),
            'category_id' => $this->categoryB->id,
            'company_id'  => $this->user->company_id,
            'is_active'   => 1,
            'is_deleted'  => 0,
            'price'       => 40.00,
        ]);
    }

    public function test_get_services_by_category_returns_filtered_services()
    {
        $response = $this->actingAs($this->user)->get(route('services.by_category', [
            'category_id' => $this->categoryA->id,
        ]));

        $response->assertStatus(200);
        $json = $response->json();

        $ids = array_column($json, 'id');
        $this->assertContains($this->serviceA1->id, $ids);
        $this->assertContains($this->serviceA2->id, $ids);
        $this->assertNotContains($this->serviceB1->id, $ids);
    }

    public function test_get_services_returns_all_services_when_category_is_no_or_empty()
    {
        $response = $this->actingAs($this->user)->get(route('services.by_category', [
            'category_id' => 'no',
        ]));

        $response->assertStatus(200);
        $json = $response->json();

        $ids = array_column($json, 'id');
        $this->assertContains($this->serviceA1->id, $ids);
        $this->assertContains($this->serviceA2->id, $ids);
        $this->assertContains($this->serviceB1->id, $ids);
    }

    public function test_reports_index_view_loads_and_has_category_change_script()
    {
        $response = $this->actingAs($this->user)->get(route('reports.index'));
        $response->assertStatus(200);

        $response->assertSee('id="category_id"', false);
        $response->assertSee('id="service_id"', false);
        $response->assertSee('get-services-by-category');
    }
}
