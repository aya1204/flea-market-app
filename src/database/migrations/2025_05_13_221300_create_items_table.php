<!-- itemsテーブル -->
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            // 出品者
            $table->foreignId('seller_user_id')->constrained('users')->OnDelete('cascade');
            // 購入者
            $table->foreignId('purchase_user_id')->nullable()->constrained('users');
            $table->foreignId('condition_id')->constrained('conditions')->OnDelete('cascade');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->OnDelete('cascade');
            $table->foreignId('paymentmethod_id')->nullable()->constrained('paymentmethods')->OnDelete('cascade');
            $table->string('image');
            $table->string('title');
            $table->text('description');
            $table->integer('price');
            $table->boolean('is_sold')->default(false);
            $table->string('postal_code')->nullable(); // 購入時郵便番号
            $table->string('address')->nullable();     // 購入時住所
            $table->string('building')->nullable();    // 購入時建物名
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
