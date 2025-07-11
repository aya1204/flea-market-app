<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $createdItems = [];
        $items = [
            // 腕時計
            [
                'title' => '腕時計',
                'price' => '15000',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'images/Armani+Mens+Clock.jpg',
                'condition_id' => '1',
                'categories' => [1, 5, 12],
            ],

            // HDD
            [
                'title' => 'HDD',
                'price' => '5000',
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'images/HDD+Hard+Disk.jpg',
                'condition_id' => '2',
                'categories' => [2, 8],
            ],

            // 玉ねぎ3束
            [
                'title' => '玉ねぎ3束',
                'price' => '300',
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'images/iLoveIMG+d.jpg',
                'condition_id' => '3',
                'categories' => [10],
            ],

            // 革靴
            [
                'title' => '革靴',
                'price' => '4000',
                'description' => 'クラシックなデザインの革靴',
                'image' => 'images/Leather+Shoes+Product+Photo.jpg',
                'condition_id' => '4',
                'categories' => [1, 5],
            ],

            // ノートPC
            [
                'title' => 'ノートPC',
                'price' => '45000',
                'description' => '高性能なノートパソコン',
                'image' => 'images/Living+Room+Laptop.jpg',
                'condition_id' => '1',
                'categories' => [2, 8],
            ],

            // マイク
            [
                'title' => 'マイク',
                'price' => '8000',
                'description' => '高音質のレコーディング用マイク',
                'image' => 'images/Music+Mic+4632231.jpg',
                'condition_id' => '2',
                'categories' => [2],
            ],

            // ショルダーバッグ
            [
                'title' => 'ショルダーバッグ',
                'price' => '3500',
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'images/Purse+fashion+pocket.jpg',
                'condition_id' => '3',
                'categories' => [1, 4, 12],
            ],

            // タンブラー
            [
                'title' => 'タンブラー',
                'price' => '500',
                'description' => '使いやすいタンブラー',
                'image' => 'images/Tumbler+souvenir.jpg',
                'condition_id' => '4',
                'categories' => [10],
            ],

            // コーヒーミル
            [
                'title' => 'コーヒーミル',
                'price' => '4000',
                'description' => '手動のコーヒーミル',
                'image' => 'images/Waitress+with+Coffee+Grinder.jpg',
                'condition_id' => '1',
                'categories' => [10],
            ],

            // メイクセット
            [
                'title' => 'メイクセット',
                'price' => '2500',
                'description' => '便利なメイクアップセット',
                'image' => 'images/外出メイクアップセット.jpg',
                'condition_id' => '2',
                'categories' => [1, 4, 6],
            ],
        ];

        // ①商品作成
        foreach ($items as $data) {
            // 画像パスを取得
            $imagePath = $data['image'];
            // // 画像が存在するか確認し、なければデフォルト画像を使用
            $image = file_exists(storage_path('app/public/' . $imagePath))
                ? $imagePath
                : 'images/default.png';

            // 出品者をランダムに決める
            $sellerId = rand(1, 3);

            $item = Item::create([
                'title' => $data['title'],
                'price' => $data['price'],
                'description' => $data['description'],
                'image' => $data['image'],
                'condition_id' => $data['condition_id'],
                'seller_user_id' => $sellerId,
            ]);

            // 多対多のリレーションでカテゴリを紐付け
            $item->categories()->attach($data['categories']);
            $createdItems[] = $item;
        }

        // ②購入処理(ユーザー1〜3が自分の出品していない商品を1つずつ購入)
        foreach ([1, 2, 3] as $purchaseUserId) {
            $available = collect($createdItems)->filter(fn($item) => $item->purchase_user_id === null && $item->seller_user_id !== $purchaseUserId)->values();

            if ($available->isNotEmpty()) {
                $itemToBuy = $available->shift();
                $itemToBuy->update(['purchase_user_id' => $purchaseUserId]);
            }
        }
    }
}
