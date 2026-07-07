<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        $authors = [
            [
                'name' => 'Humayun Ahmed',
                'biography' => 'Eminent Bangladeshi writer, dramatist, and filmmaker. One of the most popular authors in Bengali literature.',
            ],
            [
                'name' => 'Muhammed Zafar Iqbal',
                'biography' => 'Bangladeshi author, physicist, and educator known for his science fiction and children\'s literature.',
            ],
            [
                'name' => 'J.K. Rowling',
                'biography' => 'British author best known for the Harry Potter fantasy series.',
            ],
            [
                'name' => 'Stephen King',
                'biography' => 'American author of horror, supernatural fiction, suspense, and fantasy novels.',
            ],
            [
                'name' => 'Dan Brown',
                'biography' => 'American author of thriller novels, best known for the Robert Langdon series.',
            ],
        ];

        foreach ($authors as $author) {
            Author::firstOrCreate(
                ['slug' => Str::slug($author['name'])],
                [
                    'name' => $author['name'],
                    'slug' => Str::slug($author['name']),
                    'biography' => $author['biography'],
                    'is_active' => true,
                ]
            );
        }
    }
}
