<?php

namespace Tests\Unit;

use App\Actions\User\CreateNewUser;
use App\Actions\User\ListUsers;
use App\Config\PermissionsConfig;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tests\TestsHelper;

class UserTest extends TestCase
{
    use TestsHelper;

    public function test_if_users_are_listed_searched_and_sorted_by_role(): void
    {
        $user1 = User::factory()->create();
        $user1->assignRole(PermissionsConfig::SYSTEM_ADMIN_ROLE);
        $user2 = User::factory()->create();
        $user2->assignRole(PermissionsConfig::SYSTEM_ADMIN_ROLE);
        $user3 = User::factory()->create();
        $user3->assignRole(PermissionsConfig::CLIENT_ROLE);


        $listUsers = new ListUsers();

        $users = $listUsers->list([
            'roles' => PermissionsConfig::SYSTEM_ADMIN_ROLE,
        ], 10, [
            'sortBy' => 'roles',
        ]);

        $this->assertEquals(2, $users->total());
    }

    public function test_if_user_is_created(): void
    {
        $newUser = new CreateNewUser();
        $user = $newUser->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => PermissionsConfig::CLIENT_ROLE,
        ]);

        $this->assertNotNull($user);
        $this->assertNotNull($user->id);
        $this->assertTrue($user->hasRole(PermissionsConfig::CLIENT_ROLE));
        $this->assertTrue(Hash::check('password', $user->password));
    }
}
