<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 日本のデータを追加
        Country::create([
            'name' => '日本',
            'name_en' => 'Japan',
            'code' => 'JPN',
            'continent' => 'Asia',
            'population' => 125800000,
            'capital' => '東京',
            'languages' => json_encode(['日本語']),
            'currency' => '日本円 (JPY)',
            'description' => '日本は東アジアに位置する島国で、4つの主要な島（本州、北海道、九州、四国）と多くの小さな島々から構成されています。豊かな文化、伝統、そして現代技術の融合が特徴的です。',
            'background_image' => 'country-backgrounds/japan-background.jpg',
            'latitude' => 36.2048,
            'longitude' => 138.2529,
        ]);

        // 他の主要国も追加
        Country::create([
            'name' => 'アメリカ合衆国',
            'name_en' => 'United States',
            'code' => 'USA',
            'continent' => 'North America',
            'population' => 331900000,
            'capital' => 'ワシントンD.C.',
            'languages' => json_encode(['英語']),
            'currency' => 'アメリカドル (USD)',
            'description' => '北アメリカ大陸に位置する連邦共和国。50の州とワシントンD.C.から構成され、世界最大の経済大国の一つです。',
            'background_image' => 'usa-background.jpg',
            'latitude' => 39.8283,
            'longitude' => -98.5795,
        ]);

        Country::create([
            'name' => 'イギリス',
            'name_en' => 'United Kingdom',
            'code' => 'GBR',
            'continent' => 'Europe',
            'population' => 67000000,
            'capital' => 'ロンドン',
            'languages' => json_encode(['英語']),
            'currency' => 'ポンド (GBP)',
            'description' => 'グレートブリテン島とアイルランド島の一部から構成される立憲君主制国家。豊かな歴史と文化を持つ国です。',
            'background_image' => 'uk-background.jpg',
            'latitude' => 55.3781,
            'longitude' => -3.4360,
        ]);

        Country::create([
            'name' => 'フランス',
            'name_en' => 'France',
            'code' => 'FRA',
            'continent' => 'Europe',
            'population' => 67000000,
            'capital' => 'パリ',
            'languages' => json_encode(['フランス語']),
            'currency' => 'ユーロ (EUR)',
            'description' => '西ヨーロッパに位置する共和国。芸術、文化、美食で世界的に有名で、多くの観光地を有しています。',
            'background_image' => 'france-background.jpg',
            'latitude' => 46.2276,
            'longitude' => 2.2137,
        ]);

        Country::create([
            'name' => 'ドイツ',
            'name_en' => 'Germany',
            'code' => 'DEU',
            'continent' => 'Europe',
            'population' => 83000000,
            'capital' => 'ベルリン',
            'languages' => json_encode(['ドイツ語']),
            'currency' => 'ユーロ (EUR)',
            'description' => '中央ヨーロッパに位置する連邦共和国。ヨーロッパ最大の経済大国の一つで、豊かな歴史と文化を持ちます。',
            'background_image' => 'germany-background.jpg',
            'latitude' => 51.1657,
            'longitude' => 10.4515,
        ]);
    }
}