<?php

namespace Tests\Feature;

use App\Models\TourCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTourCategoryImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_category_with_uploaded_images(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin())->post(
            route('admin.tour-categories.store'),
            $this->categoryPayload([
                'image' => UploadedFile::fake()->image('category.jpg', 1200, 800),
                'og_image' => UploadedFile::fake()->image('category-og.png', 1200, 630),
            ])
        );

        $category = TourCategory::firstOrFail();

        $response->assertRedirect(
            route('admin.tour-categories.edit', $category)
        );
        Storage::disk('public')->assertExists($category->image);
        Storage::disk('public')->assertExists($category->og_image);
    }

    public function test_replacing_an_image_removes_the_previous_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('tour-categories/old-image.jpg', 'old');

        $category = TourCategory::create([
            'name' => 'Existing Category',
            'slug' => 'existing-category',
            'image' => 'tour-categories/old-image.jpg',
            'status' => TourCategory::STATUS_DRAFT,
            'created_by' => $this->admin()->id,
        ]);

        $response = $this->actingAs($this->admin())->put(
            route('admin.tour-categories.update', $category),
            $this->categoryPayload([
                'name' => $category->name,
                'image' => UploadedFile::fake()->image('replacement.webp', 1000, 700),
            ])
        );

        $category->refresh();

        $response->assertRedirect(
            route('admin.tour-categories.edit', $category)
        );
        $this->assertNotSame('tour-categories/old-image.jpg', $category->image);
        Storage::disk('public')->assertMissing('tour-categories/old-image.jpg');
        Storage::disk('public')->assertExists($category->image);
    }

    public function test_admin_can_remove_a_category_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('tour-categories/image-to-remove.jpg', 'image');

        $category = TourCategory::create([
            'name' => 'Category With Image',
            'slug' => 'category-with-image',
            'image' => 'tour-categories/image-to-remove.jpg',
            'status' => TourCategory::STATUS_DRAFT,
            'created_by' => $this->admin()->id,
        ]);

        $response = $this->actingAs($this->admin())->put(
            route('admin.tour-categories.update', $category),
            $this->categoryPayload([
                'name' => $category->name,
                'remove_image' => true,
            ])
        );

        $category->refresh();

        $response->assertRedirect(
            route('admin.tour-categories.edit', $category)
        );
        $this->assertNull($category->image);
        Storage::disk('public')->assertMissing('tour-categories/image-to-remove.jpg');
    }

    public function test_category_images_must_be_valid_image_files(): void
    {
        $response = $this->actingAs($this->admin())->from(
            route('admin.tour-categories.create')
        )->post(route('admin.tour-categories.store'), $this->categoryPayload([
            'image' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
        ]));

        $response->assertRedirect(route('admin.tour-categories.create'));
        $response->assertSessionHasErrors('image');
        $this->assertDatabaseCount('tour_categories', 0);
    }

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'status' => true,
        ]);
    }

    private function categoryPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Adventure Tours',
            'description' => 'Small-group travel experiences.',
            'status' => TourCategory::STATUS_DRAFT,
            'sort_order' => 0,
        ], $overrides);
    }
}
