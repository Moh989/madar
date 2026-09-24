<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->boolean('is_admin')->default(false));
        Schema::create('media', function (Blueprint $t) {
            $t->id();
            $t->string('path');
            $t->json('alt');
            $t->string('source')->nullable();
            $t->string('license')->nullable();
            $t->unsignedInteger('width');
            $t->unsignedInteger('height');
            $t->timestamps();
        });
        foreach (['pages', 'sectors', 'projects', 'news', 'slides'] as $table) {
            Schema::create($table, function (Blueprint $t) use ($table) {
                $t->id();
                $t->string('slug')->unique();
                $t->json('title');
                $t->json('excerpt')->nullable();
                $t->json('body')->nullable();
                $t->json('seo_title')->nullable();
                $t->json('seo_description')->nullable();
                $t->string('image')->nullable();
                $t->json('alt')->nullable();
                $t->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
                $t->string('status')->default('draft')->index();
                $t->unsignedInteger('sort_order')->default(0);
                $t->boolean('visible')->default(true);
                $t->timestamps();
                if ($table === 'sectors') {
                    $t->json('services')->nullable();
                }
                if ($table === 'projects') {
                    $t->foreignId('sector_id')->nullable()->constrained('sectors')->nullOnDelete();
                    $t->json('gallery')->nullable();
                }
                if ($table === 'news') {
                    $t->date('published_at')->nullable();
                }
                if ($table === 'slides') {
                    $t->string('link')->nullable();
                    $t->json('button')->nullable();
                }
            });
        }
        Schema::create('inquiries', function (Blueprint $t) {
            $t->id();
            $t->string('reference')->unique();
            $t->string('type');
            $t->string('name');
            $t->string('company')->nullable();
            $t->string('contact');
            $t->foreignId('sector_id')->nullable()->constrained('sectors')->nullOnDelete();
            $t->text('details');
            $t->string('origin')->nullable();
            $t->string('destination')->nullable();
            $t->string('cargo')->nullable();
            $t->string('weight')->nullable();
            $t->string('locale', 2);
            $t->string('status')->default('new')->index();
            $t->string('notification_status')->default('pending');
            $t->text('notes')->nullable();
            $t->timestamps();
        });
        Schema::create('settings', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->json('value')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['settings', 'inquiries', 'slides', 'news', 'projects', 'sectors', 'pages', 'media'] as $t) {
            Schema::dropIfExists($t);
        }Schema::table('users', fn (Blueprint $t) => $t->dropColumn('is_admin'));
    }
};
