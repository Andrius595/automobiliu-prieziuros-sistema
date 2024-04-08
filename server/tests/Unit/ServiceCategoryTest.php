<?php

namespace Tests\Unit;

use App\Actions\ServiceCategory\CreateNewServiceCategory;
use App\Actions\ServiceCategory\ListServiceCategories;
use App\Actions\ServiceCategory\UpdateServiceCategory;
use Tests\TestCase;
use Tests\TestsHelper;

class ServiceCategoryTest extends TestCase
{
    use TestsHelper;

    public function test_if_service_category_is_created(): void
    {
        $newServiceCategory = new CreateNewServiceCategory();
        $serviceCategory = $newServiceCategory->create([
            'name' => 'Service Category',
        ]);

        $this->assertNotNull($serviceCategory);
        $this->assertNotNull($serviceCategory->id);
    }

    //update service category
    public function test_if_service_category_is_updated(): void
    {
        $newServiceCategory = new CreateNewServiceCategory();
        $serviceCategory = $newServiceCategory->create([
            'name' => 'Service Category',
        ]);

        $updateServiceCategory = new UpdateServiceCategory();

        $updateServiceCategory->update($serviceCategory, [
            'name' => 'Service Category1',
        ]);

        $serviceCategory->refresh();

        $this->assertEquals('Service Category1', $serviceCategory->name);
    }

    public function test_if_service_categories_are_listed(): void
    {
        $newServiceCategory = new CreateNewServiceCategory();
        $newServiceCategory->create([
            'name' => 'Service Category1',
        ]);
        $newServiceCategory->create([
            'name' => 'Service Category2',
        ]);

        $listServiceCategories = new ListServiceCategories();

        $serviceCategories = $listServiceCategories->list();

        $this->assertEquals(2, $serviceCategories->total());
    }
}
