<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(Castle\User::class, function (Faker\Generator $faker) {
	return [
		'name' => $faker->name,
		'email' => $faker->unique()->safeEmail,
		'phone' => $faker->phoneNumber,
		'password' => bcrypt(str_random(mt_rand(8, 16)))
	];
});

$factory->define(Castle\Document::class, function (Faker\Generator $faker) {
	$name = $faker->unique()->sentence;
	$fields = mt_rand(1, 4);
	return [
		'name' => $name,
		'slug' => str_slug($name),
		'content' => $faker->paragraphs(mt_rand(4, 8), true),
		'metadata' => mt_rand(0, 10) % 3 == 0 ? array_combine($faker->words($fields), $faker->sentences($fields)) : [],
		'attachments' => mt_rand(0, 10) % 3 == 0 ? [str_slug($faker->words(mt_rand(1, 4), true)).'.'.$faker->fileExtension] : [],
	];
});

$factory->define(Castle\Tag::class, function (Faker\Generator $faker) {
	$name = $faker->unique()->words(mt_rand(1, 4), true);
	return [
		'name' => $name,
		'slug' => str_slug($name),
		'description' => $faker->sentence,
		'color' => $faker->hexColor,
	];
});

$factory->define(Castle\Client::class, function (Faker\Generator $faker) {
	$company = $faker->unique()->company;
	return [
		'name' => $company,
		'slug' => strtolower(preg_replace('/[^A-Z0-9]+/', '', $company)),
		'color' => $faker->hexColor,
		'description' => $faker->paragraphs(2, true),
	];
});

$factory->define(Castle\ResourceType::class, function (Faker\Generator $faker) {
	$word = $faker->unique()->word;
	return [
		'slug' => str_slug($word),
		'name' => $word
	];
});

$factory->define(Castle\Resource::class, function (Faker\Generator $faker) {
	$name = $faker->unique()->words(mt_rand(1, 4), true);
	$fields = mt_rand(1, 8);
	return [
		'name' => $name,
		'slug' => str_slug($name),
		'metadata' => array_combine($faker->words($fields), $faker->sentences($fields)),
		'attachments' => mt_rand(0, 10) % 3 == 0 ? [str_slug($faker->words(mt_rand(1, 4), true)).'.'.$faker->fileExtension] : [],
	];
});

$factory->define(Castle\Discussion::class, function (Faker\Generator $faker) {
	$name = $faker->unique()->sentence;
	return [
		'name' => $name,
		'slug' => str_slug($name),
		'content' => $faker->paragraphs(mt_rand(4, 8), true),
		'attachments' => mt_rand(0, 10) % 3 == 0 ? [str_slug($faker->words(mt_rand(1, 4), true)).'.'.$faker->fileExtension] : [],
	];
});

$factory->define(Castle\Comment::class, function (Faker\Generator $faker) {
	return [
		'content' => $faker->sentences(mt_rand(1, 3), true),
	];
});

$factory->define(Castle\Vote::class, function (Faker\Generator $faker) {
	return [
		'value' => mt_rand(1, 3) == 1 ? -1 : 1,
	];
});
