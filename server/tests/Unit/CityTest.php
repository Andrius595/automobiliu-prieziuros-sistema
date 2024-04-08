<?php

namespace Tests\Unit;

use App\Actions\City\CreateNewCity;
use App\Actions\City\ListCities;
use App\Actions\City\UpdateCity;
use Tests\TestCase;
use Tests\TestsHelper;

class CityTest extends TestCase
{
    use TestsHelper;

    public function test_if_city_is_created(): void
    {
        $newCity = new CreateNewCity();
        $city = $newCity->create([
            'name' => 'City',
        ]);

        $this->assertNotNull($city);
        $this->assertNotNull($city->id);
    }

    //update city
    public function test_if_city_is_updated(): void
    {
        $newCity = new CreateNewCity();
        $city = $newCity->create([
            'name' => 'City',
        ]);

        $updateCity = new UpdateCity();

        $updateCity->update($city, [
            'name' => 'City1',
        ]);

        $city->refresh();

        $this->assertEquals('City1', $city->name);
    }

    public function test_if_cities_are_listed(): void
    {
        $newCity = new CreateNewCity();
        $newCity->create([
            'name' => 'City1',
        ]);
        $newCity->create([
            'name' => 'City2',
        ]);

        $listCities = new ListCities();

        $cities = $listCities->list();

        $this->assertEquals(2, $cities->total());
    }
}
