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
