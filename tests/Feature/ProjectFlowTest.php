<?php

namespace Tests\Feature;

use App\Models\PriceReference;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/projects')->assertRedirect('/login');
    }

    public function test_user_can_log_in_and_reach_dashboard(): void
    {
        $user = User::factory()->create(['password' => 'rahasia123']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'rahasia123',
        ])->assertRedirect(route('projects.index'));

        $this->assertAuthenticatedAs($user);
        $this->get('/projects')->assertOk();
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => 'rahasia123']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'salah',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_repeated_failures(): void
    {
        $user = User::factory()->create(['password' => 'rahasia123']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'salah']);
        }

        $this->post('/login', ['email' => $user->email, 'password' => 'rahasia123'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertStringContainsString(
            'Terlalu banyak percobaan',
            session('errors')->first('email'),
        );
    }

    public function test_user_can_change_their_own_password(): void
    {
        $user = User::factory()->create(['password' => 'rahasia123']);

        $this->actingAs($user)->put(route('account.password'), [
            'current_password' => 'rahasia123',
            'password' => 'sandibaru456',
            'password_confirmation' => 'sandibaru456',
        ])->assertRedirect(route('account.edit'));

        $this->assertTrue(Hash::check('sandibaru456', $user->fresh()->password));
    }

    public function test_password_change_requires_the_correct_current_password(): void
    {
        $user = User::factory()->create(['password' => 'rahasia123']);

        $this->actingAs($user)->put(route('account.password'), [
            'current_password' => 'tebakan',
            'password' => 'sandibaru456',
            'password_confirmation' => 'sandibaru456',
        ])->assertSessionHasErrors('current_password', null, 'password');

        $this->assertTrue(Hash::check('rahasia123', $user->fresh()->password));
    }

    public function test_password_change_requires_matching_confirmation(): void
    {
        $user = User::factory()->create(['password' => 'rahasia123']);

        $this->actingAs($user)->put(route('account.password'), [
            'current_password' => 'rahasia123',
            'password' => 'sandibaru456',
            'password_confirmation' => 'beda789',
        ])->assertSessionHasErrors('password', null, 'password');

        $this->assertTrue(Hash::check('rahasia123', $user->fresh()->password));
    }

    public function test_user_can_change_their_name_and_email(): void
    {
        $user = User::factory()->create(['email' => 'adek@datalaila.test']);

        $this->actingAs($user)->put(route('account.profile'), [
            'name' => 'Adek Estimator',
            'email' => 'adek.asli@gmail.com',
        ])->assertRedirect(route('account.edit'));

        $user->refresh();
        $this->assertSame('Adek Estimator', $user->name);
        $this->assertSame('adek.asli@gmail.com', $user->email);

        $this->post('/logout');
        $this->post('/login', ['email' => 'adek.asli@gmail.com', 'password' => 'password'])
            ->assertRedirect(route('projects.index'));
    }

    public function test_email_must_be_valid_and_unique(): void
    {
        $other = User::factory()->create(['email' => 'dipakai@gmail.com']);
        $user = User::factory()->create(['email' => 'adek@datalaila.test']);

        $this->actingAs($user)->put(route('account.profile'), [
            'name' => 'Adek',
            'email' => 'bukan-email',
        ])->assertSessionHasErrors('email', null, 'profile');

        $this->actingAs($user)->put(route('account.profile'), [
            'name' => 'Adek',
            'email' => $other->email,
        ])->assertSessionHasErrors('email', null, 'profile');

        $this->assertSame('adek@datalaila.test', $user->fresh()->email);
    }

    public function test_keeping_the_same_email_is_allowed(): void
    {
        $user = User::factory()->create(['email' => 'adek@datalaila.test']);

        $this->actingAs($user)->put(route('account.profile'), [
            'name' => 'Nama Baru',
            'email' => 'adek@datalaila.test',
        ])->assertRedirect(route('account.edit'));

        $this->assertSame('Nama Baru', $user->fresh()->name);
    }

    public function test_user_can_create_and_view_a_project(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/projects', [
            'name' => 'Rumah Bu Sari',
            'deadline' => '2026-10-01',
            'status' => 'berjalan',
        ])->assertRedirect();

        $project = Project::firstWhere('name', 'Rumah Bu Sari');

        $this->assertSame($user->id, $project->user_id);
        $this->actingAs($user)
            ->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('Rumah Bu Sari')
            ->assertSee('Berjalan');
    }

    public function test_dashboard_filters_by_search_and_status(): void
    {
        $user = User::factory()->create();
        $user->projects()->create(['name' => 'Rumah Bu Sari', 'status' => 'berjalan']);
        $user->projects()->create(['name' => 'Ruko Pak Budi', 'status' => 'selesai']);

        $this->actingAs($user)->get('/projects?q=Ruko')
            ->assertSee('Ruko Pak Budi')
            ->assertDontSee('Rumah Bu Sari');

        $this->actingAs($user)->get('/projects?status=selesai')
            ->assertSee('Ruko Pak Budi')
            ->assertDontSee('Rumah Bu Sari');
    }

    public function test_uploaded_files_are_categorised_automatically(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $project = $user->projects()->create(['name' => 'Rumah Bu Sari', 'status' => 'berjalan']);

        $this->actingAs($user)->post(route('projects.files.store', $project), [
            'files' => [
                UploadedFile::fake()->create('BQ Rumah.xlsx', 12),
                UploadedFile::fake()->create('Denah.dwg', 20),
                UploadedFile::fake()->create('Model.skp', 30),
                UploadedFile::fake()->create('Kontrak.pdf', 8),
                UploadedFile::fake()->create('Lokasi.jpg', 40),
                UploadedFile::fake()->create('Tampak.png', 40),
                UploadedFile::fake()->create('Material.webp', 15),
                UploadedFile::fake()->create('Penawaran.docx', 18),
                UploadedFile::fake()->create('Catatan lama.doc', 10),
            ],
        ])->assertRedirect();

        $this->assertSame([
            'BQ Rumah.xlsx' => 'bq_excel',
            'Denah.dwg' => '2d',
            'Model.skp' => '3d',
            'Kontrak.pdf' => 'pdf',
            'Lokasi.jpg' => 'foto',
            'Tampak.png' => 'foto',
            'Material.webp' => 'foto',
            'Penawaran.docx' => 'word',
            'Catatan lama.doc' => 'word',
        ], $project->files()->pluck('category', 'original_name')->all());

        foreach ($project->files as $file) {
            Storage::disk('local')->assertExists($file->file_path);
        }
    }

    public function test_unsupported_file_extension_is_rejected(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $project = $user->projects()->create(['name' => 'Rumah Bu Sari', 'status' => 'berjalan']);

        $this->actingAs($user)->post(route('projects.files.store', $project), [
            'files' => [UploadedFile::fake()->create('virus.exe', 5)],
        ])->assertSessionHasErrors('files.0');

        $this->assertSame(0, $project->files()->count());
    }

    public function test_user_can_download_a_file_with_its_original_name(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $project = $user->projects()->create(['name' => 'Rumah Bu Sari', 'status' => 'berjalan']);

        $this->actingAs($user)->post(route('projects.files.store', $project), [
            'files' => [UploadedFile::fake()->create('BQ Rumah.xlsx', 12)],
        ]);

        $this->actingAs($user)
            ->get(route('files.download', $project->files()->first()))
            ->assertOk()
            ->assertDownload('BQ Rumah.xlsx');
    }

    public function test_photo_is_served_inline_for_preview(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $project = $user->projects()->create(['name' => 'Rumah Bu Sari', 'status' => 'berjalan']);

        $this->actingAs($user)->post(route('projects.files.store', $project), [
            'files' => [UploadedFile::fake()->image('Lokasi.jpg')],
        ]);

        $file = $project->files()->first();

        $this->actingAs($user)->get(route('files.raw', $file))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg')
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->actingAs($user)->get(route('files.preview', $file))
            ->assertOk()
            ->assertSee(route('files.raw', $file));
    }

    public function test_spreadsheet_preview_renders_cell_values(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $project = $user->projects()->create(['name' => 'Rumah Bu Sari', 'status' => 'berjalan']);

        $this->actingAs($user)->post(route('projects.files.store', $project), [
            'files' => [UploadedFile::fake()->createWithContent(
                'BQ.csv',
                "Uraian,Volume,Satuan\nPasangan bata,120,m2\n",
            )],
        ]);

        $this->actingAs($user)
            ->get(route('files.preview', $project->files()->first()))
            ->assertOk()
            ->assertSee('Uraian')
            ->assertSee('Pasangan bata')
            ->assertSee('120');
    }

    public function test_cad_file_has_no_preview_and_is_offered_for_download(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $project = $user->projects()->create(['name' => 'Rumah Bu Sari', 'status' => 'berjalan']);

        $this->actingAs($user)->post(route('projects.files.store', $project), [
            'files' => [UploadedFile::fake()->create('Denah.dwg', 20)],
        ]);

        $file = $project->files()->first();

        $this->assertNull($file->preview_kind);
        $this->actingAs($user)->get(route('files.raw', $file))->assertNotFound();
        $this->actingAs($user)->get(route('files.preview', $file))
            ->assertOk()
            ->assertSee('tidak bisa ditampilkan di browser');
    }

    public function test_preview_is_blocked_for_another_users_file(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $project = $owner->projects()->create(['name' => 'Rumah Bu Sari', 'status' => 'berjalan']);

        $this->actingAs($owner)->post(route('projects.files.store', $project), [
            'files' => [UploadedFile::fake()->image('Lokasi.jpg')],
        ]);

        $file = $project->files()->first();

        $this->actingAs($intruder)->get(route('files.raw', $file))->assertForbidden();
        $this->actingAs($intruder)->get(route('files.preview', $file))->assertForbidden();
    }

    public function test_price_list_is_shared_across_projects_not_tied_to_one(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('prices.store'), [
            'name' => 'Keramik 60x60', 'price' => 150000,
        ])->assertRedirect(route('prices.index'));

        $this->actingAs($user)
            ->get(route('prices.index'))
            ->assertOk()
            ->assertSee('Keramik 60x60')
            ->assertSee('Rp150.000');

        $this->assertSame($user->id, PriceReference::first()->user_id);
    }

    public function test_price_list_can_be_searched(): void
    {
        $user = User::factory()->create();
        $user->priceReferences()->create(['name' => 'Keramik 60x60', 'price' => 150000]);
        $user->priceReferences()->create(['name' => 'Semen Tiga Roda', 'price' => 65000]);

        $this->actingAs($user)->get(route('prices.index', ['q' => 'semen']))
            ->assertSee('Semen Tiga Roda')
            ->assertDontSee('Keramik 60x60');
    }

    public function test_price_entries_can_be_updated_and_deleted(): void
    {
        $user = User::factory()->create();
        $price = $user->priceReferences()->create(['name' => 'Keramik 60x60', 'price' => 150000]);

        $this->actingAs($user)->put(route('prices.update', $price), [
            'name' => 'Keramik 60x60 Roman', 'price' => 165000,
        ])->assertRedirect();

        $this->assertSame('Keramik 60x60 Roman', $price->fresh()->name);

        $this->actingAs($user)->delete(route('prices.destroy', $price))->assertRedirect();
        $this->assertDatabaseCount('price_references', 0);
    }

    public function test_user_cannot_touch_another_users_price_entry(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $price = $owner->priceReferences()->create(['name' => 'Keramik', 'price' => 1]);

        $this->actingAs($intruder)->put(route('prices.update', $price), [
            'name' => 'Diubah', 'price' => 2,
        ])->assertForbidden();
        $this->actingAs($intruder)->delete(route('prices.destroy', $price))->assertForbidden();

        $this->assertSame('Keramik', $price->fresh()->name);
    }

    public function test_deleting_a_project_keeps_the_price_list(): void
    {
        $user = User::factory()->create();
        $project = $user->projects()->create(['name' => 'Rumah Bu Sari', 'status' => 'berjalan']);
        $user->priceReferences()->create(['name' => 'Keramik 60x60', 'price' => 150000]);

        $this->actingAs($user)->delete(route('projects.destroy', $project))->assertRedirect();

        $this->assertDatabaseCount('projects', 0);
        $this->assertDatabaseCount('price_references', 1);
    }

    public function test_user_cannot_touch_another_users_project(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $project = $owner->projects()->create(['name' => 'Rumah Bu Sari', 'status' => 'berjalan']);

        $this->actingAs($intruder)->get(route('projects.show', $project))->assertForbidden();
        $this->actingAs($intruder)->get(route('projects.edit', $project))->assertForbidden();
        $this->actingAs($intruder)->delete(route('projects.destroy', $project))->assertForbidden();
        $this->actingAs($intruder)->post(route('projects.files.store', $project), [
            'files' => [UploadedFile::fake()->create('BQ.xlsx', 5)],
        ])->assertForbidden();
    }

    public function test_deleting_a_project_removes_its_files(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $project = $user->projects()->create(['name' => 'Rumah Bu Sari', 'status' => 'berjalan']);

        $this->actingAs($user)->post(route('projects.files.store', $project), [
            'files' => [UploadedFile::fake()->create('BQ Rumah.xlsx', 12)],
        ]);

        $this->actingAs($user)->delete(route('projects.destroy', $project))->assertRedirect();

        $this->assertDatabaseCount('projects', 0);
        $this->assertDatabaseCount('project_files', 0);
        Storage::disk('local')->assertDirectoryEmpty('projects');
    }
}
