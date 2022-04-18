<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserStatus;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;


class UserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $user;
    protected $role;

    public function setUp(): void
    {
        parent::setUp();

            $this->user = User::factory()->create();
            $this->role = Role::create(['name'=>'admin', 'guard_name'=>'web']);
            $this->user->assignRole('admin');
    }

    /**
     * Check all user page
     *
     * @return void
     */
    public function test_all_user_page()
    {
        $status = UserStatus::factory()->create();
        $response = $this->actingAs($this->user, 'web')->withSession(['banned' => false])->get('/');
        $response->assertStatus(200);
    }

    /**
     * Check create user page
     *
     * @return void
     */
    public function test_create_user_page()
    {
        $response = $this->actingAs($this->user, 'web')->withSession(['banned' => false])->get('/users/create');
        $response->assertStatus(200);
    }

    /**
     * Check store new user
     *
     * @return void
     */
    public function test_store_user()
    {
        Storage::fake('avatars');
        $file = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->actingAs($this->user, 'web')
            ->withSession(['banned' => false])
            ->post('/users', [
                'name' => $this->faker->name(),
                'email' => $this->faker->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'remember_token' => Str::random(10),
                'vk'=> Str::random(10),
                'instagram'=> Str::random(10),
                'telegram'=> Str::random(10),
                'job'=>Str::random(10),
                'phone'=>Str::random(10),
                'address'=>Str::random(10),
                'status_id'=>1,
                'avatar' => $file
        ]);

        $user_new = User::orderBy('id','desc')->first();
        Storage::delete($user_new->avatar);
        $response->assertRedirect('/users');
    }

    /**
     * Check one user page
     *
     * @return void
     */
    public function test_user_page()
    {
        $response = $this->actingAs($this->user, 'web')->withSession(['banned' => false])->get('/users/'.$this->user->id);
        $response->assertStatus(200);
    }

    /**
     * Check edit user page
     *
     * @return void
     */
    public function test_edit_user_page()
    {
        $response = $this->actingAs($this->user, 'web')->withSession(['banned' => false])->get('/users/'.$this->user->id.'/edit');
        $response->assertStatus(200);
    }

    /**
     * Check update user
     *
     * @return void
     */
    public function test_update_user()
    {
        $response = $this->actingAs($this->user, 'web')
            ->withSession(['banned' => false])
            ->patch('/users/'.$this->user->id, [
                'name' => $this->faker->name(),
                'job'=>Str::random(10),
                'phone'=>Str::random(10),
                'address'=>Str::random(10),
            ]);
        $response->assertRedirect('/users');
    }

    /**
     * Check edit user credentials page
     *
     * @return void
     */
    public function test_edit_user_credentials_page()
    {
        $response = $this->actingAs($this->user, 'web')->withSession(['banned' => false])->get('/users/edit_credentials/'.$this->user->id);
        $response->assertStatus(200);
    }

    /**
     * Check update user credentials
     *
     * @return void
     */
    public function test_update_credentials_user()
    {
        $response = $this->actingAs($this->user, 'web')
            ->withSession(['banned' => false])
            ->patch('/users/update_credentials/'.$this->user->id, [
                'email' => $this->faker->unique()->safeEmail(),
                'password' => '12345678',
                'password_confirmation' => '12345678',

            ]);
        $response->assertRedirect('/users');
    }

    /**
     * Check edit user status page
     *
     * @return void
     */
    public function test_edit_user_status_page()
    {
        $response = $this->actingAs($this->user, 'web')->withSession(['banned' => false])->get('/users/edit_status/'.$this->user->id);
        $response->assertStatus(200);
    }

    /**
     * Check update user status
     *
     * @return void
     */
    public function test_update_status_user()
    {
        $response = $this->actingAs($this->user, 'web')
            ->withSession(['banned' => false])
            ->patch('/users/update_status/'.$this->user->id, [
                'status_id' => 1,
            ]);
        $response->assertRedirect('/users');
    }

    /**
     * Check edit user avatar page
     *
     * @return void
     */
    public function test_edit_user_avatar_page()
    {
        $response = $this->actingAs($this->user, 'web')->withSession(['banned' => false])->get('/users/edit_avatar/'.$this->user->id);
        $response->assertStatus(200);
    }

    /**
     * Check update user avatar
     *
     * @return void
     */
    public function test_update_user_avatar()
    {
        Storage::fake('avatars');

        $file = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->actingAs($this->user, 'web')
            ->withSession(['banned' => false])
            ->patch('/users/update_avatar/'.$this->user->id, [
                'avatar' => $file
            ]);

        Storage::delete($this->user->avatar);
        $response->assertRedirect('/users');
    }

    /**
     * Check edit user avatar page
     *
     * @return void
     */
    public function test_delete_user()
    {
        $response = $this->actingAs($this->user, 'web')->withSession(['banned' => false])->delete('/users/'.$this->user->id);
        $response->assertRedirect('/users');
    }
}
