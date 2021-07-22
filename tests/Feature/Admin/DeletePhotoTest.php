<?php

namespace Tests\Feature\Admin;


use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\State;
use App\Models\Photo;
use App\Models\User\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;
use Spatie\Permission\Models\Role;
use Tests\Feature\HasPhotoUploads;
use Tests\TestCase;

class DeletePhotoTest extends TestCase
{
    use HasPhotoUploads;

    /** @var User */
    protected $admin;
    /** @var User */
    protected $user;
    /** @var Photo */
    protected $photo;
    /** @var array */
    private $imageAndAttributes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setImagePath();

        /** @var User $admin */
        $this->admin = User::factory()->create(['verification_required' => false]);

        $this->admin->assignRole(Role::create(['name' => 'admin']));

        $this->user = User::factory()->create(['verification_required' => true]);

        // User uploads an image -------------------
        $this->actingAs($this->user);

        $this->imageAndAttributes = $this->getImageAndAttributes();

        $this->post('/submit', ['file' => $this->imageAndAttributes['file']]);

        $this->photo = $this->user->fresh()->photos->last();
    }

    protected function tearDown(): void
    {
        if (File::exists($this->imageAndAttributes['filepath'])) {
            File::delete($this->imageAndAttributes['filepath']);
        }

        parent::tearDown();
    }

    public function test_an_admin_can_delete_photos_uploaded_by_users()
    {
        // User tags the image
        $this->actingAs($this->user);

        $this->post('/add-tags', [
            'photo_id' => $this->photo->id,
            'presence' => true,
            'tags' => [
                'smoking' => [
                    'butts' => 3
                ]
            ]
        ]);

        // We make sure the photo exists
        $this->assertFileExists($this->imageAndAttributes['filepath']);
        $this->assertEquals(1, $this->user->has_uploaded);
        $this->assertEquals(4, $this->user->xp);
        $this->assertEquals(1, $this->user->total_images);
        $this->assertInstanceOf(Photo::class, $this->photo);

        // Admin deletes the photo -------------------
        $this->actingAs($this->admin);

        $this->post('/admin/destroy', ['photoId' => $this->photo->id]);

        $this->user->refresh();

        // And it's gone
        $this->assertEquals(1, $this->user->has_uploaded); // TODO shouldn't it decrement?
        $this->assertEquals(0, $this->user->xp);
        $this->assertEquals(0, $this->user->total_images);
        $this->assertFileDoesNotExist($this->imageAndAttributes['filepath']);
        $this->assertCount(0, $this->user->photos);
        $this->assertDatabaseMissing('photos', ['id' => $this->photo->id]);
    }

    public function test_leaderboards_are_updated_when_an_admin_deletes_a_photo_from_a_user_with_public_name()
    {
        $country = Country::find($this->photo->country_id)->country;
        $state = State::find($this->photo->state_id)->state;
        $city = City::find($this->photo->city_id)->city;

        Redis::del("{$country}:Leaderboard");
        Redis::del("{$country}:{$state}:Leaderboard");
        Redis::del("{$country}:{$state}:{$city}:Leaderboard");

        $this->user->update([
            'show_name' => true,
            'show_username' => true
        ]);

        // User tags the image
        $this->actingAs($this->user);

        $this->post('/add-tags', [
            'photo_id' => $this->photo->id,
            'presence' => true,
            'tags' => [
                'smoking' => [
                    'butts' => 3
                ]
            ]
        ]);

        $this->assertEquals(4, Redis::zscore("{$country}:Leaderboard", $this->user->id));
        $this->assertEquals(4, Redis::zscore("{$country}:{$state}:Leaderboard", $this->user->id));
        $this->assertEquals(4, Redis::zscore("{$country}:{$state}:{$city}:Leaderboard", $this->user->id));

        // Admin deletes the photo -------------------
        $this->actingAs($this->admin);

        $this->post('/admin/destroy', ['photoId' => $this->photo->id]);

        // Assert leaderboards are updated ------------
        $this->assertEquals(0, Redis::zscore("{$country}:Leaderboard", $this->user->id));
        $this->assertEquals(0, Redis::zscore("{$country}:{$state}:Leaderboard", $this->user->id));
        $this->assertEquals(0, Redis::zscore("{$country}:{$state}:{$city}:Leaderboard", $this->user->id));
    }

    public function test_leaderboards_are_not_updated_when_an_admin_deletes_a_photo_from_a_user_with_private_name()
    {
        $country = Country::find($this->photo->country_id)->country;
        $state = State::find($this->photo->state_id)->state;
        $city = City::find($this->photo->city_id)->city;

        Redis::del("{$country}:Leaderboard");
        Redis::del("{$country}:{$state}:Leaderboard");
        Redis::del("{$country}:{$state}:{$city}:Leaderboard");

        $this->user->update([
            'show_name' => false,
            'show_username' => false
        ]);

        // User tags the image
        $this->actingAs($this->user);

        $this->post('/add-tags', [
            'photo_id' => $this->photo->id,
            'presence' => true,
            'tags' => [
                'smoking' => [
                    'butts' => 3
                ]
            ]
        ]);

        $this->assertNull(Redis::zscore("{$country}:Leaderboard", $this->user->id));
        $this->assertNull(Redis::zscore("{$country}:{$state}:Leaderboard", $this->user->id));
        $this->assertNull(Redis::zscore("{$country}:{$state}:{$city}:Leaderboard", $this->user->id));

        // Admin deletes the photo -------------------
        $this->actingAs($this->admin);

        $this->post('/admin/destroy', ['photoId' => $this->photo->id]);

        // Assert leaderboards are not updated ------------
        $this->assertNull(Redis::zscore("{$country}:Leaderboard", $this->user->id));
        $this->assertNull(Redis::zscore("{$country}:{$state}:Leaderboard", $this->user->id));
        $this->assertNull(Redis::zscore("{$country}:{$state}:{$city}:Leaderboard", $this->user->id));
    }

    public function test_unauthorized_users_cannot_delete_photos()
    {
        // Unauthenticated users ---------------------
        $response = $this->post('/admin/destroy', ['photoId' => 1]);

        $response->assertRedirect('/');

        // A non-admin user tries to delete the photo ------------
        $anotherUser = User::factory()->create();

        $this->actingAs($anotherUser);

        $response = $this->post('/admin/destroy', ['photoId' => $this->photo->id]);

        $response->assertRedirect('/');

        $this->assertInstanceOf(Photo::class, $this->photo->fresh());
    }

    public function test_it_throws_not_found_exception_if_photo_doesnt_exist()
    {
        $this->actingAs($this->admin);

        $response = $this->post('/admin/destroy', ['photoId' => 0]);

        $response->assertNotFound();
    }
}
