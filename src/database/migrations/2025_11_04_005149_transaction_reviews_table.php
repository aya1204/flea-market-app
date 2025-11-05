<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TransactionReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade'); // レビューしたユーザー（出品者 or 購入者）
            $table->foreignId('reviewee_id')->constrained('users')->onDelete('cascade'); // レビューされたユーザー（出品者 or 購入者）
            $table->tinyInteger('rating'); // 星1～5
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
        Schema::dropIfExists('transaction_reviews');
    }
}
